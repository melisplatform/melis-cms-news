import { useState, useEffect, useCallback } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import {
  ArrowLeft, Save, Loader2, ChevronDown, ChevronUp,
  Plus, X, Eye, Calendar, Globe, Tag, Search, SlidersHorizontal,
  GitBranch, Image, Paperclip,
} from 'lucide-react'

import { Button } from './components/ui/button'
import { Input } from './components/ui/input'
import { RichEditor, type RichEditorEngine } from './components/ui/rich-editor'
import { cn } from './lib/utils'
import * as newsApi from './lib/news-api'

// Brick route base — host mounts the Component at NEWS_ROUTE and NEWS_ROUTE/:id.
const NEWS_ROUTE = '/melis-cms/news'

// Host tab API (exposed by MelisCore at runtime). Optional — guarded at call sites.
declare global {
  interface Window {
    __melisOpenTab?: (t: { id: string; label: string; path: string }) => void
    __melisCloseTab?: (id: string) => void
    /** Extension point: optional modules (e.g. melis-ai-community-extensions) register
     *  extra actions to render in each paragraph's header bar. */
    __melisNewsExtensions?: {
      renderParagraphActions?: (
        paraIdx: number,
        onContent: (text: string) => void,
        context: { newsId?: string; title?: string; subtitle?: string; paragraphs?: string[]; images?: string[] }
      ) => React.ReactNode
    }
  }
}

// ─── Types ─────────────────────────────────────────────────────────────────────

type Status = '0' | '1'

interface FormState {
  title: string
  subtitle: string
  paragraphs: string[]   // maps to cnews_paragraph1-4
  status: Status
  siteId: string
  publishDate: string
  unpublishDate: string
  sliderId: string
  categoryIds: number[]
  seo: newsApi.NewsSeo
  image1: string | null
  image2: string | null
  image3: string | null
  document1: string | null
  document2: string | null
  document3: string | null
}

const EMPTY_SEO: newsApi.NewsSeo = {
  url: '', urlRedirect: '', url301: '', metaTitle: '', metaDescription: '', canonical: '',
}

const EMPTY: FormState = {
  title: '', subtitle: '', paragraphs: [''],
  status: '0', siteId: '', publishDate: '', unpublishDate: '',
  sliderId: '', categoryIds: [], seo: EMPTY_SEO,
  image1: null, image2: null, image3: null,
  document1: null, document2: null, document3: null,
}

const MAX_PARAGRAPHS = 4  // cnews_paragraph1-4 in DB

function toInputDate(d: string | null | undefined): string {
  if (!d) return ''
  try { return new Date(d).toISOString().slice(0, 16) } catch { return '' }
}

function localeToFlag(locale: string): string {
  const map: Record<string, string> = {
    en_EN: '🇬🇧', fr_FR: '🇫🇷', de_DE: '🇩🇪', es_ES: '🇪🇸',
    it_IT: '🇮🇹', nl_NL: '🇳🇱', pt_PT: '🇵🇹',
  }
  return map[locale] ?? '🌐'
}

// ─── Sidebar section ──────────────────────────────────────────────────────────

function SidebarSection({
  title, icon: Icon, children, collapsible = false, defaultOpen = true,
}: {
  title: string; icon: React.ElementType; children: React.ReactNode
  collapsible?: boolean; defaultOpen?: boolean
}) {
  const [open, setOpen] = useState(defaultOpen)
  return (
    <div className="border-b border-border pb-4 last:border-0 last:pb-0">
      <button
        type="button"
        onClick={() => collapsible && setOpen((v) => !v)}
        className={cn(
          'flex w-full items-center justify-between py-1.5 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground',
          collapsible && 'cursor-pointer hover:text-foreground transition-colors',
        )}
      >
        <span className="flex items-center gap-1.5">
          <Icon className="size-3.5" />
          {title}
        </span>
        {collapsible && (open
          ? <ChevronUp className="size-3.5" />
          : <ChevronDown className="size-3.5" />)}
      </button>
      {open && <div className="mt-2.5 space-y-2.5">{children}</div>}
    </div>
  )
}

// ─── Paragraph editor ─────────────────────────────────────────────────────────

function ParagraphEditor({
  index, value, onChange, onRemove, canRemove, engine, extraActions,
}: {
  index: number; value: string; onChange: (v: string) => void
  onRemove: () => void; canRemove: boolean; engine: RichEditorEngine
  extraActions?: React.ReactNode
}) {
  return (
    <div>
      <div className="mb-1.5 flex items-center justify-between">
        <span className="text-[11px] font-medium text-muted-foreground tracking-wide">
          Paragraph {index + 1}
        </span>
        <div className="flex items-center gap-2">
          {extraActions}
          {canRemove && (
            <button
              type="button"
              onClick={onRemove}
              title="Remove paragraph"
              className="rounded p-0.5 text-muted-foreground/50 hover:text-destructive transition-colors"
            >
              <X className="size-3.5" />
            </button>
          )}
        </div>
      </div>
      <RichEditor
        engine={engine}
        value={value}
        onChange={onChange}
        placeholder="Write your content here…"
        minRows={6}
      />
    </div>
  )
}

// ─── Image slot ───────────────────────────────────────────────────────────────

function ImageSlot({ src, label }: { src: string | null; label: string }) {
  if (src) {
    return (
      <div className="group relative overflow-hidden rounded-xl border border-border bg-card">
        <img
          src={src}
          alt={label}
          className="h-32 w-full object-cover"
          onError={(e) => { (e.currentTarget as HTMLImageElement).src = '' }}
        />
        <div className="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
          <span className="text-xs font-medium text-white">{label}</span>
        </div>
      </div>
    )
  }
  return (
    <label className="group flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-border bg-card py-5 transition-colors hover:border-primary/50 hover:bg-primary/5">
      <div className="flex size-9 items-center justify-center rounded-full bg-muted group-hover:bg-primary/10 transition-colors">
        <Image className="size-4 text-muted-foreground group-hover:text-primary transition-colors" />
      </div>
      <p className="text-[11px] font-medium text-muted-foreground">{label}</p>
      <input type="file" accept="image/*" className="sr-only" />
    </label>
  )
}

// ─── Module-level cache ────────────────────────────────────────────────────────

type NewsItemCache = { form: FormState; langId: number }
const _newsCache = new Map<string, NewsItemCache>()

let _langCache:  newsApi.Language[]    | null = null
let _siteCache:  newsApi.Site[]        | null = null

// ─── Main component ───────────────────────────────────────────────────────────

export default function NewsFormPage() {
  const { id } = useParams<{ id?: string }>()
  const navigate = useNavigate()
  const isNew = !id || id === 'new'

  // The original used a per-module sub-tab store; the host only exposes flat top tabs via
  // window.__melisOpenTab / __melisCloseTab. We map the form view to a top tab at its own path,
  // and re-open with the same id to update its label (host treats same-id open as an update).
  const subTabPath = id ? `${NEWS_ROUTE}/${id}` : `${NEWS_ROUTE}/new`

  // eslint-disable-next-line react-hooks/exhaustive-deps
  useEffect(() => {
    // When opening an existing article, drop a lingering "new" tab if any.
    if (id && id !== 'new') window.__melisCloseTab?.(`${NEWS_ROUTE}/new`)
    window.__melisOpenTab?.({ id: subTabPath, label: isNew ? 'Nouvel article' : 'Chargement…', path: subTabPath })
  }, [])

  const [languages, setLanguages]       = useState<newsApi.Language[]>([])
  const [sites, setSites]               = useState<newsApi.Site[]>([])
  const [categories, setCategories]     = useState<newsApi.NewsCategory[]>([])
  const [previewUrl, setPreviewUrl]     = useState<string | null>(null)
  const [editorEngine, setEditorEngine] = useState<RichEditorEngine>('tiptap')

  const [langId, setLangId]   = useState<number>(1)
  const [form, setForm]       = useState<FormState>(EMPTY)
  const [loading, setLoading] = useState(false)
  const [saving, setSaving]   = useState(false)
  const [apiError, setApiError] = useState<string | null>(null)
  const [errors, setErrors]   = useState<Partial<Record<string, string>>>({})

  const loadNews = useCallback(async (newsId: number, targetLangId: number) => {
    try {
      const d = await newsApi.fetchNewsById(newsId, targetLangId)
      setForm({
        title:         d.title    ?? '',
        subtitle:      d.subtitle ?? '',
        paragraphs:    [d.paragraph1, d.paragraph2, d.paragraph3, d.paragraph4]
                         .filter((_, i) => i === 0 || d[`paragraph${i + 1}` as keyof typeof d]),
        status:        d.status === 1 ? '1' : '0',
        siteId:        d.siteId ? String(d.siteId) : '',
        publishDate:   toInputDate(d.publishDate),
        unpublishDate: toInputDate(d.unpublishDate),
        sliderId:      d.sliderId ? String(d.sliderId) : '',
        categoryIds:   d.categoryIds ?? [],
        seo:           d.seo ?? EMPTY_SEO,
        image1:        d.image1 ?? null,
        image2:        d.image2 ?? null,
        image3:        d.image3 ?? null,
        document1:     d.document1 ?? null,
        document2:     d.document2 ?? null,
        document3:     d.document3 ?? null,
      })
    } catch (e) {
      setApiError(e instanceof Error ? e.message : 'Error loading article')
    }
  }, [])

  useEffect(() => {
    if (!_newsCache.has(id ?? '')) setLoading(true)

    const tasks: Promise<unknown>[] = [
      _langCache
        ? Promise.resolve().then(() => { setLanguages(_langCache!); if (!_newsCache.has(id ?? '')) setLangId(_langCache![0]?.id ?? 1) })
        : newsApi.fetchLanguages().then((langs) => { _langCache = langs; setLanguages(langs); if (!_newsCache.has(id ?? '') && langs.length > 0) setLangId(langs[0].id) }).catch(() => {}),
      _siteCache
        ? Promise.resolve().then(() => setSites(_siteCache!))
        : newsApi.fetchSites().then(s => { _siteCache = s; setSites(s) }).catch(() => {}),
      newsApi.fetchCategories().then(setCategories).catch(() => {}),
    ]

    if (!isNew) {
      const cached = _newsCache.get(id!)
      if (cached) {
        setForm(cached.form)
        setLangId(cached.langId)
      } else {
        tasks.push(loadNews(Number(id), langId))
      }
      tasks.push(newsApi.fetchNewsPreviewUrl(Number(id)).then(setPreviewUrl).catch(() => {}))
    }

    Promise.all(tasks).finally(() => setLoading(false))
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id, isNew])

  useEffect(() => {
    if (!isNew && id && form.title) {
      _newsCache.set(id, { form, langId })
    }
  }, [form, langId, id, isNew])

  useEffect(() => {
    if (!isNew && !loading) {
      newsApi.fetchCategories(langId).then(setCategories).catch(() => {})
      loadNews(Number(id), langId)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [langId])

  useEffect(() => {
    if (!isNew && form.title) {
      // Re-opening with the same id updates the tab label (host treats same-id as update).
      window.__melisOpenTab?.({ id: subTabPath, label: form.title, path: subTabPath })
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [form.title])

  function set<K extends keyof FormState>(key: K, value: FormState[K]) {
    setForm((prev) => ({ ...prev, [key]: value }))
    setErrors((prev) => ({ ...prev, [key]: undefined }))
  }

  function setSeo(key: keyof newsApi.NewsSeo, value: string) {
    setForm((prev) => ({ ...prev, seo: { ...prev.seo, [key]: value } }))
  }

  function updateParagraph(i: number, value: string) {
    setForm((prev) => {
      const p = [...prev.paragraphs]; p[i] = value
      return { ...prev, paragraphs: p }
    })
  }

  function addParagraph() {
    if (form.paragraphs.length < MAX_PARAGRAPHS) {
      setForm((prev) => ({ ...prev, paragraphs: [...prev.paragraphs, ''] }))
    }
  }

  function removeParagraph(i: number) {
    setForm((prev) => {
      const p = prev.paragraphs.filter((_, idx) => idx !== i)
      return { ...prev, paragraphs: p.length ? p : [''] }
    })
  }

  function toggleCategory(catId: number) {
    setForm((prev) => {
      const ids = prev.categoryIds.includes(catId)
        ? prev.categoryIds.filter((c) => c !== catId)
        : [...prev.categoryIds, catId]
      return { ...prev, categoryIds: ids }
    })
  }

  function validate() {
    const errs: Partial<Record<string, string>> = {}
    if (!form.title.trim()) errs.title  = 'Title is required'
    if (!form.siteId)       errs.siteId = 'Please select a site'
    setErrors(errs)
    return Object.keys(errs).length === 0
  }

  async function handleSave() {
    if (!validate()) return
    setSaving(true); setApiError(null)
    try {
      const payload: newsApi.NewsSavePayload = {
        id:            isNew ? null : Number(id),
        langId,
        title:         form.title.trim(),
        subtitle:      form.subtitle.trim() || undefined,
        paragraphs:    form.paragraphs,
        status:        form.status === '1' ? 1 : 0,
        siteId:        Number(form.siteId),
        publishDate:   form.publishDate
          ? new Date(form.publishDate).toISOString().slice(0, 19).replace('T', ' ')
          : null,
        unpublishDate: form.unpublishDate
          ? new Date(form.unpublishDate).toISOString().slice(0, 19).replace('T', ' ')
          : null,
        sliderId:    form.sliderId ? Number(form.sliderId) : null,
        categoryIds: form.categoryIds,
        seo:         form.seo,
      }
      const res = await newsApi.saveNews(payload)
      const lang = (document.documentElement.lang || 'en').slice(0, 2)
      window.postMessage({ __melisNotif: true, kind: 'ok', title: 'News', message: lang === 'fr' ? 'Article enregistré.' : 'Article saved.' }, '*')
      // Saved: if we were on the "new" tab, close it; open/activate the saved article's tab.
      if (isNew) window.__melisCloseTab?.(`${NEWS_ROUTE}/new`)
      window.__melisOpenTab?.({ id: `${NEWS_ROUTE}/${res.id}`, label: form.title.trim() || `Article #${res.id}`, path: `${NEWS_ROUTE}/${res.id}` })
      navigate(`${NEWS_ROUTE}/${res.id}`, { replace: true })
    } catch (e) {
      setApiError(e instanceof Error ? e.message : 'Error saving article')
    } finally {
      setSaving(false)
    }
  }

  const isPublished  = form.status === '1'
  const statusClass  = isPublished
    ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
    : 'bg-amber-500/10 text-amber-600 border-amber-500/20'
  const statusDot    = isPublished ? 'bg-emerald-500' : 'bg-amber-400'

  if (loading) {
    return (
      <div className="flex min-h-[40vh] items-center justify-center">
        <Loader2 className="size-5 animate-spin text-muted-foreground" />
      </div>
    )
  }

  return (
    <div className="flex flex-col">

      {/* Sticky header */}
      <header className="sticky top-0 z-10 flex items-center justify-between border-b border-border bg-background/95 px-5 py-2.5 backdrop-blur-sm">
        <div className="flex items-center gap-2.5 min-w-0">
          <Button variant="ghost" size="icon" className="size-8 shrink-0" onClick={() => navigate(NEWS_ROUTE)}>
            <ArrowLeft className="size-4" />
          </Button>
          <div className="min-w-0">
            <p className="text-[11px] leading-none text-muted-foreground mb-0.5">News</p>
            <h1 className="truncate text-sm font-semibold leading-tight max-w-xs">
              {form.title || (isNew ? 'New article' : `Article #${id}`)}
            </h1>
          </div>
        </div>

        <div className="flex items-center gap-2">
          {previewUrl && (
            <Button
              variant="ghost"
              size="sm"
              className="h-8 gap-1.5 text-muted-foreground text-xs"
              onClick={() => window.open(previewUrl, '_blank')}
            >
              <Eye className="size-3.5" />
              Preview
            </Button>
          )}

          <button
            type="button"
            onClick={() => set('status', isPublished ? '0' : '1')}
            className={cn(
              'inline-flex cursor-pointer items-center gap-1.5 rounded-md border px-2.5 py-1.5 text-xs font-medium transition-colors select-none',
              statusClass,
            )}
          >
            <span className={cn('size-1.5 rounded-full', statusDot)} />
            {isPublished ? 'Published' : 'Draft'}
          </button>

          {apiError && <span className="text-xs text-destructive">{apiError}</span>}

          <Button size="sm" className="h-8 gap-1.5 min-w-[88px] text-xs" onClick={handleSave} disabled={saving}>
            {saving
              ? <><Loader2 className="size-3.5 animate-spin" />Saving…</>
              : <><Save className="size-3.5" />Save</>}
          </Button>
        </div>
      </header>

      {/* Two-column body */}
      <div className="flex items-start">

        {/* Main content */}
        <main className="flex-1 min-w-0 px-8 py-6 space-y-6">

          {/* Language switcher */}
          <div className="flex items-center gap-1 rounded-lg bg-muted p-1 w-fit">
            {languages.length > 0
              ? languages.map((lang) => (
                  <button
                    key={lang.id}
                    type="button"
                    onClick={() => setLangId(lang.id)}
                    className={cn(
                      'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-all select-none',
                      langId === lang.id
                        ? 'bg-background text-foreground shadow-sm'
                        : 'text-muted-foreground hover:text-foreground',
                    )}
                  >
                    <span className="text-sm leading-none">{localeToFlag(lang.locale)}</span>
                    {lang.name}
                  </button>
                ))
              : ['English', 'Français'].map((n) => (
                  <span key={n} className="px-3 py-1.5 text-xs text-muted-foreground">{n}</span>
                ))
            }
          </div>

          {/* Title */}
          <div>
            <input
              value={form.title}
              onChange={(e) => set('title', e.target.value)}
              placeholder="Article title…"
              autoFocus={isNew}
              className={cn(
                'w-full bg-transparent text-[1.65rem] font-bold tracking-tight leading-tight placeholder:text-muted-foreground/40 focus:outline-none border-b-2 border-transparent transition-colors pb-1',
                errors.title ? 'border-destructive' : 'focus:border-border',
              )}
            />
            {errors.title && <p className="mt-1 text-xs text-destructive">{errors.title}</p>}
          </div>

          {/* Subtitle */}
          <input
            value={form.subtitle}
            onChange={(e) => set('subtitle', e.target.value)}
            placeholder="Add a subtitle…"
            className="w-full bg-transparent text-[1.05rem] text-muted-foreground placeholder:text-muted-foreground/40 focus:outline-none border-b border-transparent focus:border-border transition-colors pb-1"
          />

          {/* Body */}
          <section className="space-y-4">
            <div className="flex items-center gap-3">
              <span className="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground">Body</span>
              <div className="h-px flex-1 bg-border" />
              <div className="flex items-center gap-1 rounded-md bg-muted p-0.5">
                {(['tiptap', 'tinymce'] as RichEditorEngine[]).map((eng) => (
                  <button
                    key={eng}
                    type="button"
                    onClick={() => setEditorEngine(eng)}
                    className={cn(
                      'rounded px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide transition-colors select-none',
                      editorEngine === eng
                        ? 'bg-background text-foreground shadow-sm'
                        : 'text-muted-foreground hover:text-foreground',
                    )}
                  >
                    {eng}
                  </button>
                ))}
              </div>
              <span className="text-[11px] text-muted-foreground/60">{form.paragraphs.length}/{MAX_PARAGRAPHS}</span>
            </div>

            {form.paragraphs.map((p, i) => (
              <ParagraphEditor
                key={i}
                index={i}
                value={p}
                onChange={(v) => updateParagraph(i, v)}
                onRemove={() => removeParagraph(i)}
                canRemove={form.paragraphs.length > 1}
                engine={editorEngine}
                extraActions={window.__melisNewsExtensions?.renderParagraphActions?.(
                  i,
                  (text) => updateParagraph(i, text),
                  {
                    newsId: isNew ? undefined : id,
                    title: form.title,
                    subtitle: form.subtitle,
                    paragraphs: form.paragraphs,
                    images: [form.image1, form.image2, form.image3].filter((x): x is string => x !== null),
                  }
                )}
              />
            ))}

            {form.paragraphs.length < MAX_PARAGRAPHS && (
              <button
                type="button"
                onClick={addParagraph}
                className="flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-border py-3 text-xs font-medium text-muted-foreground hover:border-primary/60 hover:text-primary transition-colors"
              >
                <Plus className="size-3.5" />
                Add paragraph
              </button>
            )}
          </section>

          {/* Media */}
          <section className="space-y-4">
            <div className="flex items-center gap-3">
              <span className="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground">Media</span>
              <div className="h-px flex-1 bg-border" />
            </div>

            <div className="space-y-3">
              <p className="text-[11px] font-medium text-muted-foreground uppercase tracking-wide">Images</p>
              <div className="grid grid-cols-3 gap-3">
                <ImageSlot src={form.image1} label="Image 1" />
                <ImageSlot src={form.image2} label="Image 2" />
                <ImageSlot src={form.image3} label="Image 3" />
              </div>
            </div>

            <div className="space-y-3">
              <p className="text-[11px] font-medium text-muted-foreground uppercase tracking-wide">File attachments</p>
              <label className="group flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-border px-4 py-3 transition-colors hover:border-primary/50 hover:bg-primary/5">
                <div className="flex size-8 items-center justify-center rounded-full bg-muted group-hover:bg-primary/10 transition-colors">
                  <Paperclip className="size-4 text-muted-foreground group-hover:text-primary transition-colors" />
                </div>
                <span className="text-xs text-muted-foreground">Click to attach a file</span>
                <input type="file" className="sr-only" />
              </label>
            </div>
          </section>
        </main>

        {/* Sidebar */}
        <aside className="w-64 shrink-0 self-start sticky top-[57px] border-l border-border bg-muted/10 p-4 space-y-4">

          <SidebarSection title="Status" icon={GitBranch}>
            <div className="flex items-center justify-between gap-2">
              <button
                type="button"
                onClick={() => set('status', isPublished ? '0' : '1')}
                className={cn(
                  'inline-flex cursor-pointer items-center gap-1.5 rounded-md border px-2.5 py-1.5 text-xs font-medium transition-colors select-none',
                  statusClass,
                )}
              >
                <span className={cn('size-1.5 rounded-full', statusDot)} />
                {isPublished ? 'Published' : 'Draft'}
              </button>
              <Button variant="outline" size="sm" className="h-7 gap-1.5 text-xs px-2.5">
                <GitBranch className="size-3" />
                Workflow
              </Button>
            </div>
          </SidebarSection>

          <SidebarSection title="Publication" icon={Calendar}>
            <div>
              <label className="mb-1 block text-[11px] text-muted-foreground">Publish on</label>
              <Input
                type="datetime-local"
                value={form.publishDate}
                onChange={(e) => set('publishDate', e.target.value)}
                className="h-8 text-xs"
              />
            </div>
            <div>
              <label className="mb-1 block text-[11px] text-muted-foreground">Unpublish on</label>
              <Input
                type="datetime-local"
                value={form.unpublishDate}
                onChange={(e) => set('unpublishDate', e.target.value)}
                className="h-8 text-xs"
              />
            </div>
          </SidebarSection>

          <SidebarSection title="Site" icon={Globe}>
            <select
              value={form.siteId}
              onChange={(e) => set('siteId', e.target.value)}
              className={cn(
                'h-8 w-full rounded-md border border-input bg-background px-2.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring',
                errors.siteId && 'border-destructive',
              )}
            >
              <option value="">Choose a site…</option>
              {sites.map((s) => (
                <option key={s.id} value={String(s.id)}>{s.name}</option>
              ))}
            </select>
            {errors.siteId && <p className="mt-1 text-xs text-destructive">{errors.siteId}</p>}
          </SidebarSection>

          <SidebarSection title="Categories" icon={Tag} collapsible defaultOpen>
            {categories.length === 0 ? (
              <p className="text-[11px] text-muted-foreground/60">No categories available</p>
            ) : (
              <div className="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                {categories.map((cat) => (
                  <label key={cat.id} className="flex cursor-pointer items-center gap-2 rounded-md px-1 py-0.5 hover:bg-accent transition-colors">
                    <input
                      type="checkbox"
                      checked={form.categoryIds.includes(cat.id)}
                      onChange={() => toggleCategory(cat.id)}
                      className="size-3.5 accent-primary"
                    />
                    <span className="text-xs">{cat.name || `Category #${cat.id}`}</span>
                  </label>
                ))}
              </div>
            )}
          </SidebarSection>

          <SidebarSection title="SEO" icon={Search} collapsible defaultOpen={false}>
            {(
              [
                { key: 'metaTitle',       label: 'Meta title',       type: 'input' },
                { key: 'metaDescription', label: 'Meta description',  type: 'textarea' },
                { key: 'url',             label: 'URL',               type: 'input' },
                { key: 'urlRedirect',     label: 'URL redirect',      type: 'input' },
                { key: 'url301',          label: 'URL 301',           type: 'input' },
                { key: 'canonical',       label: 'Canonical URL',     type: 'input' },
              ] as { key: keyof newsApi.NewsSeo; label: string; type: 'input' | 'textarea' }[]
            ).map(({ key, label, type }) => (
              <div key={key}>
                <label className="mb-1 block text-[11px] text-muted-foreground">{label}</label>
                {type === 'textarea' ? (
                  <textarea
                    value={form.seo[key]}
                    onChange={(e) => setSeo(key, e.target.value)}
                    rows={2}
                    className="w-full resize-none rounded-md border border-input bg-background px-2.5 py-1.5 text-xs placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-ring"
                  />
                ) : (
                  <Input
                    value={form.seo[key]}
                    onChange={(e) => setSeo(key, e.target.value)}
                    className="h-7 text-xs"
                  />
                )}
              </div>
            ))}
          </SidebarSection>

          <SidebarSection title="Slider" icon={SlidersHorizontal} collapsible defaultOpen={false}>
            <Input
              value={form.sliderId}
              onChange={(e) => set('sliderId', e.target.value)}
              placeholder="Slider ID…"
              className="h-8 text-xs"
              type="number"
              min={0}
            />
          </SidebarSection>
        </aside>
      </div>
    </div>
  )
}
