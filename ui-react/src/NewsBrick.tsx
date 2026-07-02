import NewsPage from './NewsPage'

/**
 * Single brick Component. Monté une fois par le shell sur l'onglet « Actualités ».
 * Toute la navigation liste ⇄ édition se fait EN INTERNE via des sous-onglets
 * (NewsPage), sans toucher à l'URL ni créer d'onglet général du shell — exactement
 * comme l'outil Slider.
 */
export default function NewsBrick() {
  return <NewsPage />
}
