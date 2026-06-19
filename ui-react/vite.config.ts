import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import path from 'node:path'

/**
 * Build for the MelisCmsNews React brick.
 *
 * Produces a single IIFE bundle (public/ui-react/brick.js) loaded at runtime by the
 * MelisCore React shell when the module is active. React / ReactRouter are EXTERNAL,
 * mapped to the host globals exposed in MelisCore's main.tsx — so the brick reuses the
 * host React instance (hooks, context, Router all work across the boundary).
 *
 * Tailwind (v4) is bundled INTO the brick: brick.tsx imports `./index.css?inline` and
 * injects it once at runtime, so only brick.js needs to be loaded by the host.
 */
export default defineConfig({
  plugins: [tailwindcss()],
  esbuild: { jsx: 'automatic' },
  // Bundled deps (TipTap/ProseMirror, etc.) reference process.env.NODE_ENV for dev checks.
  // A lib (IIFE) build does NOT auto-replace it like an app build → "process is not defined"
  // at runtime. Replace it (and process.env) with literals at build time.
  define: {
    'process.env.NODE_ENV': JSON.stringify('production'),
    'process.env': '{}',
  },
  build: {
    outDir: path.resolve(import.meta.dirname, '..', 'public', 'ui-react'),
    emptyOutDir: false,
    cssCodeSplit: false,
    lib: {
      entry: path.resolve(import.meta.dirname, 'src/brick.tsx'),
      formats: ['iife'],
      name: 'MelisCmsNewsBrick',
      fileName: () => 'brick.js',
    },
    rollupOptions: {
      external: ['react', 'react-dom', 'react/jsx-runtime', 'react-router-dom'],
      output: {
        // Some bundled CJS deps keep runtime `require(...)` calls: TipTap's use-sync-external-store
        // shim does `require('react')`, others do `require('fs'|'crypto'|…)` guarded for Node. In a
        // browser IIFE there is no `require` → ReferenceError / a React without hooks. Provide a stub
        // that maps the React specifiers to the HOST globals (so hooks come from the shared instance)
        // and returns {} for everything else (Node-only paths the deps guard).
        banner: [
          'var require = function(n){',
          '  if (n === "react") return globalThis.MelisReact;',
          '  if (n === "react-dom" || n === "react-dom/client") return globalThis.MelisReactDOM;',
          '  if (n === "react/jsx-runtime") return globalThis.MelisReactJsxRuntime;',
          '  if (n === "react-router-dom") return globalThis.MelisReactRouterDOM;',
          '  return {};',
          '};',
        ].join(''),
        globals: {
          react: 'MelisReact',
          'react-dom': 'MelisReactDOM',
          'react/jsx-runtime': 'MelisReactJsxRuntime',
          'react-router-dom': 'MelisReactRouterDOM',
        },
      },
    },
  },
})
