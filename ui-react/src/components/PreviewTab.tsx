import { useState, useEffect } from 'react'
import { ExternalLink, Loader2, AlertCircle } from 'lucide-react'
import { Button } from './ui/button'
import { t } from '../lib/i18n'
import * as newsApi from '../lib/news-api'

export function PreviewTab({ newsId, isNew }: {
  newsId: number | 'new'
  isNew: boolean
}) {
  const id = isNew ? undefined : Number(newsId)
  const [pages, setPages] = useState<newsApi.PreviewPage[]>([])
  const [selectedPageId, setSelectedPageId] = useState<number | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    if (isNew || !id) return

    setLoading(true)
    setError(null)
    newsApi.fetchNewsPreview(id)
      .then((data) => {
        const list = data.pages ?? []
        setPages(list)
        // Auto-sélection de la 1ʳᵉ page (comme le legacy : preview chargé d'office,
        // sélecteur masqué s'il n'y a qu'une seule page de détail).
        if (list.length > 0) setSelectedPageId(list[0].id)
      })
      .catch((e) => {
        setError(e instanceof Error ? e.message : t('err_load'))
      })
      .finally(() => setLoading(false))
  }, [id, isNew])

  if (isNew) {
    return (
      <section className="space-y-4">
        <div className="flex items-center gap-3">
          <span className="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground">{t('preview')}</span>
          <div className="h-px flex-1 bg-border" />
        </div>
        <p className="rounded-lg border border-dashed border-border bg-muted/20 px-4 py-3 text-xs text-muted-foreground">
          {t('media_save_first')}
        </p>
      </section>
    )
  }

  const selectedPage = pages.find((p) => p.id === selectedPageId)

  return (
    <section className="space-y-4">
      <div className="flex items-center gap-3">
        <span className="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground">{t('preview')}</span>
        <div className="h-px flex-1 bg-border" />
      </div>

      {loading && (
        <div className="flex items-center justify-center py-8">
          <Loader2 className="size-4 animate-spin text-muted-foreground" />
        </div>
      )}

      {error && (
        <div className="flex items-center gap-2 rounded-lg border border-destructive/50 bg-destructive/5 px-4 py-3 text-xs text-destructive">
          <AlertCircle className="size-4 shrink-0" />
          {error}
        </div>
      )}

      {!loading && pages.length === 0 && !error && (
        <p className="rounded-lg border border-dashed border-border bg-muted/20 px-4 py-3 text-xs text-muted-foreground">
          {t('no_preview_page')}
        </p>
      )}

      {!loading && pages.length > 0 && (
        <div className="space-y-3">
          {/* Sélecteur affiché uniquement si plusieurs pages de détail (parité legacy). */}
          <div className="flex items-center justify-between gap-3">
            {pages.length > 1 ? (
              <select
                value={selectedPageId ?? ''}
                onChange={(e) => setSelectedPageId(Number(e.target.value))}
                className="h-8 max-w-xs flex-1 rounded-md border border-input bg-background px-2.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                aria-label={t('preview_select_page')}
              >
                {pages.map((page) => (
                  <option key={page.id} value={page.id}>
                    {page.label}
                  </option>
                ))}
              </select>
            ) : (
              <span />
            )}

            {selectedPage && (
              <Button
                variant="outline"
                size="sm"
                className="gap-1.5 text-xs"
                onClick={() => window.open(selectedPage.url, '_blank')}
              >
                <ExternalLink className="size-3" />
                {t('preview_new_tab')}
              </Button>
            )}
          </div>

          {/* Preview chargé d'office, sans clic (comportement legacy). */}
          {selectedPage && (
            <div className="rounded-lg border border-border overflow-hidden bg-muted/5">
              <iframe
                key={selectedPage.id}
                src={selectedPage.url}
                className="w-full border-0"
                style={{ minHeight: '600px' }}
                title={`Preview: ${selectedPage.label}`}
              />
            </div>
          )}
        </div>
      )}
    </section>
  )
}
