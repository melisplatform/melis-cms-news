import { useEffect, useState } from 'react'

/**
 * True when the viewport is narrower than `breakpoint` (default 640, matching Tailwind's `sm`).
 * Drives the list table's forced essential-columns + per-row "+" expand, mirroring the legacy
 * DataTables responsive behaviour instead of requiring a manual Columns-manager pick.
 */
export function useIsNarrow(breakpoint = 640): boolean {
  const [narrow, setNarrow] = useState(() => window.innerWidth < breakpoint)
  useEffect(() => {
    const onResize = () => setNarrow(window.innerWidth < breakpoint)
    window.addEventListener('resize', onResize)
    return () => window.removeEventListener('resize', onResize)
  }, [breakpoint])
  return narrow
}
