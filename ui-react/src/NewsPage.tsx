import { useState, useEffect } from 'react'
import { useLocation } from 'react-router-dom'
import NewsListPage from './NewsListPage'
import NewsFormPage from './NewsFormPage'
import { t } from './lib/i18n'

/**
 * Conteneur de l'outil Actualités (brique MelisCmsNews), monté une fois par le shell sur
 * l'onglet « Actualités ». Reproduit le système de SOUS-ONGLETS de l'outil Slider (et des
 * Utilisateurs/Sites) : on reste sur l'unique onglet de shell « Actualités » et on affiche
 * une barre de sous-onglets DANS l'outil (← Retour + un onglet par article ouvert) — PLUS
 * de `__melisOpenTab` (qui créait un onglet général du shell). Chaque article ouvert garde
 * son NewsFormPage monté (état préservé) → navigation instantanée entre articles.
 */

type EditId = number | 'new'
type View = { kind: 'list' } | { kind: 'edit'; id: EditId }
interface OpenTab { id: EditId; name: string }

/**
 * Reflète le sous-onglet actif dans l'URL : /[section]/[tool]/:id (ou /new), comme Utilisateurs.
 * COSMÉTIQUE (history.replaceState) — PAS de navigation React Router (pattern sous-onglets in-tool,
 * état local). Le host (ToolTabBar) ne réécrit pas l'URL de cet outil (SELF_MANAGED_URL).
 */
function reflectSubTabUrl(seg: string | number | null) {
  const base = window.location.pathname.replace(/\/(?:new|\d+)$/, '')
  const next = seg != null && seg !== '' ? `${base}/${seg}` : base
  if (window.location.pathname !== next) window.history.replaceState(window.history.state, '', next)
}

const NewsIcon = () => (
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ flexShrink: 0 }}>
    <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2" />
    <path d="M18 14h-8M15 18h-5M10 6h8v4h-8V6Z" />
  </svg>
)

function SubTabBar({ tabs, activeId, onBack, onSelect, onClose }: {
  tabs: OpenTab[]; activeId: EditId | null; onBack: () => void; onSelect: (id: EditId) => void; onClose: (id: EditId) => void
}) {
  const back = t('back')
  return (
    <div style={{ display: 'flex', alignItems: 'stretch', borderBottom: '1px solid var(--color-border,#e5e7eb)', background: 'var(--color-background,#fff)', padding: '0 8px', overflowX: 'auto', flexShrink: 0 }}>
      <button onClick={onBack}
        style={{ marginRight: 4, flexShrink: 0, display: 'inline-flex', alignItems: 'center', padding: '6px 8px', fontSize: 12, color: 'var(--color-muted-foreground,#6b7280)', background: 'transparent', border: 0, cursor: 'pointer', whiteSpace: 'nowrap' }}>
        ← {back}
      </button>
      {tabs.map((tab) => {
        const isActive = activeId === tab.id
        return (
          <div key={String(tab.id)} onClick={() => onSelect(tab.id)}
            style={{ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '0 10px', fontSize: 12, fontWeight: 500, cursor: 'pointer', whiteSpace: 'nowrap', userSelect: 'none',
              borderBottom: isActive ? '2px solid var(--color-primary,#cb4040)' : '2px solid transparent',
              color: isActive ? 'var(--color-foreground)' : 'var(--color-muted-foreground,#6b7280)',
              background: isActive ? 'var(--color-background,#fff)' : 'transparent' }}>
            <NewsIcon />
            <span style={{ maxWidth: 160, overflow: 'hidden', textOverflow: 'ellipsis' }}>{tab.name}</span>
            <button onClick={(e) => { e.stopPropagation(); onClose(tab.id) }}
              style={{ marginLeft: 2, borderRadius: 4, padding: 2, border: 0, background: 'transparent', cursor: 'pointer', color: 'inherit', lineHeight: 0 }} title={back}>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round"><path d="M18 6 6 18M6 6l12 12" /></svg>
            </button>
          </div>
        )
      })}
    </div>
  )
}

export default function NewsPage() {
  const [view, setView] = useState<View>({ kind: 'list' })
  const [open, setOpen] = useState<OpenTab[]>([])

  const newLabel = () => t('new_article')

  function openEditor(id: number, name: string) {
    const label = name || `#${id}`
    // Onglet déjà ouvert : on ne renomme QUE si l'appelant fournit un nom — une réouverture
    // externe sans libellé (cf. effet URL plus bas) ne doit pas écraser le titre par « #8 ».
    setOpen((prev) => (prev.some((o) => o.id === id)
      ? (name ? prev.map((o) => (o.id === id ? { ...o, name: label } : o)) : prev)
      : [...prev, { id, name: label }]))
    setView({ kind: 'edit', id })
  }
  function openNew() {
    setOpen((prev) => (prev.some((o) => o.id === 'new') ? prev : [...prev, { id: 'new', name: newLabel() }]))
    setView({ kind: 'edit', id: 'new' })
  }
  function closeEditor(id: EditId) {
    setOpen((prev) => {
      const rest = prev.filter((o) => o.id !== id)
      setView((v) => (v.kind === 'edit' && v.id === id ? (rest.length ? { kind: 'edit', id: rest[rest.length - 1].id } : { kind: 'list' }) : v))
      return rest
    })
  }
  function renameTab(id: EditId, title: string) {
    const label = title || (id === 'new' ? newLabel() : `#${id}`)
    setOpen((prev) => prev.map((o) => (o.id === id ? { ...o, name: label } : o)))
  }
  // Après sauvegarde : l'onglet « new » devient l'onglet de l'article créé (id réel) ;
  // un article existant voit juste son libellé mis à jour.
  function handleSaved(savedId: number, title: string) {
    const label = title || `#${savedId}`
    setOpen((prev) => {
      if (prev.some((o) => o.id === savedId)) return prev.map((o) => (o.id === savedId ? { ...o, name: label } : o))
      if (prev.some((o) => o.id === 'new')) return prev.map((o) => (o.id === 'new' ? { id: savedId, name: label } : o))
      return [...prev, { id: savedId, name: label }]
    })
    setView({ kind: 'edit', id: savedId })
  }

  // ── Vue « Old » (iframe legacy) ──────────────────────────────────────────────────
  // Elle reste 100% LEGACY : ouvrir un article depuis la liste legacy ouvre le formulaire legacy
  // DANS l'iframe (tabOpen), et la barre d'onglets de l'hôte (ToolTabBar, alimentée par le pont
  // __melisToolTabs) permet de revenir à la liste — comme pour tout autre outil de la plateforme.
  // On ne détourne PLUS ces onglets vers NewsFormPage : c'était le but du toggle de pouvoir
  // comparer les deux interfaces, et le détournement rendait la vue Old inutilisable.

  const activeId = view.kind === 'edit' ? view.id : null

  // ── Ouverture depuis l'EXTÉRIEUR de l'outil : /melis-cms/news/:id ────────────────
  // L'œil du plugin Workflow (et tout autre appelant du pont `__melisOpenTool`) navigue vers
  // /melis-cms/news/<id>. La brique étant PERSISTANTE, elle n'est pas remontée à chaque appel :
  // on lit donc la location scopée (Shell isole le routage de chaque brique) à CHAQUE navigation
  // — `key` change même quand on redemande l'article déjà affiché dans l'URL, ce qui rejoue
  // l'ouverture. Le libellé arrive via l'état de navigation (`melisTabLabel`, posé par App.tsx)
  // → le sous-onglet porte le vrai titre dès le 1er rendu, sans « #8 » transitoire.
  const loc = useLocation()
  useEffect(() => {
    const m = loc.pathname.match(/\/(\d+)$/)
    if (!m) return
    const id = Number(m[1])
    const label = (loc.state as { melisTabLabel?: string } | null)?.melisTabLabel || ''
    openEditor(id, label)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [loc.key, loc.pathname])

  // URL = /[section]/[tool]/:id (ou /new), reflétée à chaque changement de sous-onglet actif.
  useEffect(() => { reflectSubTabUrl(activeId) }, [activeId])

  return (
    <div style={{ display: 'flex', flexDirection: 'column', height: '100%' }}>
      {open.length > 0 && (
        <SubTabBar tabs={open} activeId={activeId}
          onBack={() => setView({ kind: 'list' })} onSelect={(id) => setView({ kind: 'edit', id })} onClose={closeEditor} />
      )}

      <div style={{ flex: 1, minHeight: 0 }}>
        <div style={{ height: '100%', display: view.kind === 'list' ? 'block' : 'none' }}>
          <NewsListPage active={view.kind === 'list'} onOpen={openEditor} onNew={openNew} />
        </div>

        {open.map((o) => (
          <div key={String(o.id)} style={{ height: '100%', display: activeId === o.id ? 'block' : 'none' }}>
            <NewsFormPage
              newsId={o.id}
              onSaved={handleSaved}
              onTitleChange={(t) => renameTab(o.id, t)}
            />
          </div>
        ))}
      </div>
    </div>
  )
}
