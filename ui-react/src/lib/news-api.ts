const XHR_HEADER = { 'X-Requested-With': 'XMLHttpRequest' } as const

// ─── Upload / suppression de médias (images + fichiers) ─────────────────────────
// Réutilise les endpoints legacy ÉPROUVÉS de MelisCmsNews (aucune modif backend) :
//   POST /melis/MelisCmsNews/MelisCmsNews/saveFileForm     (multipart, upload → colonne)
//   POST /melis/MelisCmsNews/MelisCmsNews/removeAttachFile (vide la colonne + supprime le fichier)
// Les fichiers atterrissent dans public/media/news/<newsId>/ ; le chemin est stocké dans
// cnews_image1..3 (images) ou cnews_documents1..3 (fichiers). L'article doit exister (newsId).

export type MediaKind = 'image' | 'file'

/** Colonne DB cible pour un slot donné (1..3). */
export function mediaColumn(kind: MediaKind, slot: 1 | 2 | 3): string {
  return (kind === 'image' ? 'cnews_image' : 'cnews_documents') + slot
}

export interface UploadResult { success: boolean; message?: string }

/** Uploade un fichier vers un slot précis (remplace le contenu de la colonne). */
export async function uploadNewsFile(newsId: number, kind: MediaKind, slot: 1 | 2 | 3, file: File): Promise<UploadResult> {
  try {
    const fd = new FormData()
    fd.append('cnews_document', file)
    fd.append('cnews_id', String(newsId))
    fd.append('type', kind)
    fd.append('column', mediaColumn(kind, slot))
    const res = await fetch('/melis/MelisCmsNews/MelisCmsNews/saveFileForm', {
      method: 'POST',
      headers: { ...XHR_HEADER }, // pas de Content-Type : le navigateur pose la boundary multipart
      credentials: 'include',
      body: fd,
    })
    const data = await res.json()
    return { success: data?.success === 1 || data?.success === true, message: data?.textMessage }
  } catch (e) {
    return { success: false, message: e instanceof Error ? e.message : 'Upload error' }
  }
}

/** Supprime le fichier d'un slot (vide la colonne + efface le fichier disque). */
export async function removeNewsFile(newsId: number, kind: MediaKind, slot: 1 | 2 | 3): Promise<UploadResult> {
  try {
    const body = new URLSearchParams({ newsId: String(newsId), type: kind, column: mediaColumn(kind, slot) })
    const res = await fetch('/melis/MelisCmsNews/MelisCmsNews/removeAttachFile', {
      method: 'POST',
      headers: { ...XHR_HEADER, 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      credentials: 'include',
      body: body.toString(),
    })
    const data = await res.json()
    return { success: data?.success === 1 || data?.success === true, message: data?.textMessage }
  } catch (e) {
    return { success: false, message: e instanceof Error ? e.message : 'Delete error' }
  }
}

/** Recharge UNIQUEMENT les champs média d'un article (après upload/suppression) sans écraser
 *  les autres champs du formulaire (titre, paragraphes non sauvés…). */
export async function fetchNewsMedia(newsId: number, langId: number): Promise<Pick<NewsDetail, 'image1' | 'image2' | 'image3' | 'document1' | 'document2' | 'document3'>> {
  const d = await fetchNewsById(newsId, langId)
  return {
    image1: d.image1, image2: d.image2, image3: d.image3,
    document1: d.document1, document2: d.document2, document3: d.document3,
  }
}

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
  status: number // 1 = active, 0 = inactive (affiché avec un point vert/rouge, comme le legacy)
}

export interface NewsTag {
  id: number
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
  tagIds: number[]
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
  tagIds?: number[]
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

// siteId : restreint aux catégories liées au site de l'article (comme le legacy). Omis → tous sites.
export async function fetchCategories(langId?: number, siteId?: number | string): Promise<NewsCategory[]> {
  const qs = new URLSearchParams()
  if (langId) qs.set('langId', String(langId))
  if (siteId) qs.set('siteId', String(siteId))
  const suffix = qs.toString() ? `?${qs.toString()}` : ''
  return apiFetch<NewsCategory[]>(`/melis/react-api/news/categories${suffix}`)
}

// ─── Tags (modular — only present when MelisCmsTags is active) ───────────────────
// Comme les catégories, la liste des tags disponibles est fournie par le back-office
// news (/melis/react-api/news/tags) qui lit melis_cms_tag ; la section n'est affichée
// que si le module MelisCmsTags est actif (détecté via /react-modules).

export async function fetchTags(langId?: number): Promise<NewsTag[]> {
  const qs = langId ? `?langId=${langId}` : ''
  return apiFetch<NewsTag[]>(`/melis/react-api/news/tags${qs}`)
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
