/**
 * ConfirmDialog — modale de confirmation générique du back-office React.
 *
 * Remplace `window.confirm()` : la boîte native du navigateur affiche l'URL du BO, ignore le
 * thème (clair/sombre) et n'est pas traduisible — trois raisons pour lesquelles les suppressions
 * du BO React (utilisateurs, commentaires, listes des modules…) passent toutes par CETTE modale.
 * Toute suppression doit donc l'utiliser, jamais confirm().
 *
 * Fichier PARTAGÉ (copie byte-identique dans chaque brique, comme melis-form-errors.tsx) :
 * les briques ne partagent pas de paquet npm, seulement des fichiers dupliqués à l'identique.
 * Sans dépendance Radix (les briques ne l'embarquent pas) : overlay + backdrop maison, fermeture
 * Échap ou clic extérieur, focus automatique sur le bouton de confirmation.
 */
import { useEffect } from 'react'
import { AlertTriangle, Loader2 } from 'lucide-react'

// Mêmes classes que le <Button> shadcn des briques (variant outline / destructive, size sm) :
// ce fichier est partagé tel quel, il ne peut pas importer le Button local d'une brique.
const BTN_BASE = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:pointer-events-none disabled:opacity-50 [&_svg]:size-4 [&_svg]:shrink-0 cursor-pointer h-9 px-3'

export interface ConfirmDialogProps {
  open: boolean
  /** Question posée (courte) — ex. « Supprimer « Mon post » ? ». */
  title: string
  /** Conséquence de l'action (irréversibilité…). Optionnel. */
  description?: string
  confirmLabel: string
  cancelLabel: string
  /** Action en cours → boutons désactivés + spinner sur la confirmation. */
  busy?: boolean
  onConfirm: () => void
  onCancel: () => void
}

export function ConfirmDialog({
  open, title, description, confirmLabel, cancelLabel, busy, onConfirm, onCancel,
}: ConfirmDialogProps) {
  useEffect(() => {
    if (!open) return
    const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') onCancel() }
    document.addEventListener('keydown', onKey)
    return () => document.removeEventListener('keydown', onKey)
  }, [open, onCancel])

  if (!open) return null

  return (
    <div
      className="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4"
      role="dialog" aria-modal="true"
      onMouseDown={(e) => { if (e.target === e.currentTarget) onCancel() }}
    >
      <div className="w-full max-w-sm rounded-xl border border-border bg-background p-5 shadow-xl">
        <div className="flex items-start gap-3">
          <span className="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-full bg-red-500/10 text-red-600">
            <AlertTriangle className="size-5" />
          </span>
          <div className="min-w-0 flex-1">
            <h2 className="text-sm font-semibold text-foreground">{title}</h2>
            {description && <p className="mt-1 text-xs leading-relaxed text-muted-foreground">{description}</p>}
          </div>
        </div>
        <div className="mt-5 flex justify-end gap-2">
          <button
            type="button"
            onClick={onCancel}
            disabled={busy}
            className={`${BTN_BASE} border border-input bg-card hover:bg-accent hover:text-accent-foreground`}
          >
            {cancelLabel}
          </button>
          <button
            type="button"
            autoFocus
            onClick={onConfirm}
            disabled={busy}
            className={`${BTN_BASE} bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90`}
          >
            {busy && <Loader2 className="size-3.5 animate-spin" />}
            {confirmLabel}
          </button>
        </div>
      </div>
    </div>
  )
}
