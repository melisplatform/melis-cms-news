/**
 * Client de la modale « Workflow » (validation) — apportée par MelisSmallBusiness.
 *
 * Lectures : couche JSON ajoutée dans MelisSmallBusiness (MelisReactApiWorkflowController) :
 *   GET /melis/react-api/workflow/roles | /users?roleId= | /history?type=&id=
 * Mutations : endpoints legacy ÉPROUVÉS de MelisWorkflow (mêmes appels que public/js/workflow.js) :
 *   GET /melis/MelisSmallBusiness/MelisWorkflow/saveWorkflowAsk?...
 *   GET /melis/MelisSmallBusiness/MelisWorkflow/saveWorkflowActions?wfaction=validate|refuse&...
 * La brique ne peut pas importer l'hôte : client autonome (X-Requested-With + cookies).
 */

const XHR = { 'X-Requested-With': 'XMLHttpRequest' } as const

export interface WorkflowRole { id: number; name: string }
export interface WorkflowUser { id: number; name: string }
export type WorkflowAction = 'VALIDATION' | 'VALIDATED' | 'REFUSED'
export interface WorkflowEvent {
  wfeId: number
  wfeWfId: number
  date: string
  action: WorkflowAction
  toUser: string
  toRole: string
  fromUser: string
  canAnswer: boolean
  /** Optional note left with the action (matched from the comment timeline). */
  comment?: string
}
export interface WorkflowHistory { currentUserId: number; items: WorkflowEvent[] }

export interface WorkflowMutationResult {
  success: boolean
  textTitle?: string
  textMessage?: string
  errors?: unknown
}

async function readJson<T>(url: string): Promise<T> {
  const res = await fetch(url, { headers: XHR, credentials: 'include' })
  const data = (await res.json()) as { success: boolean; data?: T; error?: string }
  if (!res.ok || !data.success) throw new Error(data.error ?? `HTTP ${res.status}`)
  return data.data as T
}

export async function fetchWorkflowRoles(): Promise<WorkflowRole[]> {
  return readJson<WorkflowRole[]>('/melis/react-api/workflow/roles')
}

export async function fetchWorkflowUsers(roleId: number): Promise<WorkflowUser[]> {
  return readJson<WorkflowUser[]>(`/melis/react-api/workflow/users?roleId=${encodeURIComponent(String(roleId))}`)
}

export async function fetchWorkflowHistory(type: string, id: string | number): Promise<WorkflowHistory> {
  const qs = new URLSearchParams({ type, id: String(id) })
  return readJson<WorkflowHistory>(`/melis/react-api/workflow/history?${qs}`)
}

/** Contexte technique du workflow, dérivé de l'item (ex. une actualité). */
export interface WorkflowContext {
  wfType: string       // ex. "NEWS"
  wfId: string | number
  wfDetails: string    // ex. "<titre> (<id>)"
  wfOpeningJs: string  // JS legacy de réouverture (stocké tel quel)
}

async function readMutation(url: string): Promise<WorkflowMutationResult> {
  try {
    const res = await fetch(url, { headers: XHR, credentials: 'include' })
    const data = await res.json()
    return {
      success: data?.success === 1 || data?.success === true,
      textTitle: data?.textTitle,
      textMessage: data?.textMessage,
      errors: data?.errors,
    }
  } catch (e) {
    return { success: false, textMessage: e instanceof Error ? e.message : 'Error' }
  }
}

/**
 * Envoie une demande de validation.
 *  - roleId réel (> 0) : la demande part à TOUT le rôle → `userId` doit être null.
 *  - roleId === -1 (« 1 person only ») : cible un utilisateur précis → `userId` requis.
 * Endpoint JSON dédié (MelisSmallBusiness), qui gère les deux cas (le chemin legacy exigeait
 * toujours un userId).
 */
export async function sendWorkflowAsk(ctx: WorkflowContext, roleId: number, userId: number | null): Promise<WorkflowMutationResult> {
  const body = new URLSearchParams({
    roleId: String(roleId),
    userId: userId != null ? String(userId) : '',
    wfType: ctx.wfType,
    wfId: String(ctx.wfId),
    wfDetails: ctx.wfDetails,
    wfOpeningJs: ctx.wfOpeningJs,
  })
  try {
    const res = await fetch('/melis/react-api/workflow/ask', {
      method: 'POST',
      headers: { ...XHR, 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      credentials: 'include',
      body: body.toString(),
    })
    const data = await res.json()
    return { success: data?.success === true, textMessage: data?.error }
  } catch (e) {
    return { success: false, textMessage: e instanceof Error ? e.message : 'Error' }
  }
}

/** Valide ou refuse une demande (répond à un évènement dont on est le destinataire). */
export async function sendWorkflowAction(
  ctx: WorkflowContext,
  action: 'validate' | 'refuse',
  ev: { wfeId: number; wfeWfId: number },
): Promise<WorkflowMutationResult> {
  const qs = new URLSearchParams({
    wfaction: action,
    wfType: ctx.wfType,
    wfId: String(ctx.wfId),
    wfDetails: ctx.wfDetails,
    wfOpeningJs: ctx.wfOpeningJs,
    'wfe-id': String(ev.wfeId),
    wfe_wf_id: String(ev.wfeWfId),
  })
  return readMutation(`/melis/MelisSmallBusiness/MelisWorkflow/saveWorkflowActions?${qs}`)
}

/**
 * Ajoute un commentaire de workflow à la timeline de l'item (après une demande / validation /
 * refus). Réutilise l'endpoint legacy éprouvé (addWorkflowComments) : le titre du commentaire est
 * dérivé de l'action côté serveur ; `text` (la note) est optionnel — un texte vide enregistre
 * quand même l'entrée d'action dans la timeline (parité bouton « No comment » legacy).
 */
export async function sendWorkflowComment(
  ctx: WorkflowContext,
  action: 'ask' | 'validate' | 'refuse',
  text: string,
): Promise<WorkflowMutationResult> {
  // Champ d'item selon le type (news / page / blog) — comme la vue legacy du modal commentaire.
  const field = ctx.wfType === 'PAGE' ? 'pcom_page_id' : ctx.wfType === 'BLOG' ? 'pcom_blog_id' : 'pcom_news_id'
  const body = new URLSearchParams({ [field]: String(ctx.wfId), action, pcom_text: text })
  try {
    const res = await fetch('/melis/MelisSmallBusiness/MelisWorkflow/addWorkflowComments', {
      method: 'POST',
      headers: { ...XHR, 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      credentials: 'include',
      body: body.toString(),
    })
    const data = await res.json()
    return {
      success: data?.success === 1 || data?.success === true,
      textTitle: data?.textTitle,
      textMessage: data?.textMessage,
      errors: data?.errors,
    }
  } catch (e) {
    return { success: false, textMessage: e instanceof Error ? e.message : 'Error' }
  }
}

/** Construit le contexte technique workflow pour une actualité (parité vue legacy news). */
export function newsWorkflowContext(newsId: string | number, title: string): WorkflowContext {
  const t = title || `#${newsId}`
  return {
    wfType: 'NEWS',
    wfId: newsId,
    wfDetails: `${t} (${newsId})`,
    wfOpeningJs: `melisHelper.tabOpen('${t}', 'fa fa-rss fa-2x', '${newsId}_id_meliscmsnews_page', 'meliscmsnews_page', { newsId: ${newsId} });`,
  }
}
