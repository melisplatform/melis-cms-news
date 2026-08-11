/**
 * i18n minimal de la brique News — FR/EN piloté par la langue du BO (<html lang>).
 * Un seul dictionnaire partagé par tous les composants de l'outil, pour éviter les chaînes
 * en dur (jusqu'ici anglais dans le formulaire, français dans la liste). Usage : `t('save')`.
 * Interpolation simple : `t('paragraph_n', { n: 1 })`.
 */

export type Lang = 'fr' | 'en'

export function newsLang(): Lang {
  const l = (typeof document !== 'undefined' ? document.documentElement.lang : 'en') || 'en'
  return l.slice(0, 2).toLowerCase() === 'fr' ? 'fr' : 'en'
}

const DICT: Record<string, { en: string; fr: string }> = {
  // ── Form: header / actions ──
  preview:            { en: 'Preview',            fr: 'Aperçu' },
  save:               { en: 'Save',               fr: 'Enregistrer' },
  saving:             { en: 'Saving…',            fr: 'Enregistrement…' },
  published:          { en: 'Published',          fr: 'Publié' },
  unpublished:        { en: 'Unpublished',        fr: 'Non publié' },
  workflow:           { en: 'Workflow',           fr: 'Workflow' },

  // ── Form: fields ──
  article_title_ph:   { en: 'Article title…',     fr: "Titre de l'article…" },
  subtitle_ph:        { en: 'Add a subtitle…',    fr: 'Ajouter un sous-titre…' },
  body:               { en: 'Body',               fr: 'Contenu' },
  add_paragraph:      { en: 'Add paragraph',      fr: 'Ajouter un paragraphe' },
  paragraph_n:        { en: 'Paragraph {n}',      fr: 'Paragraphe {n}' },
  remove_paragraph:   { en: 'Remove paragraph',   fr: 'Supprimer le paragraphe' },
  drag_reorder:       { en: 'Drag to reorder',    fr: 'Glisser pour réordonner' },
  editor_ph:          { en: 'Write your content here…', fr: 'Écrivez votre contenu ici…' },

  // ── Form: media ──
  media:              { en: 'Media',              fr: 'Médias' },
  images:             { en: 'Images',             fr: 'Images' },
  image_n:            { en: 'Image {n}',          fr: 'Image {n}' },
  file_attachments:   { en: 'File attachments',   fr: 'Fichiers joints' },
  media_max:          { en: 'max. 3',             fr: 'max. 3' },
  attach_file:        { en: 'Click to attach a file', fr: 'Cliquer pour joindre un fichier' },
  remove:             { en: 'Remove',             fr: 'Supprimer' },
  replace:            { en: 'Replace',            fr: 'Remplacer' },
  media_save_first:   { en: 'Save the article first to add images and files.', fr: "Enregistrez l'article pour pouvoir ajouter des images et des fichiers." },
  save_first_short:   { en: 'Save the article first', fr: "Enregistrez d'abord l'article" },

  // ── Form: sidebar sections ──
  status:             { en: 'Status',             fr: 'Statut' },
  publication:        { en: 'Publication',        fr: 'Publication' },
  publish_on:         { en: 'Publish on',         fr: 'Publier le' },
  unpublish_on:       { en: 'Unpublish on',       fr: 'Dépublier le' },
  site:               { en: 'Site',               fr: 'Site' },
  choose_site:        { en: 'Choose a site…',     fr: 'Choisir un site…' },
  author:             { en: 'Author',             fr: 'Auteur' },
  choose:             { en: 'Choose…',            fr: 'Choisir…' },

  // ── Comments (module MelisCmsComments) ──
  comments:            { en: 'Comments',            fr: 'Commentaires' },
  comments_validation: { en: 'Comments validation', fr: 'Validation des commentaires' },
  comments_validation_hint: { en: 'When on, front-office comments stay hidden until approved.', fr: "Si activé, les commentaires du site restent masqués jusqu'à validation." },
  comment_add:         { en: 'Add a comment',       fr: 'Ajouter un commentaire' },
  comment_name_ph:     { en: 'Name',                fr: 'Nom' },
  comment_text_ph:     { en: 'Write a comment…',    fr: 'Écrire un commentaire…' },
  comment_save_first:  { en: 'Save the article first to manage comments.', fr: "Enregistrez l'article pour gérer les commentaires." },
  no_comments:         { en: 'No comments yet',      fr: 'Aucun commentaire' },
  comment_approve:     { en: 'Approve',             fr: 'Valider' },
  comment_refuse:      { en: 'Refuse',              fr: 'Refuser' },
  comment_pending:     { en: 'Pending',             fr: 'En attente' },
  comment_approved:    { en: 'Approved',            fr: 'Validé' },
  comment_refused:     { en: 'Refused',             fr: 'Refusé' },
  comment_delete_confirm: { en: 'Delete this comment?', fr: 'Supprimer ce commentaire ?' },
  comment_delete_desc: { en: 'This comment will be permanently deleted. This action cannot be undone.', fr: 'Ce commentaire sera définitivement supprimé. Cette action est irréversible.' },
  comment_delete_by:   { en: 'From {name}', fr: 'De {name}' },
  pager_page_of:       { en: 'Page {page} of {total}', fr: 'Page {page} sur {total}' },
  pager_prev:          { en: 'Previous', fr: 'Précédent' },
  pager_next:          { en: 'Next', fr: 'Suivant' },
  seo:                { en: 'SEO',                fr: 'SEO' },
  meta_title:         { en: 'Meta title',         fr: 'Titre meta' },
  meta_description:   { en: 'Meta description',   fr: 'Description meta' },
  url:                { en: 'URL',                fr: 'URL' },
  url_redirect:       { en: 'URL redirect',       fr: 'Redirection URL' },
  url_301:            { en: 'URL 301',            fr: 'URL 301' },
  canonical_url:      { en: 'Canonical URL',      fr: 'URL canonique' },
  categories:         { en: 'Categories',         fr: 'Catégories' },
  no_category:        { en: 'No category available', fr: 'Aucune catégorie disponible' },
  tags:               { en: 'Tags',               fr: 'Tags' },
  no_tag:             { en: 'No tag available',   fr: 'Aucun tag disponible' },
  slider:             { en: 'Slider',             fr: 'Slider' },
  no_slider:          { en: 'No slider',          fr: 'Aucun slider' },
  preview_select_page: { en: 'Select page for preview', fr: 'Sélectionner une page pour l\'aperçu' },
  preview_new_tab:    { en: 'Display in new tab',  fr: 'Afficher dans un nouvel onglet' },
  preview_show:       { en: 'Display below',      fr: 'Afficher ci-dessous' },
  preview_hide:       { en: 'Hide preview',       fr: 'Masquer l\'aperçu' },
  no_preview_page:    { en: 'No pages available for preview', fr: 'Aucune page disponible pour l\'aperçu' },
  calendar:           { en: 'Calendar',           fr: 'Calendrier' },
  cal_today:          { en: 'Today',              fr: "Aujourd'hui" },
  cal_clear:          { en: 'Clear',              fr: 'Effacer' },

  // ── Form: notifications / errors ──
  saved_ok:           { en: 'Article saved.',     fr: 'Article enregistré.' },
  err_load:           { en: 'Error loading article', fr: "Erreur lors du chargement de l'article" },
  err_save:           { en: 'Error saving article',  fr: "Erreur lors de l'enregistrement" },
  err_upload:         { en: 'Upload error',       fr: "Erreur d'envoi du fichier" },
  err_delete:         { en: 'Delete error',       fr: 'Erreur de suppression' },
  title_required:     { en: 'Title is required',  fr: 'Le titre est obligatoire' },
  site_required:      { en: 'Please select a site', fr: 'Veuillez sélectionner un site' },
  check_required:     { en: 'Please check the required fields.', fr: 'Veuillez vérifier les champs obligatoires.' },

  // ── List: header / KPIs ──
  news_title:         { en: 'News',               fr: 'Actualités' },
  news_subtitle:      { en: 'Manage your news articles', fr: 'Gestion des articles' },
  total_articles:     { en: 'Total articles',     fr: 'Total articles' },
  count_published:    { en: 'Published',          fr: 'Publiés' },
  count_drafts:       { en: 'Unpublished',        fr: 'Dépubliés' },
  new_article:        { en: 'New article',        fr: 'Nouvel article' },
  view_new:           { en: 'New',                fr: 'New' },
  view_old:           { en: 'Old',                fr: 'Old' },
  melis_view:         { en: 'News — Melis view',  fr: 'News — Vue Melis' },
  no_list_rights:     { en: "You don't have permission to view the article list.", fr: "Vous n'avez pas les droits pour consulter la liste des articles." },

  // ── List: filters / table ──
  search_ph:          { en: 'Search…',            fr: 'Rechercher…' },
  filter_all:         { en: 'All',                fr: 'Tous' },
  filter_active:      { en: 'Active',             fr: 'Actif' },
  filter_inactive:    { en: 'Inactive',           fr: 'Inactif' },
  col_id:             { en: 'ID',                 fr: 'ID' },
  col_title:          { en: 'Title',              fr: 'Titre' },
  col_subtitle:       { en: 'Subtitle',           fr: 'Sous-titre' },
  col_site:           { en: 'Site',               fr: 'Site' },
  col_publication:    { en: 'Publication',        fr: 'Publication' },
  col_expiration:     { en: 'Expiration',         fr: 'Expiration' },
  col_created:        { en: 'Created',            fr: 'Créé le' },
  col_status:         { en: 'Status',             fr: 'Statut' },
  col_actions:        { en: 'Actions',            fr: 'Actions' },
  status_published:   { en: 'Published',          fr: 'Publié' },
  status_draft:       { en: 'Unpublished',        fr: 'Dépublié' },
  untitled:           { en: 'Untitled',           fr: 'Sans titre' },
  no_articles:        { en: 'No articles found',  fr: 'Aucun article trouvé' },
  edit:               { en: 'Edit',               fr: 'Modifier' },
  delete:             { en: 'Delete',             fr: 'Supprimer' },
  loading:            { en: 'Loading…',           fr: 'Chargement…' },
  end_of_list:        { en: 'end of list',        fr: 'fin de la liste' },
  reset_filters:      { en: 'Reset filters',      fr: 'Réinitialiser les filtres' },
  refresh:            { en: 'Refresh',            fr: 'Rafraîchir' },

  // ── List: columns manager + export modal ──
  columns:            { en: 'Columns',            fr: 'Colonnes' },
  cols_hidden:        { en: 'Hidden',             fr: 'Masquées' },
  cols_visible:       { en: 'Visible',            fr: 'Visibles' },
  drag_here:          { en: 'Drag here',          fr: 'Glisser ici' },
  reset:              { en: 'Reset',              fr: 'Réinitialiser' },
  pin:                { en: 'Pin',                fr: 'Épingler' },
  unpin:              { en: 'Unpin',              fr: 'Désépingler' },
  export:             { en: 'Export',             fr: 'Exporter' },
  export_row:         { en: 'article',            fr: 'article' },
  export_rows:        { en: 'articles',           fr: 'articles' },
  format:             { en: 'Format',             fr: 'Format' },
  cols_to_export:     { en: 'Columns to export',  fr: 'Colonnes à exporter' },
  drag_include_order: { en: '— drag to include and order', fr: '— glisser pour inclure et ordonner' },
  excluded:           { en: 'Excluded',           fr: 'Non incluses' },
  included:           { en: 'Included',           fr: 'À exporter' },
  cancel:             { en: 'Cancel',             fr: 'Annuler' },
  exporting:          { en: 'Exporting…',         fr: 'Export…' },
  download:           { en: 'Download {fmt}',     fr: 'Télécharger {fmt}' },
  export_error:       { en: 'Error during export', fr: "Erreur lors de l'export" },
  export_sheet:       { en: 'News',               fr: 'Actualités' },
  export_filename:    { en: 'news',               fr: 'actualites' },

  confirm_delete:     { en: 'Delete “{title}”?',  fr: 'Supprimer « {title} » ?' },
  error:              { en: 'Error',              fr: 'Erreur' },
  export_subtitle:    { en: '{n} {rows} with the active filters', fr: '{n} {rows} avec les filtres actifs' },

  // ── Sub-tabs (NewsPage) ──
  back:               { en: 'Back',               fr: 'Retour' },

  // ── Rich editor toolbar ──
  h2:                 { en: 'Heading 2',          fr: 'Titre 2' },
  h3:                 { en: 'Heading 3',          fr: 'Titre 3' },
  bold:               { en: 'Bold',               fr: 'Gras' },
  italic:             { en: 'Italic',             fr: 'Italique' },
  underline:          { en: 'Underline',          fr: 'Souligné' },
  strikethrough:      { en: 'Strikethrough',      fr: 'Barré' },
  align_left:         { en: 'Align left',         fr: 'Aligner à gauche' },
  align_center:       { en: 'Center',             fr: 'Centrer' },
  align_right:        { en: 'Align right',        fr: 'Aligner à droite' },
  justify:            { en: 'Justify',            fr: 'Justifier' },
  bullet_list:        { en: 'Bullet list',        fr: 'Liste à puces' },
  numbered_list:      { en: 'Numbered list',      fr: 'Liste numérotée' },
  link:               { en: 'Link',               fr: 'Lien' },
  clear_formatting:   { en: 'Clear formatting',   fr: 'Effacer la mise en forme' },
}

export function t(key: keyof typeof DICT | string, vars?: Record<string, string | number>): string {
  const entry = DICT[key as string]
  let s = entry ? entry[newsLang()] : (key as string)
  if (vars) for (const [k, v] of Object.entries(vars)) s = s.replaceAll(`{${k}}`, String(v))
  return s
}
