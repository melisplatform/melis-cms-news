import type { ReactNode } from 'react'

/**
 * Per-row "+" toggle (leftmost column of a table) that reveals the columns currently hidden
 * by the narrow-viewport essential-column collapse. Pair with <HiddenColsRow>. Inline styles
 * (not Tailwind className) so it stays independent of whichever utility classes this brick's
 * bundled Tailwind build happens to have generated.
 */
const sIcon = { width: 13, height: 13, flexShrink: 0 } as const
const PlusIcon = () => <svg style={sIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 5v14M5 12h14" /></svg>
const MinusIcon = () => <svg style={sIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12h14" /></svg>

export function ExpandToggle({ expanded, onClick }: { expanded: boolean; onClick: () => void }) {
  return (
    <button type="button" onClick={onClick} aria-expanded={expanded}
      style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center', width: 24, height: 24, borderRadius: 6, border: '1px solid var(--color-border)', background: 'transparent', color: 'var(--color-muted-foreground)', cursor: 'pointer', padding: 0 }}>
      {expanded ? <MinusIcon /> : <PlusIcon />}
    </button>
  )
}

/**
 * Detail row shown under an expanded row — one label/value pair per hidden column.
 * Single stacked column (narrow rows are always on a phone-width viewport by construction).
 */
export function HiddenColsRow({ cols, labelFor, renderValue, colSpan }: {
  cols: { id: string; visible: boolean }[]
  labelFor: (id: string) => string
  renderValue: (id: string) => ReactNode
  colSpan: number
}) {
  const hidden = cols.filter((c) => !c.visible)
  if (hidden.length === 0) return null
  return (
    <tr>
      <td colSpan={colSpan} style={{ padding: '10px 16px', borderTop: '1px solid var(--color-border)', background: 'var(--color-muted,rgba(0,0,0,.02))', width: 0 }}>
        <div style={{ display: 'grid', gridTemplateColumns: 'minmax(0, 1fr)', rowGap: 10 }}>
          {hidden.map((c) => (
            <div key={c.id} style={{ display: 'grid', gridTemplateColumns: 'auto minmax(0, 1fr)', alignItems: 'baseline', gap: 8, fontSize: 13 }}>
              <span style={{ fontSize: 11, fontWeight: 600, textTransform: 'uppercase', letterSpacing: '.04em', color: 'var(--color-muted-foreground)' }}>{labelFor(c.id)}:</span>
              <span style={{ minWidth: 0, maxWidth: 260, overflowWrap: 'break-word', display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical', overflow: 'hidden' }}>{renderValue(c.id)}</span>
            </div>
          ))}
        </div>
      </td>
    </tr>
  )
}
