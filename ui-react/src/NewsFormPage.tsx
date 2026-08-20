import { useState, useEffect, useCallback, useMemo, useRef } from 'react'
import {
  Save, Loader2, ChevronDown, ChevronUp,
  Plus, X, Eye, Calendar, Globe, Tag, Search, SlidersHorizontal,
  GitBranch, Image, Paperclip, GripVertical, FolderTree, User,
  MessageSquare, Check, Ban, Trash2, ChevronLeft, ChevronRight,
} from 'lucide-react'

import { Button } from './components/ui/button'
import { Input } from './components/ui/input'
// Choix d'éditeur TipTap/TinyMCE désactivé : on ne garde QUE TinyMCE (config 'tool' de melis-core,
// idem « Emails management »). L'import TipTap est conservé en commentaire au cas où.
// import { RichEditor, type RichEditorEngine } from './components/ui/rich-editor'
import { MelisToolEditor } from './components/ui/melis-tool-editor'
import { CalendarPopup } from './components/ui/date-time-picker'
import { PreviewTab } from './components/PreviewTab'
import { cn } from './lib/utils'
import { t } from './lib/i18n'
import * as newsApi from './lib/news-api'
import { useCaps } from './shared/useCaps'
import { useIsNarrow } from './shared/useIsNarrow'
import { FormErrorBanner, koNotify, okNotify, type FormIssue } from './shared/melis-form-errors'
import { ConfirmDialog } from './shared/confirm-dialog'

// News tool capability key — must match config/react.capabilities.php, i.e. the melisKey of the
// rights-bearing menu node. NOT `meliscmsnews_left_menu`: that is the type-link target and stays
// the renderable zone key.
const NEWS_CAPS_KEY = 'meliscmsnews_tools_section'

// Host API (exposed by MelisCore at runtime). Optional — guarded at call sites.
declare global {
  interface Window {
    /** Extension point: optional modules (e.g. melis-ai-community-extensions) register
     *  extra actions to render in each paragraph's header bar. */
    __melisNewsExtensions?: {
      renderParagraphActions?: (
        paraIdx: number,
        onContent: (text: string) => void,
        context: { newsId?: string; title?: string; subtitle?: string; paragraphs?: string[]; images?: string[] }
      ) => React.ReactNode
    }
  }
}

// ─── Types ─────────────────────────────────────────────────────────────────────

type Status = '0' | '1'

interface FormState {
  title: string
  subtitle: string
  paragraphs: string[]   // maps to cnews_paragraph1-10
  status: Status
  siteId: string
  publishDate: string
  unpublishDate: string
  sliderId: string
  authorId: string
  validateComments: boolean
  categoryIds: number[]
  tagIds: number[]
  seo: newsApi.NewsSeo
  image1: string | null
  image2: string | null
  image3: string | null
  document1: string | null
  document2: string | null
  document3: string | null
}

const EMPTY_SEO: newsApi.NewsSeo = {
  url: '', urlRedirect: '', url301: '', metaTitle: '', metaDescription: '', canonical: '',
}

const EMPTY: FormState = {
  title: '', subtitle: '', paragraphs: [''],
  status: '0', siteId: '', publishDate: '', unpublishDate: '',
  sliderId: '', authorId: '', validateComments: false, categoryIds: [], tagIds: [], seo: EMPTY_SEO,
  image1: null, image2: null, image3: null,
  document1: null, document2: null, document3: null,
}

const MAX_PARAGRAPHS = 10  // cnews_paragraph1-10 in DB

// ─── Textes PAR LANGUE ─────────────────────────────────────────────────────────
// L'actualité est stockée en deux morceaux : `melis_cms_news` (une ligne — statut, site, dates,
// médias, slider, catégories, tags : INDÉPENDANT de la langue) et `melis_cms_news_texts`
// (UNE LIGNE PAR LANGUE — titre, sous-titre, paragraphes, SEO). Le formulaire reflète ce
// découpage : `form` porte les champs indépendants de la langue + les textes de la langue
// AFFICHÉE, et `langTexts` garde en mémoire les textes des AUTRES langues déjà saisies/chargées.
// Le bouton de langue échange donc vraiment le contenu (avant : un seul jeu de textes, donc la
// même saisie sous chaque drapeau), et l'enregistrement écrit toutes les traductions en un appel
// — comme le formulaire legacy, qui poste les N blocs de langue d'un coup.
type LangText = Pick<FormState, 'title' | 'subtitle' | 'paragraphs' | 'seo'>

const EMPTY_TEXT: LangText = { title: '', subtitle: '', paragraphs: [''], seo: EMPTY_SEO }

const pickText = (f: FormState): LangText => ({
  title: f.title, subtitle: f.subtitle, paragraphs: f.paragraphs, seo: f.seo,
})

/** Une traduction est « saisie » dès qu'un de ses champs porte du contenu. */
const hasText = (x: LangText): boolean =>
  !!(x.title.trim() || x.subtitle.trim() || x.paragraphs.some((p) => p.trim())
     || Object.values(x.seo).some((v) => String(v ?? '').trim()))

// La valeur BDD ("YYYY-MM-DD HH:MM:SS") et la valeur <input datetime-local> ("YYYY-MM-DDTHH:MM")
// sont des heures MURALES (sans fuseau). On les manipule en chaînes — JAMAIS via new Date().toISOString()
// (qui applique un décalage de fuseau et fait dériver la date à chaque save/load, indépendamment de la langue).
function toInputDate(d: string | null | undefined): string {
  if (!d) return ''
  const m = String(d).match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/)
  return m ? `${m[1]}-${m[2]}-${m[3]}T${m[4]}:${m[5]}` : ''
}
// "YYYY-MM-DDTHH:MM" → "YYYY-MM-DD HH:MM:SS" (pour la BDD), sans conversion de fuseau.
function toDbDate(v: string): string | null {
  const m = v && v.match(/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2})$/)
  return m ? `${m[1]} ${m[2]}:00` : null
}
// Aperçu lisible dans la langue active (fr → "12 juillet 2026 à 14:30", en → "12 July 2026 at 14:30").
function formatDatePreview(v: string, lang: string): string {
  const m = v && v.match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/)
  if (!m) return ''
  const dt = new Date(+m[1], +m[2] - 1, +m[3], +m[4], +m[5])
  return dt.toLocaleString(lang === 'fr' ? 'fr-FR' : 'en-GB', { dateStyle: 'long', timeStyle: 'short' })
}

// ─── Champ date+heure localisé (langue du BO, dynamique) ─────────────────────────
// `<input type="datetime-local">` s'affiche dans la locale du NAVIGATEUR (ignore l'attribut
// `lang` sous Chromium) → un BO FR sur navigateur EN montrerait mm/dd/yyyy. Ce champ affiche/édite
// dans le format de la LANGUE DU BO tout en stockant la valeur interne "YYYY-MM-DDTHH:MM", et garde
// un calendrier natif (input datetime-local caché + showPicker()). L'ordre jour/mois est déduit de
// la locale via Intl → fonctionne pour N'IMPORTE QUELLE langue du BO (dynamique), pas seulement fr/en.
function localeDayFirst(locale: string): boolean {
  try {
    const parts = new Intl.DateTimeFormat(locale || 'en').formatToParts(new Date(2000, 0, 2))
    const di = parts.findIndex((p) => p.type === 'day')
    const mi = parts.findIndex((p) => p.type === 'month')
    return di !== -1 && mi !== -1 && di < mi
  } catch {
    return false
  }
}
// "YYYY-MM-DDTHH:MM" → "jj/mm/aaaa HH:MM" (ou "mm/jj/aaaa HH:MM").
function dtToDisplay(value: string, dayFirst: boolean): string {
  const m = value && value.match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/)
  if (!m) return ''
  const [, y, mo, d, h, mi] = m
  const date = dayFirst ? `${d}/${mo}/${y}` : `${mo}/${d}/${y}`
  return `${date} ${h}:${mi}`
}
// Saisie localisée "jj/mm/aaaa[ HH:MM]" (ou mm/jj) → "YYYY-MM-DDTHH:MM". '' si vide, null si invalide.
function dtFromDisplay(text: string, dayFirst: boolean): string | null {
  const s = text.trim()
  if (s === '') return ''
  const m = s.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:[ T](\d{1,2}):(\d{2}))?$/)
  if (!m) return null
  const a = m[1], b = m[2], y = m[3]
  const h = m[4] ?? '00', mi = m[5] ?? '00'
  const day = dayFirst ? a : b
  const mon = dayFirst ? b : a
  const D = +day, M = +mon, H = +h, MI = +mi
  if (M < 1 || M > 12 || D < 1 || D > 31 || H > 23 || MI > 59) return null
  const p2 = (n: number) => String(n).padStart(2, '0')
  return `${y}-${p2(M)}-${p2(D)}T${p2(H)}:${p2(MI)}`
}

function DateTimeField({ value, onChange, locale }: {
  value: string
  onChange: (v: string) => void
  locale: string
}) {
  const dayFirst = useMemo(() => localeDayFirst(locale), [locale])
  const [text, setText] = useState(() => dtToDisplay(value, dayFirst))
  const [open, setOpen] = useState(false)
  const wrapRef = useRef<HTMLDivElement>(null)

  // Resynchronise l'affichage quand la valeur change de l'extérieur (chargement, changement de langue).
  useEffect(() => { setText(dtToDisplay(value, dayFirst)) }, [value, dayFirst])

  // Ferme le calendrier au clic en dehors.
  useEffect(() => {
    if (!open) return
    const onDown = (e: MouseEvent) => { if (wrapRef.current && !wrapRef.current.contains(e.target as Node)) setOpen(false) }
    document.addEventListener('mousedown', onDown)
    return () => document.removeEventListener('mousedown', onDown)
  }, [open])

  const commit = () => {
    const iso = dtFromDisplay(text, dayFirst)
    if (iso === null) { setText(dtToDisplay(value, dayFirst)); return } // saisie invalide → on revient à la valeur
    onChange(iso)
  }

  return (
    <div className="relative" ref={wrapRef}>
      <Input
        value={text}
        onChange={(e) => setText(e.target.value)}
        onBlur={commit}
        onKeyDown={(e) => { if (e.key === 'Enter') (e.currentTarget as HTMLInputElement).blur() }}
        placeholder={dayFirst ? 'jj/mm/aaaa hh:mm' : 'mm/dd/yyyy hh:mm'}
        inputMode="numeric"
        className="h-8 pr-8 text-xs"
      />
      <button
        type="button"
        onClick={() => setOpen((o) => !o)}
        tabIndex={-1}
        aria-label={t('calendar')}
        className="absolute right-1.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
      >
        <Calendar className="size-3.5" />
      </button>
      {/* Calendrier custom localisé (langue du BO) — remplace le picker natif (anglais forcé). */}
      {open && <CalendarPopup value={value} onChange={onChange} locale={locale} onClose={() => setOpen(false)} />}
    </div>
  )
}

// Vrais drapeaux servis par MelisCore (les emojis de drapeaux ne s'affichent pas sous Windows).
function Flag({ locale }: { locale: string }) {
  const short = (locale || 'en').slice(0, 2).toLowerCase()
  return (
    <img
      src={`/MelisCore/assets/images/lang/${short}.png`}
      alt=""
      className="h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
      onError={(e) => { (e.currentTarget as HTMLImageElement).style.display = 'none' }}
    />
  )
}

// ─── Sidebar section ──────────────────────────────────────────────────────────

function SidebarSection({
  title, icon: Icon, children, collapsible = false, defaultOpen = true,
}: {
  title: string; icon: React.ElementType; children: React.ReactNode
  collapsible?: boolean; defaultOpen?: boolean
}) {
  const [open, setOpen] = useState(defaultOpen)
  return (
    <div className="border-b border-border pb-4 last:border-0 last:pb-0">
      <button
        type="button"
        onClick={() => collapsible && setOpen((v) => !v)}
        className={cn(
          'flex w-full items-center justify-between py-1.5 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground',
          collapsible && 'cursor-pointer hover:text-foreground transition-colors',
        )}
      >
        <span className="flex items-center gap-1.5">
          <Icon className="size-3.5" />
          {title}
        </span>
        {collapsible && (open
          ? <ChevronUp className="size-3.5" />
          : <ChevronDown className="size-3.5" />)}
      </button>
      {open && <div className="mt-2.5 space-y-2.5">{children}</div>}
    </div>
  )
}

// Nombre de commentaires affichés par page dans le panneau de modération.
const COMMENTS_PER_PAGE = 5

// ─── Pager (pagination générique, client-side) ──────────────────────────────────
// Contrôle compact « Précédent · Page x sur y · Suivant ». Masqué s'il n'y a qu'une page.
function Pager({ page, totalPages, onChange }: {
  page: number
  totalPages: number
  onChange: (p: number) => void
}) {
  if (totalPages <= 1) return null
  return (
    <div className="flex items-center justify-between gap-2 pt-1">
      <Button
        variant="outline" size="sm" className="gap-1"
        disabled={page <= 1}
        onClick={() => onChange(page - 1)}
      >
        <ChevronLeft className="size-3.5" />
        {t('pager_prev')}
      </Button>
      <span className="text-[11px] font-medium text-muted-foreground">
        {t('pager_page_of', { page, total: totalPages })}
      </span>
      <Button
        variant="outline" size="sm" className="gap-1"
        disabled={page >= totalPages}
        onClick={() => onChange(page + 1)}
      >
        {t('pager_next')}
        <ChevronRight className="size-3.5" />
      </Button>
    </div>
  )
}

// ─── Comment row (modération — module MelisCmsComments) ──────────────────────────
// Pastille de statut (bleu = en attente, vert = validé/affiché, rouge = refusé/masqué),
// auteur, texte, date relative, et actions selon le statut (valider / refuser / supprimer).

function CommentRow({ c, busy, onModerate, appLang }: {
  c: newsApi.NewsComment
  busy: boolean
  onModerate: (action: 'approve' | 'refuse' | 'delete', id: number) => void
  appLang: string
}) {
  const dot   = c.validated === 1 ? 'bg-emerald-500' : c.validated === 2 ? 'bg-red-500' : 'bg-sky-500'
  const label = c.validated === 1 ? t('comment_approved') : c.validated === 2 ? t('comment_refused') : t('comment_pending')
  const when  = c.date ? new Date(c.date.replace(' ', 'T')).toLocaleString(appLang, { dateStyle: 'medium', timeStyle: 'short' }) : ''

  return (
    <li className="flex items-start gap-3 rounded-lg border border-border bg-background p-3">
      <span className={cn('mt-1.5 size-2 shrink-0 rounded-full', dot)} title={label} />
      <div className="min-w-0 flex-1">
        <div className="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
          <span className="text-sm font-medium text-foreground">{c.name}</span>
          <span className="text-[11px] text-muted-foreground">{label}</span>
          {when && <span className="text-[11px] text-muted-foreground/70">· {when}</span>}
        </div>
        {/* Le texte est déjà purifié (HTMLPurifier) côté serveur avant sauvegarde. */}
        <div
          className="mt-1 break-words text-sm text-muted-foreground [&_a]:text-primary [&_a]:underline"
          dangerouslySetInnerHTML={{ __html: c.text }}
        />
      </div>
      <div className="flex shrink-0 items-center gap-1">
        {c.validated !== 1 && (
          <button type="button" title={t('comment_approve')} disabled={busy}
            onClick={() => onModerate('approve', c.id)}
            className="rounded-md p-1.5 text-emerald-600 hover:bg-emerald-500/10 disabled:opacity-40">
            <Check className="size-4" />
          </button>
        )}
        {c.validated !== 2 && (
          <button type="button" title={t('comment_refuse')} disabled={busy}
            onClick={() => onModerate('refuse', c.id)}
            className="rounded-md p-1.5 text-amber-600 hover:bg-amber-500/10 disabled:opacity-40">
            <Ban className="size-4" />
          </button>
        )}
        <button type="button" title={t('delete')} disabled={busy}
          onClick={() => onModerate('delete', c.id)}
          className="rounded-md p-1.5 text-red-600 hover:bg-red-500/10 disabled:opacity-40">
          <Trash2 className="size-4" />
        </button>
      </div>
    </li>
  )
}

// ─── Category tree (apporté par le module MelisCmsCategory2) ─────────────────────
// Sélecteur multi-catégories rendu en ARBRE (cnews ↔ cat2 via melis_cms_news_category).
// Les catégories arrivent à plat avec `fatherCatId` ; on reconstruit la hiérarchie (racine =
// père absent de la liste ou -1) et on indente les enfants.

function CategoryTree({ categories, selected, onToggle }: {
  categories: newsApi.NewsCategory[]
  selected: number[]
  onToggle: (id: number) => void
}) {
  const byParent = useMemo(() => {
    const ids = new Set(categories.map((c) => c.id))
    const map = new Map<number, newsApi.NewsCategory[]>()
    for (const c of categories) {
      const parent = ids.has(c.fatherCatId) ? c.fatherCatId : -1
      if (!map.has(parent)) map.set(parent, [])
      map.get(parent)!.push(c)
    }
    return map
  }, [categories])

  const render = (parentId: number, depth: number): React.ReactNode =>
    (byParent.get(parentId) ?? []).map((c) => (
      <div key={c.id}>
        <label
          className="flex items-center gap-2 py-0.5 text-xs text-foreground cursor-pointer hover:text-primary"
          style={{ paddingLeft: depth * 14 }}
        >
          <input
            type="checkbox"
            checked={selected.includes(c.id)}
            onChange={() => onToggle(c.id)}
            className="size-3.5 shrink-0 rounded border-input accent-primary"
          />
          {/* Pastille de statut réel : verte = actif, rouge = inactif (cat2_status). Le picker
              legacy (jstree) affiche tout en vert sans distinction — on est ici plus fidèle au
              vrai statut, comme l'arbre de gestion des catégories. */}
          <span
            className={cn('size-1.5 shrink-0 rounded-full', c.status === 1 ? 'bg-emerald-500' : 'bg-red-500')}
            title={c.status === 1 ? t('filter_active') : t('filter_inactive')}
          />
          <span className="truncate">{c.name}</span>
        </label>
        {render(c.id, depth + 1)}
      </div>
    ))

  return <>{render(-1, 0)}</>
}

// ─── Paragraph editor ─────────────────────────────────────────────────────────

function ParagraphEditor({
  index, value, onChange, onRemove, canRemove, extraActions,
  canReorder, isDragging, dragActive, isDropTarget, onDragStart, onDragOverCard, onDrop, onDragEnd,
}: {
  index: number; value: string; onChange: (v: string) => void
  onRemove: () => void; canRemove: boolean
  extraActions?: React.ReactNode
  canReorder: boolean
  isDragging: boolean
  dragActive: boolean       // un glisser de paragraphe est en cours (n'importe lequel)
  isDropTarget: boolean     // cette carte est la cible : le paragraphe glissé prendra CETTE position
  onDragStart: () => void
  onDragOverCard: () => void
  onDrop: () => void
  onDragEnd: () => void
}) {
  // La carte n'est « draggable » qu'après un appui sur la poignée — sinon la sélection de
  // texte dans l'éditeur riche déclencherait un glisser intempestif.
  const [armed, setArmed] = useState(false)
  return (
    <div
      draggable={armed}
      onDragStart={(e) => { e.dataTransfer.effectAllowed = 'move'; onDragStart() }}
      onDragOver={(e) => { if (canReorder) { e.preventDefault(); onDragOverCard() } }}
      onDrop={(e) => { e.preventDefault(); setArmed(false); onDrop() }}
      onDragEnd={() => { setArmed(false); onDragEnd() }}
      className={cn(
        'relative rounded-lg transition-all',
        isDragging && 'opacity-40',
        // La carte cible : le paragraphe glissé prendra exactement cette position.
        isDropTarget && 'ring-2 ring-primary ring-offset-2 ring-offset-background bg-primary/5',
      )}
    >
      {/* Couche transparente capturant le survol/dépôt SUR toute la carte pendant un glisser.
          Indispensable car l'éditeur TinyMCE est un <iframe> : sans overlay, les événements
          drag au-dessus du texte sont absorbés par l'iframe et ne remontent jamais à la carte
          → impossible de déposer « dans » le texte.
          ⚠️ L'overlay est TOUJOURS monté et on ne fait que basculer `pointer-events` par classe :
          insérer/retirer un nœud DOM pendant `dragstart` annule le glisser sous Chrome. En dehors
          d'un glisser il est `pointer-events:none` → l'édition normale n'est jamais gênée. */}
      <div
        className={cn(
          'absolute inset-0 z-20',
          dragActive && !isDragging ? 'pointer-events-auto' : 'pointer-events-none',
        )}
        onDragOver={(e) => { if (canReorder) { e.preventDefault(); onDragOverCard() } }}
        onDrop={(e) => { e.preventDefault(); setArmed(false); onDrop() }}
      />
      <div className="mb-1.5 flex items-center justify-between">
        <span className="flex items-center gap-1 text-[11px] font-medium text-muted-foreground tracking-wide">
          {canReorder && (
            <span
              onMouseDown={() => setArmed(true)}
              onMouseUp={() => setArmed(false)}
              title={t('drag_reorder')}
              className="cursor-grab active:cursor-grabbing rounded p-0.5 text-muted-foreground/40 hover:text-foreground transition-colors"
            >
              <GripVertical className="size-3.5" />
            </span>
          )}
          {t('paragraph_n', { n: index + 1 })}
        </span>
        <div className="flex items-center gap-2">
          {extraActions}
          {canRemove && (
            <button
              type="button"
              onClick={onRemove}
              title={t('remove_paragraph')}
              className="rounded p-0.5 text-muted-foreground/50 hover:text-destructive transition-colors"
            >
              <X className="size-3.5" />
            </button>
          )}
        </div>
      </div>
      <MelisToolEditor
        value={value}
        onChange={onChange}
        minHeight={220}
      />
    </div>
  )
}

// Empêche le « scroll-au-focus » : cliquer une zone d'upload met le focus sur l'input fichier,
// et le navigateur scrolle alors le conteneur pour l'amener « à la vue » → tout le contenu News
// (avec ses sous-onglets) saute hors écran (onglet « vide »). On mémorise la position de scroll de
// TOUS les ancêtres au mousedown (avant le focus) et on la restaure sur quelques frames.
function preserveScrollOnFocus(target: HTMLElement) {
  const snaps: Array<[HTMLElement, number, number]> = []
  let el: HTMLElement | null = target
  while (el) { snaps.push([el, el.scrollTop, el.scrollLeft]); el = el.parentElement }
  const wx = window.scrollX, wy = window.scrollY
  const restore = () => {
    snaps.forEach(([n, top, left]) => { if (n.scrollTop !== top) n.scrollTop = top; if (n.scrollLeft !== left) n.scrollLeft = left })
    if (window.scrollX !== wx || window.scrollY !== wy) window.scrollTo(wx, wy)
  }
  requestAnimationFrame(restore)
  setTimeout(restore, 0)
  setTimeout(restore, 60)
}

// ─── Image slot (upload fonctionnel vers cnews_image1..3) ───────────────────────

function ImageSlot({ src, label, disabled, uploading, onPick, onRemove }: {
  src: string | null; label: string; disabled: boolean; uploading: boolean
  onPick: (file: File) => void; onRemove: () => void
}) {
  if (src) {
    // Actions TOUJOURS visibles sous l'image (barre dédiée) : pas d'overlay `absolute inset-0`
    // en opacity-0 — un tel overlay reste CLIQUABLE (opacity n'annule pas les pointer-events) et,
    // son bouton « Supprimer » étant centré sur l'image, un simple clic sur la vignette supprimait
    // le média par accident. Ici la vignette n'a aucune zone cliquable cachée.
    return (
      <div className="relative overflow-hidden rounded-xl border border-border bg-card">
        <img src={src} alt={label} className="h-32 w-full object-cover" onError={(e) => { (e.currentTarget as HTMLImageElement).src = '' }} />
        <div className="flex items-center justify-between gap-2 border-t border-border px-2 py-1.5">
          <span className="truncate text-[11px] font-medium text-muted-foreground">{label}</span>
          <div className="flex items-center gap-1">
            <label className={cn('rounded-md px-2 py-1 text-[11px] font-medium text-muted-foreground hover:bg-muted',
              uploading ? 'cursor-not-allowed opacity-50' : 'cursor-pointer')} title={t('replace')}>
              {t('replace')}
              <input type="file" accept="image/*" className="hidden" disabled={disabled || uploading}
                onChange={(e) => { const f = e.target.files?.[0]; if (f) onPick(f); e.currentTarget.value = '' }} />
            </label>
            <button type="button" onClick={onRemove} disabled={uploading}
              className="rounded-md px-2 py-1 text-[11px] font-medium text-destructive hover:bg-destructive/10 disabled:opacity-50">
              {uploading ? '…' : t('remove')}
            </button>
          </div>
        </div>
      </div>
    )
  }
  return (
    <label className={cn('relative group flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-border bg-card py-5 transition-colors',
      disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer hover:border-primary/50 hover:bg-primary/5')}
      onMouseDown={(e) => preserveScrollOnFocus(e.currentTarget)}>
      <div className="flex size-9 items-center justify-center rounded-full bg-muted group-hover:bg-primary/10">
        {uploading ? <Loader2 className="size-4 animate-spin text-primary" /> : <Image className="size-4 text-muted-foreground group-hover:text-primary" />}
      </div>
      <p className="text-[11px] font-medium text-muted-foreground">{label}</p>
      {/* Input positionné DANS la zone (absolute inset-0 opacity-0) et NON en sr-only hors écran :
          un input caché hors écran, une fois focus (via le label), fait scroller le conteneur pour
          l'amener « à la vue » → tout le contenu saute hors écran (onglet « vide »). Ici il reste
          dans le cadre visible → aucun scroll parasite. */}
      <input type="file" accept="image/*" className="absolute inset-0 cursor-pointer opacity-0" disabled={disabled || uploading}
        onChange={(e) => { const f = e.target.files?.[0]; if (f) onPick(f); e.currentTarget.value = '' }} />
    </label>
  )
}

// ─── File attachment slot (upload fonctionnel vers cnews_documents1..3) ──────────

function FileSlot({ src, disabled, uploading, onPick, onRemove }: {
  src: string | null; disabled: boolean; uploading: boolean
  onPick: (file: File) => void; onRemove: () => void
}) {
  if (src) {
    const name = src.split('/').pop() || src
    return (
      <div className="flex items-center gap-3 rounded-xl border border-border bg-card px-4 py-3">
        <div className="flex size-8 items-center justify-center rounded-full bg-muted"><Paperclip className="size-4 text-muted-foreground" /></div>
        <a href={src} target="_blank" rel="noreferrer" className="flex-1 truncate text-xs text-foreground hover:underline">{name}</a>
        <button type="button" onClick={onRemove} disabled={uploading}
          className="rounded-md px-2 py-1 text-[11px] font-medium text-destructive hover:bg-destructive/10">
          {uploading ? '…' : t('remove')}
        </button>
      </div>
    )
  }
  return (
    <label className={cn('relative group flex items-center gap-3 rounded-xl border border-dashed border-border px-4 py-3 transition-colors',
      disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer hover:border-primary/50 hover:bg-primary/5')}
      onMouseDown={(e) => preserveScrollOnFocus(e.currentTarget)}>
      <div className="flex size-8 items-center justify-center rounded-full bg-muted group-hover:bg-primary/10">
        {uploading ? <Loader2 className="size-4 animate-spin text-primary" /> : <Paperclip className="size-4 text-muted-foreground group-hover:text-primary" />}
      </div>
      <span className="text-xs text-muted-foreground">{t('attach_file')}</span>
      {/* cf. ImageSlot : input dans la zone (pas sr-only hors écran) pour éviter le scroll au focus. */}
      <input type="file" className="absolute inset-0 cursor-pointer opacity-0" disabled={disabled || uploading}
        onChange={(e) => { const f = e.target.files?.[0]; if (f) onPick(f); e.currentTarget.value = '' }} />
    </label>
  )
}

// ─── Module-level cache ────────────────────────────────────────────────────────

type NewsItemCache = { form: FormState; langId: number; langTexts: Record<number, LangText> }
const _newsCache = new Map<string, NewsItemCache>()

let _langCache:  newsApi.Language[]    | null = null
let _siteCache:  newsApi.Site[]        | null = null

// ─── Main component ───────────────────────────────────────────────────────────

export default function NewsFormPage({ newsId, onSaved, onTitleChange }: {
  newsId: number | 'new'
  onSaved: (id: number, title: string) => void
  onTitleChange?: (title: string) => void
}) {
  const isNew = newsId === 'new'
  // On garde `id` en string|undefined (comme l'ancien useParams) pour ne rien changer au reste.
  const id = isNew ? undefined : String(newsId)
  const narrow = useIsNarrow()

  // Capacités : droit d'enregistrer = create (nouvel article) ou edit (existant).
  const { can } = useCaps(NEWS_CAPS_KEY)
  const canSave = isNew ? can('create') : can('edit')

  const [languages, setLanguages]       = useState<newsApi.Language[]>([])
  const [sites, setSites]               = useState<newsApi.Site[]>([])
  const [users, setUsers]               = useState<newsApi.User[]>([])
  const [sliders, setSliders]           = useState<newsApi.Slider[]>([])
  const [sliderActive, setSliderActive] = useState(false)   // vrai ssi le module Slider est actif
  const [userAccountActive, setUserAccountActive] = useState(false)   // vrai ssi MelisCmsUserAccount est actif (→ section Auteur)
  const [sbActive, setSbActive]         = useState(false)   // vrai ssi MelisSmallBusiness est actif (→ bouton Workflow)
  const [wfOpen, setWfOpen]             = useState(false)   // modale Workflow (validation) ouverte
  const [uploadingCol, setUploadingCol] = useState<string | null>(null) // colonne média en cours d'upload/suppression
  const [categories, setCategories]     = useState<newsApi.NewsCategory[]>([])
  const [categoryActive, setCategoryActive] = useState(false) // vrai ssi MelisCmsCategory2 est actif (→ section Catégories)
  const [tags, setTags]                 = useState<newsApi.NewsTag[]>([])
  const [tagsActive, setTagsActive]     = useState(false)     // vrai ssi MelisCmsTags est actif (→ section Tags)
  // Commentaires (module optionnel MelisCmsComments) — chargés pour un article existant ;
  // commentsActive vrai ssi la route /news/:id/comments répond (module actif).
  const [comments, setComments]         = useState<newsApi.NewsComment[]>([])
  const [commentsActive, setCommentsActive] = useState(false)
  const [newComment, setNewComment]     = useState({ name: '', text: '' })
  const [commentBusy, setCommentBusy]   = useState(false)
  const [pendingDelete, setPendingDelete] = useState<newsApi.NewsComment | null>(null)  // commentaire en attente de confirmation de suppression
  const [commentsPage, setCommentsPage] = useState(1)                                    // pagination (client-side) de la liste
  const [dragIndex, setDragIndex]       = useState<number | null>(null)  // paragraphe en cours de glisser
  const [overIndex, setOverIndex]       = useState<number | null>(null)  // paragraphe actuellement survolé (cible de dépôt)
  const [previewUrl, setPreviewUrl]     = useState<string | null>(null)
  // Choix d'éditeur retiré — TinyMCE ('tool') forcé.
  // const [editorEngine, setEditorEngine] = useState<RichEditorEngine>('tiptap')

  // Langue de l'app (chrome hôte) → format de date fr/en.
  const appLang = (document.documentElement.lang || 'en').slice(0, 2)

  const [langId, setLangId]   = useState<number>(1)
  // Textes des langues NON affichées (cf. LangText plus haut) — alimentés au fil des bascules.
  const [langTexts, setLangTexts] = useState<Record<number, LangText>>({})
  // Bascule de langue en cours (chargement de la traduction depuis l'API) : pendant cet
  // intervalle `form` porte encore les textes de l'ANCIENNE langue alors que `langId` est
  // déjà la nouvelle → on gèle l'écriture du cache, qui mémoriserait un couple incohérent.
  const [langBusy, setLangBusy] = useState(false)
  const [form, setForm]       = useState<FormState>(EMPTY)
  const [loading, setLoading] = useState(false)
  const [saving, setSaving]   = useState(false)
  const [apiError, setApiError] = useState<string | null>(null)
  const [errors, setErrors]   = useState<Partial<Record<string, string>>>({})

  // `textOnly` : bascule de langue → on ne remplace QUE les textes, pour ne pas écraser les
  // modifications non enregistrées des champs indépendants de la langue (statut, site, dates…).
  const loadNews = useCallback(async (newsId: number, targetLangId: number, textOnly = false) => {
    try {
      const d = await newsApi.fetchNewsById(newsId, targetLangId)
      if (textOnly) {
        setForm((prev) => ({
          ...prev,
          title:      d.title    ?? '',
          subtitle:   d.subtitle ?? '',
          paragraphs: d.paragraphs && d.paragraphs.length ? d.paragraphs : [''],
          seo:        d.seo ?? EMPTY_SEO,
        }))
        return
      }
      setForm({
        title:         d.title    ?? '',
        subtitle:      d.subtitle ?? '',
        paragraphs:    d.paragraphs && d.paragraphs.length ? d.paragraphs : [''],
        status:        d.status === 1 ? '1' : '0',
        siteId:        d.siteId ? String(d.siteId) : '',
        publishDate:   toInputDate(d.publishDate),
        unpublishDate: toInputDate(d.unpublishDate),
        sliderId:      d.sliderId ? String(d.sliderId) : '',
        authorId:      d.authorId ? String(d.authorId) : '',
        validateComments: !!d.validateComments,
        categoryIds:   d.categoryIds ?? [],
        tagIds:        d.tagIds ?? [],
        seo:           d.seo ?? EMPTY_SEO,
        image1:        d.image1 ?? null,
        image2:        d.image2 ?? null,
        image3:        d.image3 ?? null,
        document1:     d.document1 ?? null,
        document2:     d.document2 ?? null,
        document3:     d.document3 ?? null,
      })
    } catch (e) {
      setApiError(e instanceof Error ? e.message : t('err_load'))
    }
  }, [])

  useEffect(() => {
    if (!_newsCache.has(id ?? '')) setLoading(true)

    const tasks: Promise<unknown>[] = [
      _langCache
        ? Promise.resolve().then(() => { setLanguages(_langCache!); if (!_newsCache.has(id ?? '')) setLangId(_langCache![0]?.id ?? 1) })
        : newsApi.fetchLanguages().then((langs) => { _langCache = langs; setLanguages(langs); if (!_newsCache.has(id ?? '') && langs.length > 0) setLangId(langs[0].id) }).catch(() => {}),
      _siteCache
        ? Promise.resolve().then(() => setSites(_siteCache!))
        : newsApi.fetchSites().then(s => { _siteCache = s; setSites(s) }).catch(() => {}),
      // Slider : modulaire — n'apparaît que si l'outil Slider (migré) est actif (route 404 sinon).
      newsApi.fetchSliders().then((s) => { setSliders(s); setSliderActive(true) }).catch(() => setSliderActive(false)),
      // Users : modulaire (MelisCmsUserAccount) — charge les utilisateurs disponibles comme auteurs.
      newsApi.fetchUsers().then((u) => { setUsers(u); setUserAccountActive(true) }).catch(() => setUserAccountActive(false)),
      // Modules optionnels détectés via /react-modules (listé ssi actif) :
      //  - MelisSmallBusiness → bouton « Workflow » de la barre latérale.
      //  - MelisCmsCategory2  → section « Catégories » du formulaire (l'outil categorie-v2 est
      //    désormais migré en React ; les catégories sont chargées par un effet dédié réactif à
      //    la langue une fois `categoryActive` vrai).
      newsApi.fetchActiveModules().then((mods) => {
        setSbActive(mods.includes('MelisSmallBusiness'))
        setCategoryActive(mods.includes('MelisCmsCategory2'))
        setTagsActive(mods.includes('MelisCmsTags'))
      }).catch(() => {}),
    ]

    if (!isNew) {
      const cached = _newsCache.get(id!)
      if (cached) {
        setForm(cached.form)
        setLangId(cached.langId)
        setLangTexts(cached.langTexts)
      } else {
        tasks.push(loadNews(Number(id), langId))
      }
      tasks.push(newsApi.fetchNewsPreview(Number(id)).then((preview) => setPreviewUrl(preview.previewUrl)).catch(() => {}))
    }

    Promise.all(tasks).finally(() => setLoading(false))
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id, isNew])

  useEffect(() => {
    if (!isNew && id && form.title && !langBusy) {
      _newsCache.set(id, { form, langId, langTexts })
    }
  }, [form, langId, langTexts, langBusy, id, isNew])

  // Bascule de langue (clic sur un drapeau). Les textes de la langue quittée sont mis de côté,
  // puis ceux de la langue demandée sont restaurés : depuis le tampon si elle a déjà été ouverte,
  // sinon depuis l'API (article existant) ou vides (nouvel article — rien n'est encore en base).
  // Remplace l'ancien effet sur [langId], qui rechargeait l'article en écrasant les champs
  // communs et ne faisait RIEN sur un nouvel article (d'où « la langue ne change rien »).
  async function switchLang(next: number) {
    if (next === langId || loading || langBusy) return
    const current = pickText(form)
    setLangTexts((prev) => ({ ...prev, [langId]: current }))
    setLangId(next)
    setErrors({})

    const buffered = langTexts[next]
    if (buffered) { setForm((prev) => ({ ...prev, ...buffered })); return }
    if (isNew)    { setForm((prev) => ({ ...prev, ...EMPTY_TEXT })); return }

    setLangBusy(true)
    try {
      await loadNews(Number(id), next, true)
    } finally {
      setLangBusy(false)
    }
  }

  // Catégories (module optionnel MelisCmsCategory2) : chargées ssi le module est actif, et
  // re-fetchées quand la langue du BO change (noms traduits par langue) OU quand le site de
  // l'article change (les catégories sont restreintes au site, comme le legacy). La SÉLECTION
  // `categoryIds`, elle, est indépendante de la langue et du site → conservée.
  useEffect(() => {
    if (!categoryActive) return
    newsApi.fetchCategories(langId, form.siteId || undefined).then(setCategories).catch(() => {})
  }, [langId, categoryActive, form.siteId])

  // Tags (module optionnel MelisCmsTags) : mêmes règles que les catégories — chargés ssi
  // le module est actif et re-fetchés à chaque changement de langue (titres traduits) ;
  // la SÉLECTION `tagIds` reste indépendante de la langue.
  useEffect(() => {
    if (!tagsActive) return
    newsApi.fetchTags(langId).then(setTags).catch(() => {})
  }, [langId, tagsActive])

  useEffect(() => {
    // Met à jour le libellé du sous-onglet quand le titre change (article existant).
    if (!isNew && form.title) onTitleChange?.(form.title)
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [form.title])

  function set<K extends keyof FormState>(key: K, value: FormState[K]) {
    setForm((prev) => ({ ...prev, [key]: value }))
    setErrors((prev) => ({ ...prev, [key]: undefined }))
  }

  function setSeo(key: keyof newsApi.NewsSeo, value: string) {
    setForm((prev) => ({ ...prev, seo: { ...prev.seo, [key]: value } }))
  }

  function updateParagraph(i: number, value: string) {
    setForm((prev) => {
      const p = [...prev.paragraphs]; p[i] = value
      return { ...prev, paragraphs: p }
    })
  }

  function addParagraph() {
    if (form.paragraphs.length < MAX_PARAGRAPHS) {
      setForm((prev) => ({ ...prev, paragraphs: [...prev.paragraphs, ''] }))
    }
  }

  function removeParagraph(i: number) {
    setForm((prev) => {
      const p = prev.paragraphs.filter((_, idx) => idx !== i)
      return { ...prev, paragraphs: p.length ? p : [''] }
    })
  }

  // Réordonnancement des paragraphes par glisser-déposer (drag'n'drop). L'ordre est
  // persisté à la sauvegarde (colonnes 1..N séquentielles + cnews_paragraph_order).
  function moveParagraph(from: number, to: number) {
    if (from === to) return
    setForm((prev) => {
      const p = [...prev.paragraphs]
      const [moved] = p.splice(from, 1)
      p.splice(to, 0, moved)
      return { ...prev, paragraphs: p }
    })
  }

  function toggleCategory(catId: number) {
    setForm((prev) => {
      const ids = prev.categoryIds.includes(catId)
        ? prev.categoryIds.filter((c) => c !== catId)
        : [...prev.categoryIds, catId]
      return { ...prev, categoryIds: ids }
    })
  }

  function toggleTag(tagId: number) {
    setForm((prev) => {
      const ids = prev.tagIds.includes(tagId)
        ? prev.tagIds.filter((tg) => tg !== tagId)
        : [...prev.tagIds, tagId]
      return { ...prev, tagIds: ids }
    })
  }

  // Toutes les traductions saisies (langue affichée comprise), indexées par langue.
  const allTexts = (): Record<number, LangText> => ({ ...langTexts, [langId]: pickText(form) })

  function validate() {
    const errs: Partial<Record<string, string>> = {}
    // Le titre est exigé dans AU MOINS une langue, pas forcément celle affichée : on n'empêche
    // pas d'enregistrer parce que l'utilisateur a laissé la traduction courante à traduire.
    if (!Object.values(allTexts()).some((x) => x.title.trim())) errs.title = t('title_required')
    if (!form.siteId) errs.siteId = t('site_required')
    setErrors(errs)
    return Object.keys(errs).length === 0
  }

  async function handleSave() {
    if (!validate()) return
    setSaving(true); setApiError(null)
    try {
      const texts = allTexts()
      // Une traduction est enregistrée si elle porte du contenu OU si c'est la langue affichée
      // (vider volontairement la traduction courante doit être persisté, pas ignoré).
      const translations: newsApi.NewsTranslation[] = Object.entries(texts)
        .filter(([lid, x]) => Number(lid) === langId || hasText(x))
        .map(([lid, x]) => ({
          langId:     Number(lid),
          title:      x.title.trim(),
          subtitle:   x.subtitle.trim(),
          paragraphs: x.paragraphs,
          seo:        x.seo,
        }))
      // Titre porté par l'enregistrement (validation serveur + libellé de l'onglet) : celui de la
      // langue affichée, à défaut la première traduction titrée.
      const mainTitle = form.title.trim() || (translations.find((x) => x.title)?.title ?? '')

      const payload: newsApi.NewsSavePayload = {
        id:            isNew ? null : Number(id),
        langId,
        translations,
        title:         mainTitle,
        subtitle:      form.subtitle.trim() || undefined,
        paragraphs:    form.paragraphs,
        status:        form.status === '1' ? 1 : 0,
        siteId:        Number(form.siteId),
        publishDate:   toDbDate(form.publishDate),
        unpublishDate: toDbDate(form.unpublishDate),
        sliderId:      form.sliderId ? Number(form.sliderId) : null,
        authorId:      form.authorId ? Number(form.authorId) : null,
        categoryIds:   form.categoryIds,
        tagIds:        form.tagIds,
        seo:           form.seo,
      }
      // N'envoyer le flag de modération que si le module Comments est actif (colonne présente).
      if (commentsActive) payload.validateComments = form.validateComments
      const res = await newsApi.saveNews(payload)
      okNotify(t('news_title'), t('saved_ok'))
      // Le conteneur (NewsPage) convertit l'onglet « new » en onglet de l'article créé,
      // ou met à jour le libellé d'un article existant.
      onSaved(res.id, mainTitle || `Article #${res.id}`)
    } catch (e) {
      const m = e instanceof Error ? e.message : t('err_save')
      setApiError(m)
      koNotify(t('news_title'), m)
    } finally {
      setSaving(false)
    }
  }

  // ─── Commentaires (module optionnel MelisCmsComments) ─────────────────────────
  // Charge les commentaires d'un article existant. La réponse distingue « module inactif »
  // (route 404 → commentsActive=false → section masquée) de « aucun commentaire » (liste vide).
  const loadComments = useCallback(async (newsId: number) => {
    const res = await newsApi.fetchNewsComments(newsId)
    setCommentsActive(res.active)
    setComments(res.items)
  }, [])

  useEffect(() => {
    if (!isNew && newsId) loadComments(newsId)
  }, [isNew, newsId, loadComments])

  // Pagination (client-side) : la liste complète est déjà chargée, on la découpe par page.
  const commentTotalPages = Math.max(1, Math.ceil(comments.length / COMMENTS_PER_PAGE))
  const pagedComments = comments.slice((commentsPage - 1) * COMMENTS_PER_PAGE, commentsPage * COMMENTS_PER_PAGE)
  // Recadre la page courante si la liste rétrécit (ex. après une suppression sur la dernière page).
  useEffect(() => {
    if (commentsPage > commentTotalPages) setCommentsPage(commentTotalPages)
  }, [commentsPage, commentTotalPages])

  // Exécute réellement l'action de modération (appel API + rechargement).
  async function runModeration(action: 'approve' | 'refuse' | 'delete', commentId: number) {
    setCommentBusy(true); setApiError(null)
    try {
      if (action === 'approve') await newsApi.approveNewsComment(commentId)
      else if (action === 'refuse') await newsApi.refuseNewsComment(commentId)
      else await newsApi.deleteNewsComment(commentId)
      if (newsId) await loadComments(newsId)
    } catch (e) {
      setApiError(e instanceof Error ? e.message : t('error'))
    } finally {
      setCommentBusy(false)
    }
  }

  // Handler du CommentRow : valider/refuser s'exécutent directement ; supprimer ouvre la
  // modale de confirmation (ConfirmDialog) au lieu du window.confirm() natif.
  function moderateComment(action: 'approve' | 'refuse' | 'delete', commentId: number) {
    if (action === 'delete') {
      setPendingDelete(comments.find((c) => c.id === commentId) ?? null)
      return
    }
    void runModeration(action, commentId)
  }

  async function confirmDelete() {
    if (!pendingDelete) return
    await runModeration('delete', pendingDelete.id)
    setPendingDelete(null)
  }

  async function addComment() {
    if (isNew || !newsId || !newComment.text.trim()) return
    setCommentBusy(true); setApiError(null)
    try {
      await newsApi.saveNewsComment({ postId: newsId, text: newComment.text.trim(), name: newComment.name.trim() })
      setNewComment({ name: '', text: '' })
      await loadComments(newsId)
    } catch (e) {
      setApiError(e instanceof Error ? e.message : t('err_save'))
    } finally {
      setCommentBusy(false)
    }
  }

  // ─── Médias (upload/suppression image + fichier) ──────────────────────────────
  // L'upload cible l'article existant (id numérique) ; désactivé tant qu'il n'est pas
  // enregistré. Après succès, on recharge UNIQUEMENT les champs média (préserve les edits).
  async function refreshMedia() {
    if (isNew || !id) return
    try {
      const media = await newsApi.fetchNewsMedia(Number(id), langId)
      setForm((f) => ({ ...f, ...media }))
    } catch { /* ignore */ }
  }
  async function uploadMedia(kind: newsApi.MediaKind, slot: 1 | 2 | 3, file: File) {
    if (isNew || !id) return
    const col = newsApi.mediaColumn(kind, slot)
    setUploadingCol(col); setApiError(null)
    const res = await newsApi.uploadNewsFile(Number(id), kind, slot, file)
    if (res.success) { await refreshMedia() } else { setApiError(res.message || t('err_upload')) }
    setUploadingCol(null)
  }
  async function removeMedia(kind: newsApi.MediaKind, slot: 1 | 2 | 3) {
    if (isNew || !id) return
    const col = newsApi.mediaColumn(kind, slot)
    setUploadingCol(col); setApiError(null)
    const res = await newsApi.removeNewsFile(Number(id), kind, slot)
    if (res.success) { await refreshMedia() } else { setApiError(res.message || t('err_delete')) }
    setUploadingCol(null)
  }

  const isPublished  = form.status === '1'
  const statusClass  = isPublished
    ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
    : 'bg-amber-500/10 text-amber-600 border-amber-500/20'
  const statusDot    = isPublished ? 'bg-emerald-500' : 'bg-amber-400'

  // Champs en erreur (validation client) → listés dans la bannière, avec leur libellé humain.
  const validationIssues: FormIssue[] = [
    ...(errors.title  ? [{ label: t('col_title'), message: errors.title  }] : []),
    ...(errors.siteId ? [{ label: t('site'),      message: errors.siteId }] : []),
  ]

  if (loading) {
    return (
      <div className="flex min-h-[40vh] items-center justify-center">
        <Loader2 className="size-5 animate-spin text-muted-foreground" />
      </div>
    )
  }

  return (
    <div className="flex flex-col">

      {/* Sticky header — le titre/retour vit dans la barre de sous-onglets (NewsPage) ;
          ici on ne garde que les actions (Preview / statut / Save). */}
      <header className="sticky top-0 z-10 flex items-center justify-end border-b border-border bg-background/95 px-5 py-2.5 backdrop-blur-sm">
        <div className={cn('flex items-center gap-2', narrow && 'flex-wrap justify-end')}>
          {previewUrl && (
            <Button
              variant="ghost"
              size="sm"
              className="h-8 gap-1.5 text-muted-foreground text-xs"
              title={t('preview')}
              onClick={() => window.open(previewUrl, '_blank')}
            >
              <Eye className="size-3.5" />
              {!narrow && t('preview')}
            </Button>
          )}

          {/* Indicateur de statut — affichage seul (le changement de statut se fait via le
              toggle de la section STATUS, ci-dessous). Pas de onClick ici. */}
          <span
            className={cn(
              'inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1.5 text-xs font-medium select-none',
              statusClass,
            )}
          >
            <span className={cn('size-1.5 rounded-full', statusDot)} />
            {isPublished ? t('published') : t('unpublished')}
          </span>

          {canSave && (
            <Button size="sm" className="h-8 gap-1.5 min-w-[88px] text-xs" onClick={handleSave} disabled={saving}>
              {saving
                ? <><Loader2 className="size-3.5 animate-spin" />{t('saving')}</>
                : <><Save className="size-3.5" />{t('save')}</>}
            </Button>
          )}
        </div>
      </header>

      {/* Two-column body — stacked on narrow (fixed-width aside would otherwise squeeze main
          content down to near-zero width, which is what caused the mangled mobile layout). */}
      <div className={narrow ? 'flex flex-col' : 'flex items-start'}>

        {/* Main content */}
        <main className={cn('flex-1 min-w-0 space-y-6', narrow ? 'px-4 py-4' : 'px-8 py-6')}>

          {/* Bannière d'erreur unifiée — résumé scannable en haut du formulaire : liste les champs
              obligatoires manquants (validation client) OU l'erreur serveur d'enregistrement.
              Le surlignage rouge inline des champs (titre) est conservé en plus. */}
          {(validationIssues.length > 0 || apiError) && (
            validationIssues.length > 0
              ? <FormErrorBanner title={t('check_required')} issues={validationIssues} />
              : <FormErrorBanner title={apiError ?? undefined} />
          )}

          {/* Language switcher */}
          <div className={cn('flex items-center gap-1 rounded-lg bg-muted p-1 w-fit', narrow && 'flex-wrap')}>
            {languages.length > 0
              ? languages.map((lang) => (
                  <button
                    key={lang.id}
                    type="button"
                    onClick={() => void switchLang(lang.id)}
                    disabled={langBusy}
                    className={cn(
                      'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-all select-none',
                      langId === lang.id
                        ? 'bg-background text-foreground shadow-sm'
                        : 'text-muted-foreground hover:text-foreground',
                    )}
                  >
                    <Flag locale={lang.locale} />
                    {lang.name}
                  </button>
                ))
              : ['English', 'Français'].map((n) => (
                  <span key={n} className="px-3 py-1.5 text-xs text-muted-foreground">{n}</span>
                ))
            }
          </div>

          {/* Title */}
          <div>
            <input
              value={form.title}
              onChange={(e) => set('title', e.target.value)}
              placeholder={t('article_title_ph')}
              autoFocus={isNew}
              className={cn(
                'w-full bg-transparent text-[1.65rem] font-bold tracking-tight leading-tight placeholder:text-muted-foreground/40 focus:outline-none border-b-2 border-transparent transition-colors pb-1',
                errors.title ? 'border-destructive' : 'focus:border-border',
              )}
            />
            {errors.title && <p className="mt-1 text-xs text-destructive">{errors.title}</p>}
          </div>

          {/* Subtitle */}
          <input
            value={form.subtitle}
            onChange={(e) => set('subtitle', e.target.value)}
            placeholder={t('subtitle_ph')}
            className="w-full bg-transparent text-[1.05rem] text-muted-foreground placeholder:text-muted-foreground/40 focus:outline-none border-b border-transparent focus:border-border transition-colors pb-1"
          />

          {/* Body */}
          <section className="space-y-4">
            <div className="flex items-center gap-3">
              <span className="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground">{t('body')}</span>
              <div className="h-px flex-1 bg-border" />
              {/* Choix d'éditeur TipTap/TinyMCE retiré — on force TinyMCE (config 'tool'). Conservé
                  en commentaire au cas où on voudrait re-proposer le switch.
              <div className="flex items-center gap-1 rounded-md bg-muted p-0.5">
                {(['tiptap', 'tinymce'] as RichEditorEngine[]).map((eng) => (
                  <button
                    key={eng}
                    type="button"
                    onClick={() => setEditorEngine(eng)}
                    className={cn(
                      'rounded px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide transition-colors select-none',
                      editorEngine === eng
                        ? 'bg-background text-foreground shadow-sm'
                        : 'text-muted-foreground hover:text-foreground',
                    )}
                  >
                    {eng}
                  </button>
                ))}
              </div>
              */}
              <span className="text-[11px] text-muted-foreground/60">{form.paragraphs.length}/{MAX_PARAGRAPHS}</span>
            </div>

            {form.paragraphs.map((p, i) => (
              <ParagraphEditor
                key={i}
                index={i}
                value={p}
                onChange={(v) => updateParagraph(i, v)}
                onRemove={() => removeParagraph(i)}
                canRemove={form.paragraphs.length > 1}
                canReorder={form.paragraphs.length > 1}
                isDragging={dragIndex === i}
                dragActive={dragIndex !== null}
                isDropTarget={dragIndex !== null && overIndex === i && dragIndex !== i}
                onDragStart={() => setDragIndex(i)}
                onDragOverCard={() => { if (overIndex !== i) setOverIndex(i) }}
                // Le déplacement N'EST PAS déclenché par l'événement `drop` : au-dessus de l'éditeur
                // TinyMCE (une <iframe>) le `drop` est absorbé/incohérent. On le fait dans `dragend`,
                // qui se déclenche TOUJOURS sur la carte glissée, en visant `overIndex` — la carte
                // surlignée. Réordonnancement type Jira : moveParagraph décale toutes les cartes
                // entre l'ancienne et la nouvelle position. Garantit « surligné = nouvelle position ».
                onDrop={() => { /* voir onDragEnd */ }}
                onDragEnd={() => {
                  if (dragIndex !== null && overIndex !== null) moveParagraph(dragIndex, overIndex)
                  setDragIndex(null); setOverIndex(null)
                }}
                extraActions={window.__melisNewsExtensions?.renderParagraphActions?.(
                  i,
                  (text) => updateParagraph(i, text),
                  {
                    newsId: isNew ? undefined : id,
                    title: form.title,
                    subtitle: form.subtitle,
                    paragraphs: form.paragraphs,
                    images: [form.image1, form.image2, form.image3].filter((x): x is string => x !== null),
                  }
                )}
              />
            ))}

            {form.paragraphs.length < MAX_PARAGRAPHS && (
              <button
                type="button"
                onClick={addParagraph}
                className="flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-border py-3 text-xs font-medium text-muted-foreground hover:border-primary/60 hover:text-primary transition-colors"
              >
                <Plus className="size-3.5" />
                {t('add_paragraph')} <span className="text-muted-foreground/60">(max. {MAX_PARAGRAPHS})</span>
              </button>
            )}
          </section>

          {/* Media */}
          <section className="space-y-4">
            <div className="flex items-center gap-3">
              <span className="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground">{t('media')}</span>
              <div className="h-px flex-1 bg-border" />
            </div>

            {/* L'upload cible un article existant → invite à enregistrer d'abord si nouveau. */}
            {isNew && (
              <p className="rounded-lg border border-dashed border-border bg-muted/20 px-4 py-3 text-xs text-muted-foreground">
                {t('media_save_first')}
              </p>
            )}

            <div className={cn('space-y-3', isNew && 'pointer-events-none opacity-50')}>
              <p className="text-[11px] font-medium text-muted-foreground uppercase tracking-wide">
                {t('images')} <span className="normal-case text-muted-foreground/70">({t('media_max')})</span>
              </p>
              <div className={cn('grid gap-3', narrow ? 'grid-cols-1' : 'grid-cols-3')}>
                {([1, 2, 3] as const).map((slot) => {
                  const col = newsApi.mediaColumn('image', slot)
                  return (
                    <ImageSlot key={col} label={t('image_n', { n: slot })} src={form[`image${slot}` as 'image1' | 'image2' | 'image3']}
                      disabled={isNew} uploading={uploadingCol === col}
                      onPick={(f) => uploadMedia('image', slot, f)} onRemove={() => removeMedia('image', slot)} />
                  )
                })}
              </div>
            </div>

            <div className={cn('space-y-3', isNew && 'pointer-events-none opacity-50')}>
              <p className="text-[11px] font-medium text-muted-foreground uppercase tracking-wide">
                {t('file_attachments')} <span className="normal-case text-muted-foreground/70">({t('media_max')})</span>
              </p>
              <div className="space-y-2">
                {([1, 2, 3] as const).map((slot) => {
                  const col = newsApi.mediaColumn('file', slot)
                  return (
                    <FileSlot key={col} src={form[`document${slot}` as 'document1' | 'document2' | 'document3']}
                      disabled={isNew} uploading={uploadingCol === col}
                      onPick={(f) => uploadMedia('file', slot, f)} onRemove={() => removeMedia('file', slot)} />
                  )
                })}
              </div>
            </div>
          </section>

          {/* Comments — modération native (module optionnel MelisCmsComments). Affiché ssi le
              module est actif ; pour un nouvel article (pas d'id) on invite à enregistrer d'abord. */}
          {(commentsActive || isNew) && (
            <section className="space-y-4">
              <div className="flex items-center gap-3">
                <span className="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground">{t('comments')}</span>
                <div className="h-px flex-1 bg-border" />
                {commentsActive && comments.length > 0 && (
                  <span className="text-[11px] font-medium text-muted-foreground">{comments.length}</span>
                )}
              </div>

              {isNew ? (
                <p className="rounded-lg border border-dashed border-border bg-muted/20 px-4 py-3 text-xs text-muted-foreground">
                  {t('comment_save_first')}
                </p>
              ) : (
                <>
                  {/* Add a comment (back-office → validé d'office) */}
                  <div className={cn('flex gap-2 rounded-lg border border-border bg-muted/10 p-3', narrow ? 'flex-col' : 'flex-row items-start')}>
                    <Input
                      value={newComment.name}
                      onChange={(e) => setNewComment((c) => ({ ...c, name: e.target.value }))}
                      placeholder={t('comment_name_ph')}
                      className={cn('h-9', !narrow && 'w-40')}
                    />
                    <textarea
                      value={newComment.text}
                      onChange={(e) => setNewComment((c) => ({ ...c, text: e.target.value }))}
                      placeholder={t('comment_text_ph')}
                      rows={1}
                      className="min-h-9 flex-1 resize-y rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                    />
                    <Button
                      size="sm"
                      className={cn('h-9 gap-1.5', narrow ? 'self-end' : 'self-start')}
                      onClick={addComment}
                      disabled={commentBusy || !newComment.text.trim()}
                    >
                      <Plus className="size-3.5" />
                      {t('comment_add')}
                    </Button>
                  </div>

                  {comments.length === 0 ? (
                    <p className="rounded-lg border border-dashed border-border bg-muted/20 px-4 py-6 text-center text-xs text-muted-foreground">
                      {t('no_comments')}
                    </p>
                  ) : (
                    <>
                      <ul className="space-y-2">
                        {pagedComments.map((c) => (
                          <CommentRow key={c.id} c={c} busy={commentBusy} onModerate={moderateComment} appLang={appLang} />
                        ))}
                      </ul>
                      <Pager page={commentsPage} totalPages={commentTotalPages} onChange={setCommentsPage} />
                    </>
                  )}
                </>
              )}
            </section>
          )}

        </main>

        {/* Sidebar — full width, stacked below main, no sticky positioning on narrow (sticky top
            makes no sense once this is no longer a side column). */}
        <aside className={narrow
          ? 'w-full border-t border-border bg-muted/10 p-4 space-y-4'
          : 'w-64 shrink-0 self-start sticky top-[57px] border-l border-border bg-muted/10 p-4 space-y-4'}>

          <SidebarSection title={t('status')} icon={GitBranch}>
            <div className="flex items-center justify-between gap-2">
              {/* Switch published/unpublished — vert = publié (ON), rouge = non publié (OFF). */}
              <button
                type="button"
                role="switch"
                aria-checked={isPublished}
                onClick={() => set('status', isPublished ? '0' : '1')}
                className="inline-flex cursor-pointer items-center gap-2 select-none"
              >
                <span
                  className={cn(
                    'relative inline-flex h-5 w-9 shrink-0 items-center rounded-full transition-colors',
                    isPublished ? 'bg-emerald-500' : 'bg-red-500',
                  )}
                >
                  <span
                    className={cn(
                      'inline-block size-4 rounded-full bg-white shadow transition-transform',
                      isPublished ? 'translate-x-[18px]' : 'translate-x-0.5',
                    )}
                  />
                </span>
                <span className="text-xs font-medium text-foreground">
                  {isPublished ? t('published') : t('unpublished')}
                </span>
              </button>
              {/* Le workflow de validation est apporté par MelisSmallBusiness — bouton masqué si inactif.
                  Désactivé tant que l'article n'est pas enregistré (pas d'id à rattacher au workflow). */}
              {sbActive && (
                <Button
                  variant="outline"
                  size="sm"
                  className="h-7 gap-1.5 text-xs px-2.5"
                  onClick={() => setWfOpen(true)}
                  disabled={isNew}
                  title={isNew ? t('save_first_short') : undefined}
                >
                  <GitBranch className="size-3" />
                  {t('workflow')}
                </Button>
              )}
            </div>
          </SidebarSection>

          <SidebarSection title={t('publication')} icon={Calendar}>
            <div>
              <label className="mb-1 block text-[11px] text-muted-foreground">{t('publish_on')}</label>
              <DateTimeField
                value={form.publishDate}
                onChange={(v) => set('publishDate', v)}
                locale={appLang}
              />
              {form.publishDate && <p className="mt-1 text-[11px] text-muted-foreground/80">{formatDatePreview(form.publishDate, appLang)}</p>}
            </div>
            <div>
              <label className="mb-1 block text-[11px] text-muted-foreground">{t('unpublish_on')}</label>
              <DateTimeField
                value={form.unpublishDate}
                onChange={(v) => set('unpublishDate', v)}
                locale={appLang}
              />
              {form.unpublishDate && <p className="mt-1 text-[11px] text-muted-foreground/80">{formatDatePreview(form.unpublishDate, appLang)}</p>}
            </div>
          </SidebarSection>

          <SidebarSection title={t('site')} icon={Globe}>
            <select
              value={form.siteId}
              onChange={(e) => set('siteId', e.target.value)}
              className={cn(
                'h-8 w-full rounded-md border border-input bg-card px-2.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring',
                errors.siteId && 'border-destructive',
              )}
            >
              <option value="">{t('choose_site')}</option>
              {sites.map((s) => (
                <option key={s.id} value={String(s.id)}>{s.name}</option>
              ))}
            </select>
            {errors.siteId && <p className="mt-1 text-xs text-destructive">{errors.siteId}</p>}
          </SidebarSection>

          {/* Auteur : modulaire — n'apparaît que si le module MelisCmsUserAccount est actif
              (la route /melis/react-api/news/users répond ; sinon la section est masquée). */}
          {userAccountActive && (
            <SidebarSection title={t('author')} icon={User}>
              <select
                value={form.authorId}
                onChange={(e) => set('authorId', e.target.value)}
                className="h-8 w-full rounded-md border border-input bg-card px-2.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
              >
                <option value="">{t('choose')}</option>
                {users.map((u) => (
                  <option key={u.id} value={String(u.id)}>{u.name}</option>
                ))}
              </select>
            </SidebarSection>
          )}

          {/* Validation des commentaires (module optionnel MelisCmsComments) — persiste
              cnews_validate_comments. Affiché ssi le module est actif (route /comments répond). */}
          {commentsActive && (
            <SidebarSection title={t('comments_validation')} icon={MessageSquare}>
              <button
                type="button"
                role="switch"
                aria-checked={form.validateComments}
                onClick={() => set('validateComments', !form.validateComments)}
                className="inline-flex cursor-pointer items-center gap-2 select-none"
              >
                <span
                  className={cn(
                    'relative inline-flex h-5 w-9 shrink-0 items-center rounded-full transition-colors',
                    form.validateComments ? 'bg-emerald-500' : 'bg-muted-foreground/40',
                  )}
                >
                  <span
                    className={cn(
                      'inline-block size-4 rounded-full bg-white shadow transition-transform',
                      form.validateComments ? 'translate-x-[18px]' : 'translate-x-0.5',
                    )}
                  />
                </span>
                <span className="text-xs font-medium text-foreground">
                  {form.validateComments ? t('filter_active') : t('filter_inactive')}
                </span>
              </button>
              <p className="mt-1.5 text-[11px] leading-snug text-muted-foreground">{t('comments_validation_hint')}</p>
            </SidebarSection>
          )}

          <SidebarSection title={t('seo')} icon={Search} collapsible defaultOpen={false}>
            {(
              [
                { key: 'metaTitle',       label: t('meta_title'),       type: 'input' },
                { key: 'metaDescription', label: t('meta_description'),  type: 'textarea' },
                { key: 'url',             label: t('url'),               type: 'input' },
                { key: 'urlRedirect',     label: t('url_redirect'),      type: 'input' },
                { key: 'url301',          label: t('url_301'),           type: 'input' },
                { key: 'canonical',       label: t('canonical_url'),     type: 'input' },
              ] as { key: keyof newsApi.NewsSeo; label: string; type: 'input' | 'textarea' }[]
            ).map(({ key, label, type }) => (
              <div key={key}>
                <label className="mb-1 block text-[11px] text-muted-foreground">{label}</label>
                {type === 'textarea' ? (
                  <textarea
                    value={form.seo[key]}
                    onChange={(e) => setSeo(key, e.target.value)}
                    rows={2}
                    className="w-full resize-none rounded-md border border-input bg-card px-2.5 py-1.5 text-xs placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-ring"
                  />
                ) : (
                  <Input
                    value={form.seo[key]}
                    onChange={(e) => setSeo(key, e.target.value)}
                    className="h-7 text-xs"
                  />
                )}
              </div>
            ))}
          </SidebarSection>

          {/* Catégories : partie MODULAIRE apportée par l'outil « categorie-v2 » (melis-cms-category2,
              migré en React) — placée SOUS le SEO (module de base), comme le Slider. La section
              n'apparaît QUE si ce module est actif (détecté via /react-modules). Le back-office news
              lit/écrit déjà melis_cms_category2 + la table de liaison melis_cms_news_category. */}
          {categoryActive && (
            <SidebarSection
              title={t('categories')}
              icon={FolderTree}
              collapsible
              defaultOpen={false}
            >
              {categories.length === 0 ? (
                <p className="text-xs text-muted-foreground">
                  {t('no_category')}
                </p>
              ) : (
                <div className="max-h-64 space-y-0.5 overflow-auto pr-1">
                  <CategoryTree categories={categories} selected={form.categoryIds} onToggle={toggleCategory} />
                </div>
              )}
            </SidebarSection>
          )}

          {/* Tags : partie MODULAIRE apportée par l'outil « Tags » (melis-cms-tags). Même mécanisme
              que les Catégories — la section n'apparaît QUE si MelisCmsTags est actif (détecté via
              /react-modules). Le back-office news lit melis_cms_tag et écrit la table de liaison
              partagée melis_cms_tag_entity (entity_type = 'NEWS'). */}
          {tagsActive && (
            <SidebarSection
              title={t('tags')}
              icon={Tag}
              collapsible
              defaultOpen={false}
            >
              {tags.length === 0 ? (
                <p className="text-xs text-muted-foreground">
                  {t('no_tag')}
                </p>
              ) : (
                <div className="max-h-64 space-y-0.5 overflow-auto pr-1">
                  {tags.map((tg) => (
                    <label
                      key={tg.id}
                      className="flex items-center gap-2 py-0.5 text-xs text-foreground cursor-pointer hover:text-primary"
                    >
                      <input
                        type="checkbox"
                        checked={form.tagIds.includes(tg.id)}
                        onChange={() => toggleTag(tg.id)}
                        className="size-3.5 shrink-0 rounded border-input accent-primary"
                      />
                      <span className="truncate">{tg.name || `#${tg.id}`}</span>
                    </label>
                  ))}
                </div>
              )}
            </SidebarSection>
          )}

          {/* Slider : modulaire — l'outil Slider EST migré, donc on l'affiche SEULEMENT s'il est actif
              (la route /melis/react-api/sliders répond ; sinon la section est masquée). Liste dynamique. */}
          {sliderActive && (
            <SidebarSection title={t('slider')} icon={SlidersHorizontal} collapsible defaultOpen={false}>
              <select
                value={form.sliderId}
                onChange={(e) => set('sliderId', e.target.value)}
                className="h-8 w-full rounded-md border border-input bg-card px-2.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
              >
                <option value="">{t('no_slider')}</option>
                {sliders.map((s) => (
                  <option key={s.id} value={String(s.id)}>{s.name}</option>
                ))}
              </select>
            </SidebarSection>
          )}
        </aside>
      </div>

      {/* Preview — rendu tout à la fin, en pleine largeur SOUS main + sidebar (et donc après la
          sidebar en vue étroite où elle se replie en dessous), et non plus dans la colonne main. */}
      <div className={cn('border-t border-border', narrow ? 'px-4 py-4' : 'px-8 py-6')}>
        <PreviewTab newsId={newsId} isNew={isNew} />
      </div>

      {/* Modale Workflow (validation) — MUTUALISÉE : composant fourni par MelisSmallBusiness via
          window.__melisWorkflowModal (même modale que l'éditeur de page CMS). Contexte type NEWS. */}
      {wfOpen && !isNew && (() => {
        const WF = (window as unknown as { __melisWorkflowModal?: React.ComponentType<{ ctx: { wfType: string; wfId: number | string; wfDetails: string; wfOpeningJs: string }; appLang: string; onClose: () => void }> }).__melisWorkflowModal
        if (!WF) return null
        const title = (form.title || `#${newsId}`).replace(/'/g, '')
        return (
          <WF
            ctx={{ wfType: 'NEWS', wfId: newsId as number, wfDetails: `${title} (${newsId})`, wfOpeningJs: `melisHelper.tabOpen('${title}', 'fa fa-rss fa-2x', '${newsId}_id_meliscmsnews_page', 'meliscmsnews_page', { newsId: ${newsId} });` }}
            appLang={appLang}
            onClose={() => setWfOpen(false)}
          />
        )
      })()}

      {/* Confirmation de suppression d'un commentaire (remplace window.confirm). */}
      <ConfirmDialog
        open={!!pendingDelete}
        title={t('comment_delete_confirm')}
        description={
          (pendingDelete?.name ? `${t('comment_delete_by', { name: pendingDelete.name })} — ` : '') +
          t('comment_delete_desc')
        }
        confirmLabel={t('delete')}
        cancelLabel={t('cancel')}
        busy={commentBusy}
        onConfirm={confirmDelete}
        onCancel={() => setPendingDelete(null)}
      />
    </div>
  )
}
