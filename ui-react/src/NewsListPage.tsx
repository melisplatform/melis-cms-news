import { Fragment, useEffect, useLayoutEffect, useMemo, useRef, useState } from 'react'
import {
  ArrowDown, ArrowUp, ArrowUpDown, CheckCircle2, Code2, Columns3,
  Download, Edit2, FileDown, FileSpreadsheet, FileText, GripVertical,
  Layout, Loader2, Newspaper, Pin, Plus, RotateCcw, Search, Trash2, X,
} from 'lucide-react'
import * as XLSX from 'xlsx'

import { Button } from './components/ui/button'
import { Input } from './components/ui/input'
import { cn } from './lib/utils'
import { t, newsLang } from './lib/i18n'
import * as newsApi from './lib/news-api'
import { useCaps } from './shared/useCaps'
import { useIsNarrow } from './shared/useIsNarrow'
import { ExpandToggle, HiddenColsRow } from './shared/ExpandableRow'
import { useDragReorder } from './shared/use-drag-reorder'
import { ConfirmDialog } from './shared/confirm-dialog'
import { koNotify } from './shared/melis-form-errors'
import { useKeysetList } from './use-keyset-list'

// News tool capability key — must match config/react.capabilities.php, i.e. the melisKey of the
// rights-bearing menu node. NOT `meliscmsnews_left_menu`: that is the type-link target and stays
// the renderable zone key used by the iframe below.
const NEWS_CAPS_KEY = 'meliscmsnews_tools_section'

// ─── Module-level cache — survit au démontage du composant (navigation) ────────

type ViewMode = 'react' | 'iframe'

interface ListCache {
  items: newsApi.NewsItem[]
  total: number
  cursor: string | null
  hasMore: boolean
  sortCol: string
  sortDir: 'asc' | 'desc'
  search: string
  status: '' | '0' | '1'
  kpiStats: newsApi.NewsStats | null
  mode: ViewMode
  iframeLoaded: boolean
}
let _cache: ListCache | null = null

// ─── Column config ────────────────────────────────────────────────────────────

interface ColDef {
  id: string
  label: string
  visible: boolean
  pinned: boolean
}

// Fixed px widths for columns with known content size.
// Columns WITHOUT an entry (title, subtitle) get no width → absorb all remaining space.
const COL_FIXED_WIDTHS: Record<string, number> = {
  id:             52,
  site:          130,
  publishDate:   124,
  unpublishDate: 124,
  creationDate:  124,
  status:         88,
  _actions:       80,
}

// Kept for tableMinWidth calculation (fallback for unmapped cols)
const COL_MIN_WIDTHS: Record<string, number> = {
  id:             52,
  title:         240,
  subtitle:      160,
  site:          130,
  publishDate:   124,
  unpublishDate: 124,
  creationDate:  124,
  status:         88,
  _actions:       80,
}

const DEFAULT_COLS: ColDef[] = [
  { id: 'id',            label: t('col_id'),          visible: true,  pinned: false },
  { id: 'title',         label: t('col_title'),       visible: true,  pinned: false },
  { id: 'subtitle',      label: t('col_subtitle'),    visible: false, pinned: false },
  { id: 'site',          label: t('col_site'),        visible: true,  pinned: false },
  { id: 'publishDate',   label: t('col_publication'), visible: true,  pinned: false },
  { id: 'unpublishDate', label: t('col_expiration'),  visible: false, pinned: false },
  { id: 'creationDate',  label: t('col_created'),     visible: false, pinned: false },
  { id: 'status',        label: t('col_status'),      visible: true,  pinned: false },
]

const COL_STORAGE_KEY = 'melis-news-cols-v3'

function loadCols(): ColDef[] {
  try {
    const raw = localStorage.getItem(COL_STORAGE_KEY)
    if (!raw) return DEFAULT_COLS
    const saved: { id: string; visible: boolean; pinned?: boolean }[] = JSON.parse(raw)
    return DEFAULT_COLS
      .map(def => {
        const s = saved.find(c => c.id === def.id)
        return s ? { ...def, visible: s.visible, pinned: s.pinned ?? false } : def
      })
      .sort((a, b) => {
        const ai = saved.findIndex(c => c.id === a.id)
        const bi = saved.findIndex(c => c.id === b.id)
        return ai === -1 || bi === -1 ? 0 : ai - bi
      })
  } catch {
    return DEFAULT_COLS
  }
}

function saveCols(cols: ColDef[]) {
  localStorage.setItem(
    COL_STORAGE_KEY,
    JSON.stringify(cols.map(c => ({ id: c.id, visible: c.visible, pinned: c.pinned }))),
  )
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

function fmtDate(d: string | null): string {
  if (!d) return '—'
  try {
    return new Date(d).toLocaleDateString(newsLang() === 'fr' ? 'fr-FR' : 'en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
  } catch { return d }
}

function getCellText(item: newsApi.NewsItem, colId: string): string {
  switch (colId) {
    case 'id':            return String(item.id)
    case 'title':         return item.title || ''
    case 'subtitle':      return item.subtitle || ''
    case 'site':          return item.siteName || ''
    case 'publishDate':   return fmtDate(item.publishDate)
    case 'unpublishDate': return fmtDate(item.unpublishDate)
    case 'creationDate':  return fmtDate(item.creationDate)
    case 'status':        return item.status === 1 ? t('status_published') : t('status_draft')
    default:              return ''
  }
}

// ─── Status badge ─────────────────────────────────────────────────────────────

function StatusBadge({ status }: { status: 0 | 1 }) {
  return (
    <span className={cn(
      'inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium',
      status === 1
        ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
        : 'bg-muted text-muted-foreground',
    )}>
      {status === 1 ? t('status_published') : t('status_draft')}
    </span>
  )
}

// ─── KPI card ─────────────────────────────────────────────────────────────────

function KpiCard({ icon, label, value, iconBg, className }: {
  icon: React.ReactNode; label: string; value: number | null; iconBg: string; className?: string
}) {
  return (
    <div className={cn('flex items-center gap-3 rounded-xl border border-border bg-card px-4 py-3 flex-1 min-w-[150px]', className)}>
      <div className={cn('flex size-10 shrink-0 items-center justify-center rounded-lg', iconBg)}>
        {icon}
      </div>
      <div className="min-w-0">
        <div className="text-2xl font-bold leading-none text-foreground">
          {value === null
            ? <Loader2 className="size-4 animate-spin text-muted-foreground" />
            : value.toLocaleString(newsLang() === 'fr' ? 'fr-FR' : 'en-GB')}
        </div>
        <div className="mt-0.5 truncate text-xs text-muted-foreground">{label}</div>
      </div>
    </div>
  )
}

// ─── Column manager ───────────────────────────────────────────────────────────

function ColManager({ cols, onChange, onClose, anchorRef }: {
  cols: ColDef[]
  onChange: (cols: ColDef[]) => void
  onClose: () => void
  anchorRef: React.RefObject<HTMLElement>
}) {
  // `next` comes back typed as the hook's minimal { id, visible } shape — cast to this module's
  // richer ColDef (label + pinned), which is what the objects actually still carry at runtime
  // (commitDrop only ever spreads from the `cols` we pass in).
  const { draggingId: dragId, overTarget: over, dragPos, startDragMouse, startDragTouch } = useDragReorder({
    cols, onChange: (next) => { const full = next as ColDef[]; onChange(full); saveCols(full) },
  })

  // Clamped position (not a `right:0` CSS anchor) — the anchor button's own right edge is rarely
  // flush with the true viewport edge on narrow, so a right-anchored popover can still push its
  // LEFT edge off-screen. Computed from the anchor's rect, recomputed on resize.
  const [pos, setPos] = useState<{ left: number; top: number; width: number } | null>(null)
  useLayoutEffect(() => {
    const el = anchorRef.current
    if (!el) return
    const margin = 12
    const width = Math.min(420, window.innerWidth - margin * 2)
    function compute() {
      const rect = el!.getBoundingClientRect()
      const left = Math.min(Math.max(margin, rect.right - width), window.innerWidth - width - margin)
      setPos({ left, top: rect.bottom + 6, width })
    }
    compute()
    window.addEventListener('resize', compute)
    return () => window.removeEventListener('resize', compute)
  }, [anchorRef])

  const visibleCols = cols.filter(c => c.visible)
  const hiddenCols  = cols.filter(c => !c.visible)

  function item(col: ColDef, panel: 'visible' | 'hidden') {
    const isMandatory = col.id === 'title'
    const isOver = over?.id === col.id && over?.panel === panel
    return (
      <div
        key={col.id}
        data-col-item={col.id}
        onMouseDown={isMandatory ? undefined : startDragMouse(col.id)}
        onTouchStart={isMandatory ? undefined : startDragTouch(col.id)}
        style={{ touchAction: 'none' }}
        className={cn(
          'flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm select-none transition-colors',
          !isMandatory && 'cursor-grab active:cursor-grabbing',
          dragId === col.id && 'opacity-40',
          isOver ? 'bg-primary/10 ring-1 ring-primary/30' : 'hover:bg-accent',
          col.pinned && panel === 'visible' && !isOver && 'bg-primary/5',
        )}
      >
        <GripVertical className={cn('size-3.5 shrink-0 text-muted-foreground/40', isMandatory && 'invisible')} />
        <span className="flex-1 truncate">{col.label}</span>
        {panel === 'visible' && (
          <button
            onClick={e => { e.stopPropagation(); onChange(cols.map(c => c.id === col.id ? { ...c, pinned: !c.pinned } : c)) }}
            title={col.pinned ? t('unpin') : t('pin')}
            className={cn(
              'flex size-5 shrink-0 items-center justify-center rounded transition-colors hover:bg-primary/10',
              col.pinned ? 'text-primary' : 'text-muted-foreground/30 hover:text-muted-foreground',
            )}
          >
            <Pin className={cn('size-3', col.pinned && 'fill-primary')} />
          </button>
        )}
      </div>
    )
  }

  if (!pos) return null
  return (
    <>
    <div
      style={{ position: 'fixed', left: pos.left, top: pos.top, width: pos.width }}
      className="z-50 rounded-xl border border-border bg-card shadow-xl"
    >
      <div className="flex items-center justify-between border-b border-border px-3 py-2.5">
        <span className="text-sm font-semibold">{t('columns')}</span>
        <button onClick={onClose} className="text-muted-foreground hover:text-foreground transition-colors">
          <X className="size-4" />
        </button>
      </div>

      <div className="grid grid-cols-2 gap-2 p-3">
        <div
          data-col-panel="hidden"
          className={cn(
            'flex flex-col gap-0.5 min-h-[140px] max-h-[min(48vh,320px)] overflow-y-auto min-w-0 rounded-lg border border-dashed p-1.5',
            over?.id === '__panel__' && over.panel === 'hidden' ? 'border-primary/40 bg-primary/5' : 'border-border',
          )}
        >
          <p className="px-1.5 pb-1 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">{t('cols_hidden')}</p>
          {hiddenCols.length === 0
            ? <div className="flex flex-1 items-center justify-center py-4 text-[11px] text-muted-foreground/40">{t('drag_here')}</div>
            : hiddenCols.map(col => item(col, 'hidden'))}
        </div>

        <div
          data-col-panel="visible"
          className={cn(
            'flex flex-col gap-0.5 min-h-[140px] max-h-[min(48vh,320px)] overflow-y-auto min-w-0 rounded-lg border border-dashed p-1.5',
            over?.id === '__panel__' && over.panel === 'visible' ? 'border-primary/40 bg-primary/5' : 'border-border',
          )}
        >
          <p className="px-1.5 pb-1 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">{t('cols_visible')}</p>
          {visibleCols.map(col => item(col, 'visible'))}
        </div>
      </div>

      <div className="border-t border-border p-1.5">
        <button
          onClick={() => onChange(DEFAULT_COLS)}
          className="w-full rounded-lg px-2 py-1.5 text-xs text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
        >
          {t('reset')}
        </button>
      </div>
    </div>
    {dragId && dragPos && (
      <div
        style={{ position: 'fixed', zIndex: 60, left: dragPos.x, top: dragPos.y, transform: 'translate(-50%, -50%)', pointerEvents: 'none', display: 'flex', alignItems: 'center', gap: 8, borderRadius: 8, padding: '6px 10px', fontSize: 14, fontWeight: 500, background: 'var(--color-card)', border: '1px solid color-mix(in srgb, var(--color-primary) 40%, transparent)', boxShadow: '0 4px 16px rgba(0,0,0,.25)' }}
      >
        <GripVertical className="size-3.5 shrink-0 text-muted-foreground/40" />
        {cols.find(c => c.id === dragId)?.label ?? dragId}
      </div>
    )}
    </>
  )
}

// ─── Export modal ─────────────────────────────────────────────────────────────

function ExportModal({ cols: colsProp, search, status, total, onClose }: {
  cols: ColDef[]
  search: string
  status: '' | '0' | '1'
  total: number
  onClose: () => void
}) {
  const [cols, setCols] = useState<ColDef[]>(colsProp)
  const { draggingId: dragId, overTarget: over, dragPos, startDragMouse, startDragTouch } = useDragReorder({
    cols, onChange: (next) => setCols(next as ColDef[]),
  })
  const included = cols.filter(c => c.visible)
  const excluded = cols.filter(c => !c.visible)
  const [format, setFormat]     = useState<'csv' | 'xlsx'>('xlsx')
  const [exporting, setExporting] = useState(false)

  function item(col: ColDef, panel: 'visible' | 'hidden') {
    const isOver = over?.id === col.id && over?.panel === panel
    return (
      <div
        key={col.id}
        data-col-item={col.id}
        onMouseDown={startDragMouse(col.id)}
        onTouchStart={startDragTouch(col.id)}
        style={{ touchAction: 'none' }}
        className={cn(
          'flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm select-none cursor-grab active:cursor-grabbing transition-colors',
          dragId === col.id && 'opacity-40',
          isOver ? 'bg-primary/10 ring-1 ring-primary/30' : 'hover:bg-accent',
        )}
      >
        <GripVertical className="size-3.5 shrink-0 text-muted-foreground/40" />
        <span className="flex-1 truncate">{col.label}</span>
      </div>
    )
  }

  async function doExport() {
    if (included.length === 0) return
    setExporting(true)
    try {
      // Récupère TOUT le jeu filtré en bouclant sur le curseur keyset (100/lot).
      const all: newsApi.NewsItem[] = []
      let cursor: string | null | undefined
      do {
        const res = await newsApi.fetchNewsList({
          limit: 100, search: search || undefined, status: status || undefined, after: cursor || undefined,
        })
        all.push(...res.items)
        cursor = res.nextCursor
      } while (cursor)
      const rows = all.map(item => included.map(c => getCellText(item, c.id)))
      const date = new Date().toISOString().slice(0, 10)
      if (format === 'xlsx') {
        const ws = XLSX.utils.aoa_to_sheet([included.map(c => c.label), ...rows])
        const wb = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(wb, ws, t('export_sheet'))
        XLSX.writeFile(wb, `${t('export_filename')}-${date}.xlsx`)
      } else {
        const csv = [included.map(c => c.label), ...rows]
          .map(row => row.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(','))
          .join('\n')
        const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' })
        const url  = URL.createObjectURL(blob)
        const a    = Object.assign(document.createElement('a'), { href: url, download: `${t('export_filename')}-${date}.csv` })
        document.body.appendChild(a); a.click(); document.body.removeChild(a)
        URL.revokeObjectURL(url)
      }
      onClose()
    } catch (e) {
      alert(e instanceof Error ? e.message : t('export_error'))
    } finally { setExporting(false) }
  }

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
      onClick={e => { if (e.target === e.currentTarget) onClose() }}
    >
      <div className="w-full max-w-lg rounded-2xl border border-border bg-card shadow-2xl">
        <div className="flex items-start justify-between border-b border-border px-5 py-4">
          <div>
            <h2 className="text-sm font-semibold">{t('export')}</h2>
            <p className="mt-0.5 text-xs text-muted-foreground">
              {total.toLocaleString(newsLang() === 'fr' ? 'fr-FR' : 'en-GB')} {total !== 1 ? t('export_rows') : t('export_row')}
            </p>
          </div>
          <button onClick={onClose} className="ml-4 text-muted-foreground hover:text-foreground transition-colors">
            <X className="size-4" />
          </button>
        </div>

        <div className="p-4 space-y-4">
          {/* Format */}
          <div>
            <p className="mb-1.5 text-xs font-semibold uppercase tracking-wider text-muted-foreground">{t('format')}</p>
            <div className="flex gap-2">
              {(['xlsx', 'csv'] as const).map(f => (
                <button key={f} onClick={() => setFormat(f)} className={cn(
                  'flex flex-1 items-center justify-center gap-2 rounded-lg border py-2 text-sm font-medium transition-colors',
                  format === f ? 'border-primary bg-primary/5 text-primary' : 'border-input text-muted-foreground hover:border-foreground/30 hover:text-foreground',
                )}>
                  {f === 'xlsx' ? <><FileSpreadsheet className="size-4 text-emerald-600" />Excel (.xlsx)</> : <><FileText className="size-4 text-blue-500" />CSV (.csv)</>}
                </button>
              ))}
            </div>
          </div>

          {/* Colonnes DnD */}
          <div>
            <p className="mb-1.5 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              {t('cols_to_export')} <span className="font-normal text-muted-foreground/60">{t('drag_include_order')}</span>
            </p>
            <div className="grid grid-cols-2 gap-2">
              <div
                data-col-panel="hidden"
                className={cn(
                  'flex flex-col gap-0.5 min-h-[100px] max-h-[min(48vh,320px)] overflow-y-auto min-w-0 rounded-lg border border-dashed p-1.5',
                  over?.id === '__panel__' && over.panel === 'hidden' ? 'border-primary/40 bg-primary/5' : 'border-border',
                )}
              >
                <p className="px-1.5 pb-1 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">{t('excluded')}</p>
                {excluded.length === 0
                  ? <div className="flex flex-1 items-center justify-center py-3 text-[11px] text-muted-foreground/40">{t('drag_here')}</div>
                  : excluded.map(col => item(col, 'hidden'))}
              </div>
              <div
                data-col-panel="visible"
                className={cn(
                  'flex flex-col gap-0.5 min-h-[100px] max-h-[min(48vh,320px)] overflow-y-auto min-w-0 rounded-lg border border-dashed p-1.5',
                  over?.id === '__panel__' && over.panel === 'visible' ? 'border-primary/40 bg-primary/5' : 'border-border',
                )}
              >
                <p className="px-1.5 pb-1 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">{t('included')}</p>
                {included.length === 0
                  ? <div className="flex flex-1 items-center justify-center py-3 text-[11px] text-muted-foreground/40">{t('drag_here')}</div>
                  : included.map(col => item(col, 'visible'))}
              </div>
            </div>
          </div>
        </div>

        <div className="flex justify-end gap-2 border-t border-border px-4 py-3">
          <Button variant="outline" size="sm" onClick={onClose} disabled={exporting}>{t('cancel')}</Button>
          <Button size="sm" onClick={doExport} disabled={exporting || included.length === 0} className="gap-1.5">
            {exporting ? <Loader2 className="size-3.5 animate-spin" /> : <FileDown className="size-3.5" />}
            {exporting ? t('exporting') : t('download', { fmt: format.toUpperCase() })}
          </Button>
        </div>
      </div>
      {dragId && dragPos && (
        <div
          style={{ position: 'fixed', zIndex: 60, left: dragPos.x, top: dragPos.y, transform: 'translate(-50%, -50%)', pointerEvents: 'none', display: 'flex', alignItems: 'center', gap: 8, borderRadius: 8, padding: '6px 10px', fontSize: 14, fontWeight: 500, background: 'var(--color-card)', border: '1px solid color-mix(in srgb, var(--color-primary) 40%, transparent)', boxShadow: '0 4px 16px rgba(0,0,0,.25)' }}
        >
          <GripVertical className="size-3.5 shrink-0 text-muted-foreground/40" />
          {cols.find(c => c.id === dragId)?.label ?? dragId}
        </div>
      )}
    </div>
  )
}

// ─── NewsListPage ─────────────────────────────────────────────────────────────

const LIMIT = 25

/**
 * Liste des articles. Pilotée par le conteneur NewsPage (sous-onglets) : `onOpen`/`onNew`
 * ouvrent un sous-onglet d'édition DANS l'outil (plus de navigation d'URL ni d'onglet de shell).
 */
export default function NewsListPage({ active, onOpen, onNew, refreshToken = 0 }: {
  active: boolean
  onOpen: (id: number, title: string) => void
  onNew: () => void
  /** Incrémenté par NewsPage à chaque sauvegarde d'article : la liste se recharge au retour. */
  refreshToken?: number
}) {
  // ── Capacités (droits avancés list/create/edit/delete/export) ────────────────
  const { can, loaded: capsLoaded } = useCaps(NEWS_CAPS_KEY)
  const canList = can('list')

  const narrow = useIsNarrow()
  const [expandedIds, setExpandedIds] = useState<Set<number>>(new Set())
  function toggleExpand(id: number) {
    setExpandedIds(prev => {
      const next = new Set(prev)
      if (next.has(id)) next.delete(id); else next.add(id)
      return next
    })
  }

  // ── View mode toggle ─────────────────────────────────────────────────────────
  const [mode, setMode] = useState<ViewMode>(_cache?.mode ?? 'react')
  const [iframeLoaded, setIframeLoaded] = useState(_cache?.iframeLoaded ?? false)

  // ── Column config ──────────────────────────────────────────────────────────
  const [cols, setCols] = useState<ColDef[]>(loadCols)

  function updateCols(next: ColDef[]) { setCols(next); saveCols(next) }

  // Pinned columns always appear first in the table
  const visibleCols = useMemo(() => {
    const v = cols.filter(c => c.visible)
    return [...v.filter(c => c.pinned), ...v.filter(c => !c.pinned)]
  }, [cols])

  // A Hidden column disappears entirely on both desktop and mobile — same rule everywhere, no "+"
  // peek at Hidden ones. Desktop shows every Visible column inline (pin-sorted, via `visibleCols`
  // above — unchanged). Mobile can't fit many columns, so only the FIRST Visible column (by the
  // user's dragged order in ColManager, NOT pin order) anchors inline; every OTHER Visible column
  // surfaces behind the per-row "+" instead, in that same order.
  const shownColsList = useMemo(() => cols.filter(c => c.visible), [cols])
  const displayCols = narrow ? shownColsList.map((c, i) => ({ ...c, visible: i === 0 })) : visibleCols
  const hasHidden = narrow && shownColsList.length > 1

  // Minimum table width = sum of column min-widths
  const tableMinWidth = useMemo(
    () => visibleCols.reduce((s, c) => s + (COL_MIN_WIDTHS[c.id] ?? 100), 0) + COL_MIN_WIDTHS._actions,
    [visibleCols],
  )

  // ── Pin offset measurement (DOM-based for accuracy) ────────────────────────
  const headerTableRef = useRef<HTMLTableElement>(null)
  const [pinOffsets,   setPinOffsets]   = useState<Record<string, number>>({})
  const [lastPinnedId, setLastPinnedId] = useState<string | undefined>()

  function measurePinOffsets() {
    const table = headerTableRef.current
    if (!table?.tHead?.rows[0]) return
    const offsets: Record<string, number> = {}
    let left = 0
    let last: string | undefined
    Array.from(table.tHead.rows[0].cells).forEach(cell => {
      const id = cell.dataset.colId
      if (!id) return
      const col = visibleCols.find(c => c.id === id)
      if (col?.pinned) {
        offsets[id] = left
        left += cell.offsetWidth
        last = id
      }
    })
    setPinOffsets(offsets)
    setLastPinnedId(last)
  }

  useLayoutEffect(() => { measurePinOffsets() }, [visibleCols])

  useEffect(() => {
    const table = headerTableRef.current
    if (!table) return
    const ro = new ResizeObserver(measurePinOffsets)
    ro.observe(table)
    return () => ro.disconnect()
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [visibleCols])

  // ── Scroll sync: body horizontal ↔ header ─────────────────────────────────
  const headerScrollRef = useRef<HTMLDivElement>(null)
  const bodyScrollRef   = useRef<HTMLDivElement>(null)

  // ── Filtres ──────────────────────────────────────────────────────────────
  const [search,   setSearch]   = useState(_cache?.search ?? '')
  const [status,   setStatus]   = useState<'' | '0' | '1'>(_cache?.status ?? '')
  const [deleting, setDeleting] = useState<number | null>(null)
  // Élément en attente de confirmation de suppression → <ConfirmDialog> (jamais window.confirm(),
  // dont la boîte native affiche l'URL du BO, ignore le thème et n'est pas traduisible).
  const [pendingDelete, setPendingDelete] = useState<{ id: number; title: string } | null>(null)

  // ── Liste : scroll infini + tri server-side + keyset (hook mutualisé) ──────
  // Le fetcher capture les filtres courants ; `deps` relance un chargement frais à
  // chaque changement de filtre. On gate sur capsLoaded/canList : tant que les caps
  // ne sont pas résolues (ou liste refusée) le fetcher renvoie un jeu vide sans
  // appeler l'API (évite un flash « Forbidden » / un 403). Le re-fetch se déclenche
  // automatiquement dès que `capsLoaded`/`canList` changent (présents dans `deps`).
  const {
    items, total, loading, hasMore, sentinelRef,
    sortCol, sortDir, toggleSort, reload, removeLocal, snapshot,
  } = useKeysetList<newsApi.NewsItem>({
    fetcher: (a) => {
      if (!capsLoaded || !canList) return Promise.resolve({ items: [], total: 0, nextCursor: null })
      return newsApi.fetchNewsList({
        ...a,
        search: search || undefined,
        status: status || undefined,
      })
    },
    deps: [search, status, capsLoaded, canList],
    limit: LIMIT,
    defaultSort: 'id',
    defaultDir: 'desc',
    initial: _cache
      ? {
          items: _cache.items, total: _cache.total, cursor: _cache.cursor,
          hasMore: _cache.hasMore, sortCol: _cache.sortCol, sortDir: _cache.sortDir,
        }
      : undefined,
    skipInitial: !!(_cache && _cache.items.length),
  })

  function handleSort(colId: string) { toggleSort(colId) }

  // ── KPIs (from dedicated stats endpoint) ──────────────────────────────────
  const [kpiStats, setKpiStats] = useState<newsApi.NewsStats | null>(_cache?.kpiStats ?? null)

  function loadKpis() {
    if (capsLoaded && !canList) return   // liste refusée → pas de compteurs
    newsApi.fetchNewsStats().then(setKpiStats).catch(() => {})
  }

  useEffect(() => { loadKpis() }, [])
  // Rafraîchit les compteurs quand on revient sur la liste (retour depuis un sous-onglet).
  useEffect(() => { if (active) loadKpis() }, [active])

  // Retour à la liste APRÈS une sauvegarde (« ← Retour » depuis le formulaire) : la liste
  // reste montée (display:none) pendant l'édition, donc rien ne la rechargerait et un article
  // fraîchement créé n'y apparaîtrait pas. NewsPage incrémente `refreshToken` à chaque save ;
  // on ne recharge que si le jeton a bougé depuis le dernier rechargement — une simple
  // consultation aller/retour ne repart donc PAS de la page 1 (scroll infini préservé).
  // `reload()` garde les lignes affichées jusqu'à l'arrivée du 1er lot (pas de clignotement).
  const appliedTokenRef = useRef(refreshToken)
  useEffect(() => {
    if (!active || refreshToken === appliedTokenRef.current) return
    appliedTokenRef.current = refreshToken
    _cache = null
    reload()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [active, refreshToken])

  // ── Cache: track current state + save on unmount ───────────────────────────
  const cacheRef = useRef<ListCache>({
    ...snapshot(), search, status, kpiStats, mode, iframeLoaded,
  })
  useEffect(() => {
    cacheRef.current = { ...snapshot(), search, status, kpiStats, mode, iframeLoaded }
  })
  useEffect(() => () => { _cache = cacheRef.current }, [])

  // Rafraîchir : recharge la liste ET les compteurs SANS toucher aux filtres/tri
  // (contrairement à resetFilters). `_cache=null` pour qu'un remontage ne restaure
  // pas l'ancien jeu de lignes. `refreshing` ne sert qu'à l'animation de l'icône.
  const [refreshing, setRefreshing] = useState(false)

  function handleRefresh() {
    _cache = null
    setRefreshing(true)
    setKpiStats(null)
    reload()
    loadKpis()
    setTimeout(() => setRefreshing(false), 600)
  }

  // Réinitialiser : recherche + statut par défaut, puis rechargement frais.
  // `_cache=null` pour ne pas restaurer un ancien état ; le changement de `search`/`status`
  // (deps du hook) relance le fetch, et `reload()` couvre le cas où ils étaient déjà vides.
  function resetFilters() {
    _cache = null
    setSearch('')
    setStatus('')
    reload()
  }

  // ── UI panels ──────────────────────────────────────────────────────────────
  const [showColMgr, setShowColMgr] = useState(false)
  const [showExport, setShowExport] = useState(false)
  const colMgrRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (!showColMgr) return
    function handler(e: MouseEvent) {
      if (colMgrRef.current && !colMgrRef.current.contains(e.target as Node))
        setShowColMgr(false)
    }
    document.addEventListener('mousedown', handler)
    return () => document.removeEventListener('mousedown', handler)
  }, [showColMgr])

  // ── Delete ─────────────────────────────────────────────────────────────────
  // Le clic n'appelle plus window.confirm() : il ARME la modale partagée (<ConfirmDialog>,
  // rendue en fin de page), qui appelle confirmDelete() une fois l'utilisateur confirmé.
  function handleDelete(id: number, title: string) {
    setPendingDelete({ id, title })
  }

  async function confirmDelete() {
    if (!pendingDelete) return
    const { id } = pendingDelete
    setDeleting(id)
    try {
      await newsApi.deleteNews(id)
      removeLocal(n => n.id === id)
      loadKpis()
      setPendingDelete(null)
    } catch (e) {
      koNotify(t('news_title'), e instanceof Error ? e.message : t('error'))
    } finally {
      setDeleting(null)
    }
  }

  // ── Pin styles ─────────────────────────────────────────────────────────────
  function pinStyle(col: ColDef, isHeader = false): React.CSSProperties {
    if (!col.pinned) return {}
    return {
      position: 'sticky',
      left: pinOffsets[col.id] ?? 0,
      zIndex: isHeader ? 20 : 2,
      backgroundColor: 'var(--color-card)',
      boxShadow: col.id === lastPinnedId ? '3px 0 6px -2px rgba(0,0,0,0.10)' : undefined,
    }
  }

  // ── Colgroup ───────────────────────────────────────────────────────────────
  function Colgroup() {
    return (
      <colgroup>
        {hasHidden && <col style={{ width: 40, minWidth: 40 }} />}
        {displayCols.filter(col => col.visible).map(col => {
          const fixed = COL_FIXED_WIDTHS[col.id]
          return (
            <col
              key={col.id}
              style={narrow
                ? { minWidth: 0 }
                : fixed ? { width: fixed, minWidth: fixed } : { minWidth: COL_MIN_WIDTHS[col.id] ?? 160 }}
            />
          )
        })}
        <col style={{ width: COL_FIXED_WIDTHS._actions, minWidth: COL_FIXED_WIDTHS._actions }} />
      </colgroup>
    )
  }

  // ── Render cell content ────────────────────────────────────────────────────
  function renderCell(item: newsApi.NewsItem, col: ColDef) {
    switch (col.id) {
      case 'id':
        return (
          <span className="tabular-nums text-xs font-medium text-muted-foreground">
            {item.id}
          </span>
        )
      case 'title':
        return (
          <button
            onClick={() => onOpen(item.id, item.title)}
            className="w-full truncate text-left font-medium text-foreground hover:text-primary transition-colors"
          >
            {item.title || <span className="italic text-muted-foreground">{t('untitled')}</span>}
          </button>
        )
      case 'subtitle':
        return <span className="block truncate text-xs text-muted-foreground">{item.subtitle || '—'}</span>
      case 'site':
        return <span className="block truncate text-muted-foreground">{item.siteName || '—'}</span>
      case 'publishDate':
        return <span className="whitespace-nowrap text-xs text-muted-foreground">{fmtDate(item.publishDate)}</span>
      case 'unpublishDate':
        return <span className="whitespace-nowrap text-xs text-muted-foreground">{fmtDate(item.unpublishDate)}</span>
      case 'creationDate':
        return <span className="whitespace-nowrap text-xs text-muted-foreground">{fmtDate(item.creationDate)}</span>
      case 'status':
        return <StatusBadge status={item.status} />
      default:
        return null
    }
  }

  // ── Render ─────────────────────────────────────────────────────────────────
  return (
    <div className="flex h-full flex-col gap-5 overflow-y-auto p-6">

      {/* Header — comes BEFORE the KPI strip (matches melis-core's Users tool layout order:
          title/actions first, then KPIs). Narrow viewports especially need the title above
          the fold rather than pushed down by the stat cards. */}
      <div className="flex items-center justify-between gap-3">
        <div className={cn(narrow && 'min-w-0')}>
          <h1 className={cn('text-xl font-semibold tracking-tight', narrow && 'truncate')}>{t('news_title')}</h1>
          <p className={cn('text-sm text-muted-foreground', narrow && 'truncate')}>{t('news_subtitle')}</p>
        </div>
        {/* On narrow: stacks internally (toggle row above "Nouvel article") so this whole block
            stays narrow enough to sit BESIDE the title on the same row, instead of the row
            wrapping below it — same pattern as melis-core's Users tool. */}
        <div className={cn('flex items-center gap-2', narrow && 'shrink-0 flex-col')}>
          {/* Toggle + refresh restent sur UNE ligne : en narrow le bloc parent passe en
              colonne, cette rangée d'icônes se retrouve alors au-dessus de « Nouvel article »
              (même disposition que l'outil Utilisateurs de melis-core). */}
          <div className="flex items-center gap-2">
            {/* Mode toggle — icon-only on narrow (title attr keeps it accessible) */}
            <div className="flex items-center rounded-lg border border-border bg-muted/40 p-1 gap-1">
              <button
                type="button"
                onClick={() => setMode('react')}
                title={t('view_new')}
                className={cn(
                  'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-colors',
                  mode === 'react'
                    ? 'bg-card shadow-sm text-foreground'
                    : 'text-muted-foreground hover:text-foreground',
                )}
              >
                <Code2 className="size-3.5" />
                {!narrow && t('view_new')}
              </button>
              <button
                type="button"
                onClick={() => { setMode('iframe'); setIframeLoaded(true) }}
                title={t('view_old')}
                className={cn(
                  'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-colors',
                  mode === 'iframe'
                    ? 'bg-card shadow-sm text-foreground'
                    : 'text-muted-foreground hover:text-foreground',
                )}
              >
                <Layout className="size-3.5" />
                {!narrow && t('view_old')}
              </button>
            </div>
            <button
              type="button"
              onClick={handleRefresh}
              title={t('refresh')}
              className="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
            >
              <RotateCcw className={cn('size-3.5', refreshing && 'animate-spin')} />
            </button>
          </div>
          {can('create') && (
            <Button onClick={onNew} size="sm" className={cn('gap-1.5', narrow && 'w-full')}>
              <Plus className="size-4" />
              {t('new_article')}
            </Button>
          )}
        </div>
      </div>

      {/* KPI strip — masqué si la liste est refusée (fait partie de la « liste »). On narrow,
          a real 2-col grid (not flex-wrap) so the 3 cards land evenly instead of an odd
          2-then-1 wrap; the 3rd card spans both columns so it still fills its row. JS ternary
          (not `sm:`) per this brick's convention — see shared/useIsNarrow.ts. */}
      {canList && (
        <div className={narrow ? 'grid grid-cols-2 gap-3' : 'flex flex-wrap gap-3'}>
          <KpiCard
            icon={<Newspaper    className="size-5 text-blue-500"    />}
            label={t('total_articles')}
            value={kpiStats?.total    ?? null}
            iconBg="bg-blue-500/10"
          />
          <KpiCard
            icon={<CheckCircle2 className="size-5 text-emerald-500" />}
            label={t('count_published')}
            value={kpiStats?.published ?? null}
            iconBg="bg-emerald-500/10"
          />
          <KpiCard
            icon={<FileText     className="size-5 text-orange-500"  />}
            label={t('count_drafts')}
            value={kpiStats?.draft     ?? null}
            iconBg="bg-orange-500/10"
            className={narrow ? 'col-span-2' : undefined}
          />
        </div>
      )}

      {/* Vue Melis classique — gardée montée pour ne pas recharger au retoggle */}
      {iframeLoaded && (
        <div className={cn('flex-1 min-h-[480px] rounded-xl border border-border overflow-hidden', mode === 'iframe' ? 'flex' : 'hidden')}>
          <iframe
            src="/melis/react-tool-page?key=meliscmsnews_left_menu"
            className="h-full w-full border-0"
            sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-modals"
            title={t('melis_view')}
          />
        </div>
      )}

      {/* React native view */}
      <div className={cn('flex flex-1 flex-col gap-4', mode !== 'react' && 'hidden')}>

      {/* Liste refusée (capacité `list`) → seule la zone liste (filtres + tableau) est remplacée. */}
      {!canList ? (
        <p className="text-sm text-muted-foreground">
          {t('no_list_rights')}
        </p>
      ) : (<>

      {/* Filters + actions */}
      <div className={narrow ? 'flex flex-col gap-2' : 'flex flex-wrap items-center gap-2'}>
        <div className={cn('relative', narrow ? 'w-full' : 'min-w-[200px] flex-1 max-w-sm')}>
          <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder={t('search_ph')}
            value={search}
            onChange={e => setSearch(e.target.value)}
            className="pl-9 h-9"
          />
        </div>
        <div className={cn('flex h-9 items-center rounded-lg border border-border bg-muted/40 p-0.5 gap-0.5', narrow && 'w-full')}>
          {([
            { value: '',  label: t('filter_all'),      dot: null              },
            { value: '1', label: t('filter_active'),   dot: 'bg-emerald-500' },
            { value: '0', label: t('filter_inactive'), dot: 'bg-red-500'     },
          ] as const).map(opt => (
            <button
              key={opt.value}
              type="button"
              onClick={() => setStatus(opt.value)}
              className={cn(
                'flex h-full items-center gap-1.5 rounded-md px-3 text-xs font-medium whitespace-nowrap transition-colors',
                narrow && 'flex-1 justify-center',
                status === opt.value
                  ? 'bg-card shadow-sm text-foreground'
                  : 'text-muted-foreground hover:text-foreground',
              )}
            >
              {opt.dot && <span className={cn('size-1.5 shrink-0 rounded-full', opt.dot)} />}
              {opt.label}
            </button>
          ))}
        </div>

        {/* "Reset filters" always gets its own full-width row on narrow — a FR/EN i18n string
            like "reset_filters" is long enough that pairing it 50/50 with anything wraps ugly. */}
        <div className={narrow ? 'flex w-full flex-col gap-2' : 'ml-auto flex items-center gap-2'}>
          <Button variant="outline" size="sm" className={cn('gap-1.5', narrow && 'w-full')} onClick={resetFilters}>
            <RotateCcw className="size-3.5" />
            {t('reset_filters')}
          </Button>
          <div className={narrow ? 'flex w-full gap-2' : 'contents'}>
            <div ref={colMgrRef} className={cn('relative', narrow && 'flex-1')}>
              <Button variant="outline" size="sm" className={cn('gap-1.5', narrow && 'w-full')} onClick={() => setShowColMgr(v => !v)}>
                <Columns3 className="size-3.5" />
                {t('columns')}
              </Button>
              {showColMgr && (
                <ColManager cols={cols} onChange={updateCols} onClose={() => setShowColMgr(false)} anchorRef={colMgrRef} />
              )}
            </div>
            {can('export') && (
              <Button variant="outline" size="sm" className={cn('gap-1.5', narrow && 'flex-1')} onClick={() => setShowExport(true)}>
                <Download className="size-3.5" />
                {t('export')}
              </Button>
            )}
          </div>
        </div>
      </div>

      {/* Table: sticky header + scrollable body */}
      <div className="rounded-xl border border-border bg-card" style={{ overflow: 'clip' }}>

        {/* Sticky header */}
        <div className="sticky top-0 z-10 border-b border-border bg-card">
          <div ref={headerScrollRef} style={{ overflowX: 'hidden' }}>
            <table
              ref={headerTableRef}
              className="w-full text-sm"
              style={narrow ? { tableLayout: 'fixed', width: '100%' } : { tableLayout: 'fixed', minWidth: tableMinWidth }}
            >
              <Colgroup />
              <thead>
                <tr className="bg-muted/40">
                  {hasHidden && <th className="w-10 px-2 py-3" />}
                  {displayCols.filter(col => col.visible).map(col => (
                    <th
                      key={col.id}
                      data-col-id={col.id}
                      className="px-4 py-3 text-left font-medium text-muted-foreground whitespace-nowrap cursor-pointer select-none group/th hover:text-foreground transition-colors"
                      style={pinStyle(col, true)}
                      onClick={() => handleSort(col.id)}
                    >
                      <div className="flex items-center gap-1.5">
                        {col.label}
                        {col.pinned && <Pin className="size-3 fill-primary text-primary opacity-60" />}
                        {sortCol === col.id
                          ? sortDir === 'asc'
                            ? <ArrowUp className="size-3 text-primary" />
                            : <ArrowDown className="size-3 text-primary" />
                          : <ArrowUpDown className="size-3 opacity-30" />}
                      </div>
                    </th>
                  ))}
                  <th className="px-4 py-3 text-right font-medium text-muted-foreground whitespace-nowrap">
                    {t('col_actions')}
                  </th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        {/* Body */}
        <div
          ref={bodyScrollRef}
          className="overflow-x-auto"
          onScroll={e => {
            if (headerScrollRef.current)
              headerScrollRef.current.scrollLeft = e.currentTarget.scrollLeft
          }}
        >
          {(!capsLoaded || loading) && items.length === 0 ? (
            <div className="flex items-center justify-center py-20">
              <Loader2 className="size-5 animate-spin text-muted-foreground" />
            </div>
          ) : items.length === 0 ? (
            <div className="py-20 text-center text-sm text-muted-foreground">{t('no_articles')}</div>
          ) : (
            <table
              className="w-full text-sm"
              style={narrow ? { tableLayout: 'fixed', width: '100%' } : { tableLayout: 'fixed', minWidth: tableMinWidth }}
            >
              <Colgroup />
              <tbody>
                {items.map(item => (
                  <Fragment key={item.id}>
                    <tr
                      className="border-b border-border/50 last:border-0 hover:bg-muted/30 transition-colors cursor-pointer"
                      onClick={() => onOpen(item.id, item.title)}
                    >
                      {hasHidden && (
                        <td className="px-2 py-3" onClick={(e) => e.stopPropagation()}>
                          <ExpandToggle expanded={expandedIds.has(item.id)} onClick={() => toggleExpand(item.id)} />
                        </td>
                      )}
                      {displayCols.filter(col => col.visible).map(col => (
                        <td key={col.id} className="px-4 py-3" style={pinStyle(col)}>
                          {renderCell(item, col)}
                        </td>
                      ))}
                      <td className="px-4 py-3">
                        <div className="flex items-center justify-end gap-1">
                          {can('edit') && (
                            <Button
                              variant="ghost" size="icon" className="size-8"
                              onClick={(e) => { e.stopPropagation(); onOpen(item.id, item.title) }} title={t('edit')}
                            >
                              <Edit2 className="size-3.5" />
                            </Button>
                          )}
                          {can('delete') && (
                            <Button
                              variant="ghost" size="icon"
                              className="size-8 text-destructive hover:text-destructive"
                              onClick={(e) => { e.stopPropagation(); handleDelete(item.id, item.title) }}
                              disabled={deleting === item.id} title={t('delete')}
                            >
                              {deleting === item.id
                                ? <Loader2 className="size-3.5 animate-spin" />
                                : <Trash2 className="size-3.5" />}
                            </Button>
                          )}
                        </div>
                      </td>
                    </tr>
                    {hasHidden && expandedIds.has(item.id) && (
                      <HiddenColsRow
                        cols={displayCols}
                        labelFor={(id) => cols.find(c => c.id === id)?.label ?? id}
                        renderValue={(id) => getCellText(item, id)}
                        colSpan={displayCols.filter(col => col.visible).length + 2}
                      />
                    )}
                  </Fragment>
                ))}
              </tbody>
            </table>
          )}

          {/* Infinite scroll sentinel */}
          <div ref={sentinelRef} className="h-1" />
          {loading && items.length > 0 && (
            <div className="flex items-center justify-center gap-2 py-4 text-xs text-muted-foreground">
              <Loader2 className="size-3.5 animate-spin" />
              {t('loading')}
            </div>
          )}
          {!hasMore && items.length > 0 && (
            <div className="py-4 text-center text-xs text-muted-foreground">
              {total.toLocaleString(newsLang() === 'fr' ? 'fr-FR' : 'en-GB')} {total > 1 ? t('export_rows') : t('export_row')} — {t('end_of_list')}
            </div>
          )}
        </div>
      </div>
      </>)}

      </div>{/* end React native view */}

      {/* Confirmation de suppression — modale partagée du BO React (remplace window.confirm). */}
      <ConfirmDialog
        open={!!pendingDelete}
        title={t('confirm_delete', { title: pendingDelete?.title || t('untitled') })}
        description={t('confirm_delete_desc')}
        confirmLabel={t('delete')}
        cancelLabel={t('cancel')}
        busy={deleting !== null}
        onConfirm={confirmDelete}
        onCancel={() => { if (deleting === null) setPendingDelete(null) }}
      />

      {/* Export modal */}
      {showExport && (
        <ExportModal
          cols={cols}
          search={search}
          status={status}
          total={total}
          onClose={() => setShowExport(false)}
        />
      )}
    </div>
  )
}
