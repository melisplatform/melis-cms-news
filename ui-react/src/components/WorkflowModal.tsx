import { useCallback, useEffect, useState } from 'react'
import { createPortal } from 'react-dom'
import { GitBranch, X, Check, Ban, Loader2, Send, MessageSquare, ChevronLeft, ChevronRight } from 'lucide-react'

import { Button } from './ui/button'
import { cn } from '../lib/utils'
import * as wf from '../lib/workflow-api'

/**
 * Modale « Workflow » (validation) — réimplémentation React de l'outil legacy
 * MelisSmallBusiness (public/js/workflow.js + render-workflow-modal*). Apportée par
 * MelisSmallBusiness : rendue par la brique hôte (ici MelisCmsNews) SEULEMENT si le
 * module est actif. Deux zones : « Demander une validation » (rôle → utilisateur →
 * envoyer) et « Historique » (avec Valider / Refuser quand on est le destinataire).
 */

type Lang = 'fr' | 'en'
const DICT: Record<Lang, Record<string, string>> = {
  fr: {
    title: 'Workflow — actions', ask: 'Demander une validation', role: 'Rôle', user: 'Utilisateur',
    chooseRole: 'Choisir un rôle…', chooseUser: 'Choisir un utilisateur…', send: 'Envoyer la requête',
    history: 'Historique', date: 'Date', status: 'Statut', to: 'Pour / Rôle', from: 'De', action: 'Action',
    validate: 'Valider', refuse: 'Refuser', empty: 'Aucune demande de validation pour le moment.',
    close: 'Fermer', VALIDATION: 'Demande', VALIDATED: 'Validé', REFUSED: 'Refusé',
    sent: 'Requête envoyée', answered: 'Réponse enregistrée', err: 'Une erreur est survenue',
    needRole: 'Choisissez un rôle', needUser: 'Choisissez un utilisateur', loading: 'Chargement…',
    commentDesc: 'Ajoutez une note à cette action (facultatif).',
    commentPlaceholder: 'Votre commentaire…', saveComment: 'Enregistrer', skip: 'Passer',
    commentSaved: 'Commentaire enregistré',
    page: 'Page', prev: 'Précédent', next: 'Suivant',
  },
  en: {
    title: 'Workflow — actions', ask: 'Ask for validation', role: 'Role', user: 'User',
    chooseRole: 'Choose a role…', chooseUser: 'Choose a user…', send: 'Send request',
    history: 'History', date: 'Date', status: 'Status', to: 'To / Role', from: 'From', action: 'Action',
    validate: 'Validate', refuse: 'Refuse', empty: 'No validation demand yet.',
    close: 'Close', VALIDATION: 'Demand', VALIDATED: 'Validated', REFUSED: 'Refused',
    sent: 'Request sent', answered: 'Answer saved', err: 'An error occurred',
    needRole: 'Choose a role', needUser: 'Choose a user', loading: 'Loading…',
    commentDesc: 'Add a note to this action (optional).',
    commentPlaceholder: 'Your comment…', saveComment: 'Save', skip: 'Skip',
    commentSaved: 'Comment saved',
    page: 'Page', prev: 'Previous', next: 'Next',
  },
}

const selectCss = 'h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring'

// Shared history grid — a FIXED action column (176px) keeps every row's columns aligned whether the
// row shows the Validate/Refuse buttons or just a "—". Header and data rows must use the same track.
const HIST_GRID = 'grid grid-cols-[140px_92px_1fr_1fr_176px] gap-2'

// History is paginated client-side (the backend returns the item's events, capped at 100).
const PAGE_SIZE = 6

function StatusBadge({ action, label }: { action: wf.WorkflowAction; label: string }) {
  const cls =
    action === 'VALIDATED' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400'
    : action === 'REFUSED' ? 'bg-red-500/15 text-red-600 dark:text-red-400'
    : 'bg-amber-500/15 text-amber-600 dark:text-amber-400'
  return <span className={cn('inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium', cls)}>{label}</span>
}

export default function WorkflowModal({ ctx, appLang, onClose }: {
  ctx: wf.WorkflowContext
  appLang: string
  onClose: () => void
}) {
  const t = DICT[appLang === 'fr' ? 'fr' : 'en']

  // Le backend legacy renvoie parfois des CLÉS de traduction brutes (« tr_… ») non résolues :
  // on ne les affiche jamais telles quelles → message local par défaut.
  const cleanMsg = (raw: string | undefined, fallback: string) =>
    raw && !raw.startsWith('tr_') ? raw : fallback

  const [roles, setRoles] = useState<wf.WorkflowRole[]>([])
  const [users, setUsers] = useState<wf.WorkflowUser[]>([])
  const [roleId, setRoleId] = useState<number | ''>('')
  const [userId, setUserId] = useState<number | ''>('')
  const [usersLoading, setUsersLoading] = useState(false)
  const [history, setHistory] = useState<wf.WorkflowEvent[]>([])
  const [histLoading, setHistLoading] = useState(true)
  const [sending, setSending] = useState(false)
  const [busyEvent, setBusyEvent] = useState<number | null>(null)
  const [notice, setNotice] = useState<{ ok: boolean; msg: string } | null>(null)
  // Étape commentaire inline (remplace la 2ᵉ modale legacy) après une demande / validation / refus.
  const [commentStep, setCommentStep] = useState<{ action: 'ask' | 'validate' | 'refuse' } | null>(null)
  const [commentText, setCommentText] = useState('')
  const [commentSaving, setCommentSaving] = useState(false)
  const [page, setPage] = useState(1)

  const pageCount = Math.max(1, Math.ceil(history.length / PAGE_SIZE))
  useEffect(() => { setPage((pp) => Math.min(pp, pageCount)) }, [pageCount])
  const pageItems = history.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE)

  const loadHistory = useCallback(async () => {
    setHistLoading(true)
    try {
      const h = await wf.fetchWorkflowHistory(ctx.wfType, ctx.wfId)
      setHistory(h.items)
    } catch { setHistory([]) } finally { setHistLoading(false) }
  }, [ctx.wfType, ctx.wfId])

  useEffect(() => { wf.fetchWorkflowRoles().then(setRoles).catch(() => {}) }, [])
  useEffect(() => { loadHistory() }, [loadHistory])
  useEffect(() => {
    const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') onClose() }
    window.addEventListener('keydown', onKey)
    return () => window.removeEventListener('keydown', onKey)
  }, [onClose])

  // A real role targets EVERYONE with that role → no user to pick. Only "1 person only" (-1)
  // requires selecting a specific user, so the user list is loaded (all users) only in that mode.
  const isPersonMode = roleId === -1
  useEffect(() => {
    if (!isPersonMode) { setUsers([]); setUserId(''); return }
    let cancelled = false
    setUsersLoading(true); setUserId('')
    wf.fetchWorkflowUsers(-1)
      .then((u) => { if (!cancelled) setUsers(u) })
      .catch(() => { if (!cancelled) setUsers([]) })
      .finally(() => { if (!cancelled) setUsersLoading(false) })
    return () => { cancelled = true }
  }, [isPersonMode])

  async function ask() {
    if (roleId === '') { setNotice({ ok: false, msg: t.needRole }); return }
    if (isPersonMode && userId === '') { setNotice({ ok: false, msg: t.needUser }); return }
    setSending(true); setNotice(null)
    const res = await wf.sendWorkflowAsk(ctx, Number(roleId), isPersonMode ? Number(userId) : null)
    setSending(false)
    if (res.success) {
      setRoleId(''); setUserId('')
      loadHistory()
      setCommentText(''); setCommentStep({ action: 'ask' })   // → étape commentaire inline
    } else {
      setNotice({ ok: false, msg: t.err })
    }
  }

  async function answer(ev: wf.WorkflowEvent, action: 'validate' | 'refuse') {
    setBusyEvent(ev.wfeId); setNotice(null)
    const res = await wf.sendWorkflowAction(ctx, action, ev)
    setBusyEvent(null)
    if (res.success) {
      loadHistory()
      setCommentText(''); setCommentStep({ action })          // → étape commentaire inline
    } else {
      setNotice({ ok: false, msg: cleanMsg(res.textMessage, t.err) })
    }
  }

  // Enregistre (ou passe) le commentaire de l'action puis revient à l'historique. L'action est
  // déjà committée : un échec du commentaire n'annule rien (best-effort), on ferme l'étape.
  async function saveComment(withText: boolean) {
    if (!commentStep) return
    setCommentSaving(true)
    await wf.sendWorkflowComment(ctx, commentStep.action, withText ? commentText.trim() : '')
    setCommentSaving(false)
    setCommentStep(null); setCommentText('')
    setNotice({ ok: true, msg: t.commentSaved })
    loadHistory()
  }

  const doneLabel = commentStep
    ? (commentStep.action === 'ask' ? t.sent : commentStep.action === 'validate' ? t.VALIDATED : t.REFUSED)
    : ''

  return createPortal(
    <div
      className="fixed inset-0 z-[9999] flex items-start justify-center overflow-auto bg-black/50 p-6 backdrop-blur-sm"
      onClick={(e) => { if (e.target === e.currentTarget) onClose() }}
    >
      <div className="mt-[6vh] w-full max-w-2xl rounded-2xl border border-border bg-card text-card-foreground shadow-2xl">
        {/* Header */}
        <div className="flex items-start gap-3 border-b border-border px-5 py-4">
          <span className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/15 text-primary">
            <GitBranch className="size-4" />
          </span>
          <div className="min-w-0 flex-1">
            <h2 className="text-[15px] font-semibold leading-tight">{t.title}</h2>
            <p className="truncate text-xs text-muted-foreground">{ctx.wfDetails}</p>
          </div>
          <button onClick={onClose} className="rounded-md p-1 text-muted-foreground hover:bg-accent hover:text-foreground" title={t.close}>
            <X className="size-4" />
          </button>
        </div>

        <div className="flex flex-col gap-5 p-5">
          {notice && (
            <div className={cn('rounded-lg border px-3 py-2 text-sm',
              notice.ok ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                : 'border-red-500/40 bg-red-500/10 text-red-600 dark:text-red-400')}>
              {notice.msg}
            </div>
          )}

          {/* Comment step (inline — replaces the legacy stacked comment modal). */}
          {commentStep && (
            <section className="rounded-xl border border-border p-4">
              <div className="mb-3 flex items-center gap-2.5">
                <span className="flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                  <Check className="size-4" />
                </span>
                <div>
                  <h3 className="text-sm font-semibold leading-tight">{doneLabel}</h3>
                  <p className="text-xs text-muted-foreground">{t.commentDesc}</p>
                </div>
              </div>
              <textarea
                autoFocus
                value={commentText}
                onChange={(e) => setCommentText(e.target.value)}
                placeholder={t.commentPlaceholder}
                rows={4}
                className="w-full resize-none rounded-md border border-input bg-background p-3 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
              />
              <div className="mt-3 flex justify-end gap-2">
                <Button variant="ghost" size="sm" onClick={() => saveComment(false)} disabled={commentSaving}>{t.skip}</Button>
                <Button size="sm" onClick={() => saveComment(true)} disabled={commentSaving || !commentText.trim()}>
                  {commentSaving ? <Loader2 className="size-4 animate-spin" /> : <MessageSquare className="size-4" />}
                  {t.saveComment}
                </Button>
              </div>
            </section>
          )}

          {!commentStep && (<>
          {/* Ask for validation */}
          <section className="rounded-xl border border-border p-4">
            <h3 className="mb-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">{t.ask}</h3>
            <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
              <label className="flex-1">
                <span className="mb-1 block text-[11px] text-muted-foreground">{t.role}</span>
                <select className={selectCss} value={roleId} onChange={(e) => setRoleId(e.target.value ? Number(e.target.value) : '')}>
                  <option value="">{t.chooseRole}</option>
                  {roles.map((r) => <option key={r.id} value={r.id}>{r.name}</option>)}
                </select>
              </label>
              {/* User picker only for "1 person only" — a real role targets everyone with it. */}
              {isPersonMode && (
                <label className="flex-1">
                  <span className="mb-1 block text-[11px] text-muted-foreground">{t.user}</span>
                  <select className={selectCss} value={userId} disabled={usersLoading}
                    onChange={(e) => setUserId(e.target.value ? Number(e.target.value) : '')}>
                    <option value="">{usersLoading ? t.loading : t.chooseUser}</option>
                    {users.map((u) => <option key={u.id} value={u.id}>{u.name}</option>)}
                  </select>
                </label>
              )}
              <Button size="sm" className="h-9 shrink-0" onClick={ask} disabled={sending || roleId === '' || (isPersonMode && userId === '')}>
                {sending ? <Loader2 className="size-4 animate-spin" /> : <Send className="size-4" />}
                {t.send}
              </Button>
            </div>
          </section>

          {/* History */}
          <section>
            <h3 className="mb-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">{t.history}</h3>
            <div className="overflow-hidden rounded-xl border border-border">
              {/* Fixed ACTION column (176px) so every row aligns whether it has buttons or "—". */}
              <div className={cn(HIST_GRID, 'border-b border-border bg-muted/40 px-3 py-2 text-[11px] font-medium uppercase text-muted-foreground')}>
                <span>{t.date}</span><span>{t.status}</span><span>{t.to}</span><span>{t.from}</span><span className="text-right">{t.action}</span>
              </div>
              {histLoading ? (
                <div className="flex items-center gap-2 px-3 py-6 text-sm text-muted-foreground"><Loader2 className="size-4 animate-spin" />{t.loading}</div>
              ) : history.length === 0 ? (
                <div className="px-3 py-6 text-center text-sm text-muted-foreground">{t.empty}</div>
              ) : (
                pageItems.map((ev) => (
                  <div key={ev.wfeId} className="border-b border-border/60 last:border-b-0">
                    <div className={cn(HIST_GRID, 'items-center px-3 py-2 text-sm')}>
                      <span className="whitespace-nowrap text-xs text-muted-foreground">{ev.date}</span>
                      <span><StatusBadge action={ev.action} label={t[ev.action]} /></span>
                      <span className="truncate">{[ev.toUser, ev.toRole].filter(Boolean).join(' / ')}</span>
                      <span className="truncate">{ev.fromUser}</span>
                      <span className="flex h-7 items-center justify-end gap-1.5">
                        {ev.canAnswer ? (
                          <>
                            <Button size="sm" variant="outline" className="h-7 gap-1 border-emerald-500/40 px-2 text-xs text-emerald-600 hover:bg-emerald-500/10 dark:text-emerald-400"
                              onClick={() => answer(ev, 'validate')} disabled={busyEvent === ev.wfeId}>
                              {busyEvent === ev.wfeId ? <Loader2 className="size-3 animate-spin" /> : <Check className="size-3" />}{t.validate}
                            </Button>
                            <Button size="sm" variant="outline" className="h-7 gap-1 border-red-500/40 px-2 text-xs text-red-600 hover:bg-red-500/10 dark:text-red-400"
                              onClick={() => answer(ev, 'refuse')} disabled={busyEvent === ev.wfeId}>
                              <Ban className="size-3" />{t.refuse}
                            </Button>
                          </>
                        ) : <span className="text-muted-foreground">—</span>}
                      </span>
                    </div>
                    {/* Optional note left with the action. */}
                    {ev.comment && (
                      <div className="flex items-start gap-1.5 px-3 pb-2 pl-[calc(0.75rem+140px)] text-xs text-muted-foreground">
                        <MessageSquare className="mt-0.5 size-3 shrink-0 opacity-70" />
                        <span className="italic">{ev.comment}</span>
                      </div>
                    )}
                  </div>
                ))
              )}

              {/* Pagination (client-side) — shown only when the history spans more than one page. */}
              {!histLoading && pageCount > 1 && (
                <div className="flex items-center justify-between border-t border-border bg-muted/30 px-3 py-2">
                  <span className="text-xs text-muted-foreground">{t.page} {page}/{pageCount}</span>
                  <div className="flex gap-1">
                    <Button size="sm" variant="outline" className="h-7 w-7 p-0" onClick={() => setPage((pp) => Math.max(1, pp - 1))} disabled={page <= 1} title={t.prev}>
                      <ChevronLeft className="size-4" />
                    </Button>
                    <Button size="sm" variant="outline" className="h-7 w-7 p-0" onClick={() => setPage((pp) => Math.min(pageCount, pp + 1))} disabled={page >= pageCount} title={t.next}>
                      <ChevronRight className="size-4" />
                    </Button>
                  </div>
                </div>
              )}
            </div>
          </section>
          </>)}
        </div>

        {/* Footer */}
        <div className="flex justify-end border-t border-border px-5 py-3">
          <Button variant="outline" size="sm" onClick={onClose}>{t.close}</Button>
        </div>
      </div>
    </div>,
    document.body,
  )
}
