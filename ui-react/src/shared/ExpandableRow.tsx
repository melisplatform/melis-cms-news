import type { ReactNode } from 'react'
import { Plus, Minus } from 'lucide-react'

/**
 * Per-row "+" toggle shown in the first column on narrow viewports, revealing the columns
 * forced out of the header by `narrowCols` (see NewsListPage). Pair with <HiddenColsRow>.
 */
export function ExpandToggle({ expanded, onClick }: { expanded: boolean; onClick: () => void }) {
  return (
    <button
      type="button"
      onClick={onClick}
      aria-expanded={expanded}
      className="inline-flex size-6 shrink-0 items-center justify-center rounded-md border border-border text-muted-foreground transition-colors hover:text-foreground"
    >
      {expanded ? <Minus className="size-3" /> : <Plus className="size-3" />}
    </button>
  )
}

/**
 * Detail row shown under an expanded row — one label/value pair per column hidden by the
 * narrow layout. Single stacked column (narrow-only, so no 2-col grid needed).
 */
export function HiddenColsRow({ items, colSpan }: {
  items: { id: string; label: string; value: ReactNode }[]
  colSpan: number
}) {
  if (items.length === 0) return null
  return (
    <tr>
      <td colSpan={colSpan} className="border-t border-border bg-muted/20 px-4 py-3">
        <div className="space-y-1.5 text-xs">
          {items.map((it) => (
            <div key={it.id} className="grid grid-cols-[auto_minmax(0,1fr)] items-baseline gap-x-2">
              <span className="font-semibold uppercase tracking-wide text-[10px] text-muted-foreground">{it.label}:</span>
              <span className="min-w-0 break-words text-foreground">{it.value}</span>
            </div>
          ))}
        </div>
      </td>
    </tr>
  )
}
