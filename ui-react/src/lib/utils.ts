import { clsx, type ClassValue } from 'clsx'
import { twMerge } from 'tailwind-merge'

/** Merges Tailwind classes, resolving conflicts (shadcn/ui convention). */
export const cn = (...inputs: ClassValue[]) => twMerge(clsx(inputs))
