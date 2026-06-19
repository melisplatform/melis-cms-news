import NewsBrick from './NewsBrick'
// Tailwind-processed CSS for this brick, imported as a raw string and injected once at
// runtime (so only brick.js needs to be loaded by the host). The theme var VALUES are also
// supplied by the host, so duplicating the @theme mapping here is harmless.
import css from './index.css?inline'

/**
 * Brick entry point. Registers the News Component (routed content area at
 * /melis-cms/news and /melis-cms/news/:id) with the MelisCore React shell.
 *
 * React / ReactDOM / react-router-dom are EXTERNAL (mapped to the host globals
 * MelisReact / MelisReactDOM / MelisReactRouterDOM) so hooks, Router and context are
 * shared with the host instance.
 */
declare global {
  interface Window {
    __melisRegisterBrick?: (b: { id: string; Component?: unknown; Sidebar?: unknown }) => void
  }
}

// Inject the brick's stylesheet once (id-guarded against duplicate mounts).
const STYLE_ID = 'melis-cms-news-brick-styles'
if (typeof document !== 'undefined' && !document.getElementById(STYLE_ID)) {
  const style = document.createElement('style')
  style.id = STYLE_ID
  style.textContent = css
  document.head.appendChild(style)
}

window.__melisRegisterBrick?.({ id: 'news', Component: NewsBrick })
