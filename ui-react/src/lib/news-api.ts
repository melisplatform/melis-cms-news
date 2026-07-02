const XHR_HEADER = { 'X-Requested-With': 'XMLHttpRequest' } as const

// ─── Base types ────────────────────────────────────────────────────────────────

export interface Language {
  id: number
  locale: string  // e.g. 'en_EN', 'fr_FR'
  name: string    // e.g. 'English', 'Français'
}

export interface NewsCategory {
  id: number
  fatherCatId: number
  name: string
}

export interface NewsSeo {
  url: string
  urlRedirect: string
  url301: string
  metaTitle: string
  metaDescription: string
  canonical: string
}

export interface NewsItem {
  id: number
  title: string
  subtitle: string
  status: 0 | 1
  siteId: number
  siteName: string
  creationDate: string
  publishDate: string | null
  unpublishDate: string | null
}

export interface NewsDetail extends NewsItem {
  paragraphs: string[]   // colonnes cnews_paragraph1-10, non vides, dans l'ordre d'affichage
  image1: string | null
  image2: string | null
  image3: string | null
  document1: string | null
  document2: string | null
  document3: string | null
  sliderId: number | null
  categoryIds: number[]
  seo: NewsSeo
}

export interface NewsListResult {
  items: NewsItem[]
  total: number
  page: number
  limit: number
}

export interface NewsListParams {
  page?: number
  limit?: number
  search?: string
  status?: '' | '0' | '1'
  siteId?: number
}

export interface NewsSavePayload {
  id?: number | null
  langId?: number
  title: string
  subtitle?: string
  paragraphs?: string[]
  status: 0 | 1
  siteId: number
  publishDate?: string | null
  unpublishDate?: string | null
  sliderId?: number | null
  categoryIds?: number[]
  seo?: Partial<NewsSeo>
}

export interface Site {
  id: number
  name: string
}

export interface NewsStats {
  total: number
  published: number
  draft: number
}

// ─── HTTP client ───────────────────────────────────────────────────────────────

async function apiFetch<T>(url: string, opts?: RequestInit): Promise<T> {
  const res = await fetch(url, {
    ...opts,
    headers: { ...XHR_HEADER, ...(opts?.headers ?? {}) },
    credentials: 'include',
  })
  if (!res.ok) {
    let msg = `HTTP ${res.status}`
    try {
      const d = (await res.json()) as { error?: string }
      if (d.error) msg = d.error
    } catch { /* ignore */ }
    throw new Error(msg)
  }
  const data = (await res.json()) as { success: boolean; data?: T; error?: string }
  if (!data.success) throw new Error(data.error ?? 'API error')
  return data.data as T
}

// ─── Languages ─────────────────────────────────────────────────────────────────

export async function fetchLanguages(): Promise<Language[]> {
  return apiFetch<Language[]>('/melis/react-api/news-languages')
}

// ─── News ──────────────────────────────────────────────────────────────────────

export async function fetchNewsList(params: NewsListParams = {}): Promise<NewsListResult> {
  const qs = new URLSearchParams()
  if (params.page)   qs.set('page',   String(params.page))
  if (params.limit)  qs.set('limit',  String(params.limit))
  if (params.search) qs.set('search', params.search)
  if (params.status !== undefined && params.status !== '') qs.set('status', params.status)
  if (params.siteId) qs.set('siteId', String(params.siteId))
  return apiFetch<NewsListResult>(`/melis/react-api/news?${qs}`)
}

export async function fetchNewsById(id: number, langId?: number): Promise<NewsDetail> {
  const qs = langId ? `?langId=${langId}` : ''
  return apiFetch<NewsDetail>(`/melis/react-api/news/${id}${qs}`)
}

export async function saveNews(payload: NewsSavePayload): Promise<{ id: number }> {
  return apiFetch<{ id: number }>('/melis/react-api/news/save', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  })
}

export async function deleteNews(id: number): Promise<void> {
  await apiFetch<null>(`/melis/react-api/news/delete/${id}`, { method: 'DELETE' })
}

export async function fetchNewsStats(): Promise<NewsStats> {
  return apiFetch<NewsStats>('/melis/react-api/news/stats')
}

export async function fetchNewsPreviewUrl(id: number): Promise<string | null> {
  const data = await apiFetch<{ previewUrl: string | null }>(`/melis/react-api/news/preview/${id}`)
  return data.previewUrl
}

// ─── Categories ────────────────────────────────────────────────────────────────

export async function fetchCategories(langId?: number): Promise<NewsCategory[]> {
  const qs = langId ? `?langId=${langId}` : ''
  return apiFetch<NewsCategory[]>(`/melis/react-api/news/categories${qs}`)
}

// ─── Sites ─────────────────────────────────────────────────────────────────────

export async function fetchSites(): Promise<Site[]> {
  return apiFetch<Site[]>('/melis/react-api/news-sites')
}

// ─── Sliders (modular — only present when MelisCmsSlider is active) ──────────────
// Fournis par le module melis-cms-slider (migré). L'appel échoue (404) si l'outil
// n'est pas actif → la section Slider du formulaire est masquée.

export interface Slider {
  id: number
  name: string
}

export async function fetchSliders(): Promise<Slider[]> {
  const data = await apiFetch<{ items: Slider[]; total: number }>('/melis/react-api/sliders')
  return data.items ?? []
}

// ─── Modules actifs (gating d'UI modulaire) ─────────────────────────────────────
// /react-modules liste les modules ACTIFS livrant une brique React. On s'en sert pour
// n'afficher un bout d'UI apporté par un module optionnel que si ce module est actif.
// Ex. le bouton « Workflow » de la barre latérale appartient à MelisSmallBusiness.

interface ReactModuleEntry { module?: string }

export async function fetchActiveModules(): Promise<string[]> {
  try {
    const list = await apiFetch<ReactModuleEntry[]>('/melis/react-api/react-modules')
    return list.map((m) => m.module ?? '').filter(Boolean)
  } catch {
    return []
  }
}
