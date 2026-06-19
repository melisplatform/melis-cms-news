import { useParams } from 'react-router-dom'
import NewsListPage from './NewsListPage'
import NewsFormPage from './NewsFormPage'

/**
 * Single brick Component. The host mounts it at both `{route}` and `{route}/:id`:
 *   - no id            → the News list
 *   - id === 'new' / N → the News form (create / edit)
 * Each page reads its own params/navigation, so we only branch on presence of `id`.
 */
export default function NewsBrick() {
  const { id } = useParams<{ id?: string }>()
  if (!id) return <NewsListPage />
  return <NewsFormPage />
}
