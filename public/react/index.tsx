import { lazy } from 'react'
import type { MelisModuleManifest } from '@/types/melis-modules'

const NewsListPage = lazy(() => import('./NewsListPage'))
const NewsFormPage = lazy(() => import('./NewsFormPage'))

const manifest: MelisModuleManifest = {
  routes: [
    { path: '/news',      component: NewsListPage },
    { path: '/news/new',  component: NewsFormPage },
    { path: '/news/:id',  component: NewsFormPage },
  ],
}

export default manifest
