import { useState, useEffect, useCallback, useMemo, useRef } from 'react'
import {
  Save, Loader2, ChevronDown, ChevronUp,
  Plus, X, Eye, Calendar, Globe, Tag, Search, SlidersHorizontal,
  GitBranch, Image, Paperclip, GripVertical, FolderTree,
} from 'lucide-react'

import { Button } from './components/ui/button'
import { Input } from './components/ui/input'
import { RichEditor, type RichEditorEngine } from './components/ui/rich-editor'
import WorkflowModal from './components/WorkflowModal'
import { cn } from './lib/utils'
import { t } from './lib/i18n'
import * as newsApi from './lib/news-api'
import { newsWorkflowContext } from './lib/workflow-api'
import { useCaps } from './shared/useCaps'

// melisKey de l'outil Actualités — clé des capacités (cf. config/react.capabilities.php)
const NEWS_MELIS_KEY = 'meliscmsnews_left_menu'

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
  categoryIds: number[]
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
  sliderId: '', categoryIds: [], seo: EMPTY_SEO,
  image1: null, image2: null, image3: null,
  document1: null, document2: null, document3: null,
}

const MAX_PARAGRAPHS = 10  // cnews_paragraph1-10 in DB

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
  const nativeRef = useRef<HTMLInputElement>(null)

  // Resynchronise l'affichage quand la valeur change de l'extérieur (chargement, changement de langue).
  useEffect(() => { setText(dtToDisplay(value, dayFirst)) }, [value, dayFirst])

  const commit = () => {
    const iso = dtFromDisplay(text, dayFirst)
    if (iso === null) { setText(dtToDisplay(value, dayFirst)); return } // saisie invalide → on revient à la valeur
    onChange(iso)
  }
  const openPicker = () => {
    const el = nativeRef.current as (HTMLInputElement & { showPicker?: () => void }) | null
    if (el?.showPicker) el.showPicker()
    else el?.focus()
  }

  return (
    <div className="relative">
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
        onClick={openPicker}
        tabIndex={-1}
        aria-label={t('calendar')}
        className="absolute right-1.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
      >
        <Calendar className="size-3.5" />
      </button>
      {/* Input natif caché : fournit le calendrier via showPicker(), valeur au format datetime-local. */}
      <input
        ref={nativeRef}
        type="datetime-local"
        value={value}
        onChange={(e) => onChange(e.target.value)}
        tabIndex={-1}
        aria-hidden="true"
        className="pointer-events-none absolute size-0 opacity-0"
      />
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
          <span className="truncate">{c.name}</span>
        </label>
        {render(c.id, depth + 1)}
      </div>
    ))

  return <>{render(-1, 0)}</>
}

// ─── Paragraph editor ─────────────────────────────────────────────────────────

function ParagraphEditor({
  index, value, onChange, onRemove, canRemove, engine, extraActions,
  canReorder, isDragging, onDragStart, onDrop, onDragEnd,
}: {
  index: number; value: string; onChange: (v: string) => void
  onRemove: () => void; canRemove: boolean; engine: RichEditorEngine
  extraActions?: React.ReactNode
  canReorder: boolean
  isDragging: boolean
  onDragStart: () => void
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
      onDragOver={(e) => { if (canReorder) e.preventDefault() }}
      onDrop={(e) => { e.preventDefault(); setArmed(false); onDrop() }}
      onDragEnd={() => { setArmed(false); onDragEnd() }}
      className={cn('rounded-lg transition-opacity', isDragging && 'opacity-40')}
    >
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
      <RichEditor
        engine={engine}
        value={value}
        onChange={onChange}
        placeholder={t('editor_ph')}
        minRows={6}
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
    return (
      <div className="group relative overflow-hidden rounded-xl border border-border bg-card">
        <img src={src} alt={label} className="h-32 w-full object-cover" onError={(e) => { (e.currentTarget as HTMLImageElement).src = '' }} />
        <div className="absolute inset-0 flex items-center justify-center gap-2 bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
          <span className="text-xs font-medium text-white">{label}</span>
          <button type="button" onClick={onRemove} disabled={uploading}
            className="rounded-md bg-white/15 px-2 py-1 text-[11px] font-medium text-white hover:bg-red-500/80">
            {uploading ? '…' : t('remove')}
          </button>
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

type NewsItemCache = { form: FormState; langId: number }
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

  // Capacités : droit d'enregistrer = create (nouvel article) ou edit (existant).
  const { can } = useCaps(NEWS_MELIS_KEY)
  const canSave = isNew ? can('create') : can('edit')

  const [languages, setLanguages]       = useState<newsApi.Language[]>([])
  const [sites, setSites]               = useState<newsApi.Site[]>([])
  const [sliders, setSliders]           = useState<newsApi.Slider[]>([])
  const [sliderActive, setSliderActive] = useState(false)   // vrai ssi le module Slider est actif
  const [sbActive, setSbActive]         = useState(false)   // vrai ssi MelisSmallBusiness est actif (→ bouton Workflow)
  const [wfOpen, setWfOpen]             = useState(false)   // modale Workflow (validation) ouverte
  const [uploadingCol, setUploadingCol] = useState<string | null>(null) // colonne média en cours d'upload/suppression
  const [categories, setCategories]     = useState<newsApi.NewsCategory[]>([])
  const [categoryActive, setCategoryActive] = useState(false) // vrai ssi MelisCmsCategory2 est actif (→ section Catégories)
  const [dragIndex, setDragIndex]       = useState<number | null>(null)  // paragraphe en cours de glisser
  const [previewUrl, setPreviewUrl]     = useState<string | null>(null)
  const [editorEngine, setEditorEngine] = useState<RichEditorEngine>('tiptap')

  // Langue de l'app (chrome hôte) → format de date fr/en.
  const appLang = (document.documentElement.lang || 'en').slice(0, 2)

  const [langId, setLangId]   = useState<number>(1)
  const [form, setForm]       = useState<FormState>(EMPTY)
  const [loading, setLoading] = useState(false)
  const [saving, setSaving]   = useState(false)
  const [apiError, setApiError] = useState<string | null>(null)
  const [errors, setErrors]   = useState<Partial<Record<string, string>>>({})

  const loadNews = useCallback(async (newsId: number, targetLangId: number) => {
    try {
      const d = await newsApi.fetchNewsById(newsId, targetLangId)
      setForm({
        title:         d.title    ?? '',
        subtitle:      d.subtitle ?? '',
        paragraphs:    d.paragraphs && d.paragraphs.length ? d.paragraphs : [''],
        status:        d.status === 1 ? '1' : '0',
        siteId:        d.siteId ? String(d.siteId) : '',
        publishDate:   toInputDate(d.publishDate),
        unpublishDate: toInputDate(d.unpublishDate),
        sliderId:      d.sliderId ? String(d.sliderId) : '',
        categoryIds:   d.categoryIds ?? [],
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
      // Modules optionnels détectés via /react-modules (listé ssi actif) :
      //  - MelisSmallBusiness → bouton « Workflow » de la barre latérale.
      //  - MelisCmsCategory2  → section « Catégories » du formulaire (l'outil categorie-v2 est
      //    désormais migré en React ; les catégories sont chargées par un effet dédié réactif à
      //    la langue une fois `categoryActive` vrai).
      newsApi.fetchActiveModules().then((mods) => {
        setSbActive(mods.includes('MelisSmallBusiness'))
        setCategoryActive(mods.includes('MelisCmsCategory2'))
      }).catch(() => {}),
    ]

    if (!isNew) {
      const cached = _newsCache.get(id!)
      if (cached) {
        setForm(cached.form)
        setLangId(cached.langId)
      } else {
        tasks.push(loadNews(Number(id), langId))
      }
      tasks.push(newsApi.fetchNewsPreviewUrl(Number(id)).then(setPreviewUrl).catch(() => {}))
    }

    Promise.all(tasks).finally(() => setLoading(false))
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id, isNew])

  useEffect(() => {
    if (!isNew && id && form.title) {
      _newsCache.set(id, { form, langId })
    }
  }, [form, langId, id, isNew])

  useEffect(() => {
    if (!isNew && !loading) {
      // Recharge l'article dans la langue sélectionnée.
      loadNews(Number(id), langId)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [langId])

  // Catégories (module optionnel MelisCmsCategory2) : chargées ssi le module est actif, et
  // re-fetchées quand la langue du BO change (les noms de catégories sont traduits par langue ;
  // la SÉLECTION `categoryIds`, elle, est indépendante de la langue → conservée).
  useEffect(() => {
    if (!categoryActive) return
    newsApi.fetchCategories(langId).then(setCategories).catch(() => {})
  }, [langId, categoryActive])

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

  function validate() {
    const errs: Partial<Record<string, string>> = {}
    if (!form.title.trim()) errs.title  = t('title_required')
    if (!form.siteId)       errs.siteId = t('site_required')
    setErrors(errs)
    return Object.keys(errs).length === 0
  }

  async function handleSave() {
    if (!validate()) return
    setSaving(true); setApiError(null)
    try {
      const payload: newsApi.NewsSavePayload = {
        id:            isNew ? null : Number(id),
        langId,
        title:         form.title.trim(),
        subtitle:      form.subtitle.trim() || undefined,
        paragraphs:    form.paragraphs,
        status:        form.status === '1' ? 1 : 0,
        siteId:        Number(form.siteId),
        publishDate:   toDbDate(form.publishDate),
        unpublishDate: toDbDate(form.unpublishDate),
        sliderId:    form.sliderId ? Number(form.sliderId) : null,
        categoryIds: form.categoryIds,
        seo:         form.seo,
      }
      const res = await newsApi.saveNews(payload)
      window.postMessage({ __melisNotif: true, kind: 'ok', title: 'News', message: t('saved_ok') }, '*')
      // Le conteneur (NewsPage) convertit l'onglet « new » en onglet de l'article créé,
      // ou met à jour le libellé d'un article existant.
      onSaved(res.id, form.title.trim() || `Article #${res.id}`)
    } catch (e) {
      setApiError(e instanceof Error ? e.message : t('err_save'))
    } finally {
      setSaving(false)
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
        <div className="flex items-center gap-2">
          {previewUrl && (
            <Button
              variant="ghost"
              size="sm"
              className="h-8 gap-1.5 text-muted-foreground text-xs"
              onClick={() => window.open(previewUrl, '_blank')}
            >
              <Eye className="size-3.5" />
              {t('preview')}
            </Button>
          )}

          <button
            type="button"
            onClick={() => set('status', isPublished ? '0' : '1')}
            className={cn(
              'inline-flex cursor-pointer items-center gap-1.5 rounded-md border px-2.5 py-1.5 text-xs font-medium transition-colors select-none',
              statusClass,
            )}
          >
            <span className={cn('size-1.5 rounded-full', statusDot)} />
            {isPublished ? t('published') : t('unpublished')}
          </button>

          {apiError && <span className="text-xs text-destructive">{apiError}</span>}

          {canSave && (
            <Button size="sm" className="h-8 gap-1.5 min-w-[88px] text-xs" onClick={handleSave} disabled={saving}>
              {saving
                ? <><Loader2 className="size-3.5 animate-spin" />{t('saving')}</>
                : <><Save className="size-3.5" />{t('save')}</>}
            </Button>
          )}
        </div>
      </header>

      {/* Two-column body */}
      <div className="flex items-start">

        {/* Main content */}
        <main className="flex-1 min-w-0 px-8 py-6 space-y-6">

          {/* Language switcher */}
          <div className="flex items-center gap-1 rounded-lg bg-muted p-1 w-fit">
            {languages.length > 0
              ? languages.map((lang) => (
                  <button
                    key={lang.id}
                    type="button"
                    onClick={() => setLangId(lang.id)}
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
                onDragStart={() => setDragIndex(i)}
                onDrop={() => { if (dragIndex !== null) moveParagraph(dragIndex, i); setDragIndex(null) }}
                onDragEnd={() => setDragIndex(null)}
                engine={editorEngine}
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
                {t('add_paragraph')}
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
              <p className="text-[11px] font-medium text-muted-foreground uppercase tracking-wide">{t('images')}</p>
              <div className="grid grid-cols-3 gap-3">
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
              <p className="text-[11px] font-medium text-muted-foreground uppercase tracking-wide">{t('file_attachments')}</p>
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
        </main>

        {/* Sidebar */}
        <aside className="w-64 shrink-0 self-start sticky top-[57px] border-l border-border bg-muted/10 p-4 space-y-4">

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
                'h-8 w-full rounded-md border border-input bg-background px-2.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring',
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
                    className="w-full resize-none rounded-md border border-input bg-background px-2.5 py-1.5 text-xs placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-ring"
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

          {/* Slider : modulaire — l'outil Slider EST migré, donc on l'affiche SEULEMENT s'il est actif
              (la route /melis/react-api/sliders répond ; sinon la section est masquée). Liste dynamique. */}
          {sliderActive && (
            <SidebarSection title={t('slider')} icon={SlidersHorizontal} collapsible defaultOpen={false}>
              <select
                value={form.sliderId}
                onChange={(e) => set('sliderId', e.target.value)}
                className="h-8 w-full rounded-md border border-input bg-background px-2.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
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

      {/* Modale Workflow (validation) — apportée par MelisSmallBusiness, cf. bouton du bloc Status. */}
      {wfOpen && !isNew && (
        <WorkflowModal
          ctx={newsWorkflowContext(newsId as number, form.title)}
          appLang={appLang}
          onClose={() => setWfOpen(false)}
        />
      )}
    </div>
  )
}
