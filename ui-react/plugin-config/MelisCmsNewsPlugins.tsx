// Full-React config TABS for the MelisCmsNews plugins. Source lives in melis-cms-news (this module),
// imported into melis-cms's SPA build and registered into the shared tab registry (PluginFormKit).
// Each tab reads/writes the shared values via ctx; one Save posts everything to the plugin's
// savePluginConfigToXml() (byte-compatible XML). Mirrors each plugin's legacy modal_form.
import {
  registerPluginTab, type PluginTabContext,
  TemplateField, PageField, TextField, DateField, RemoteSelectField,
} from '../../../melis-cms/ui-react/src/PluginFormKit'
import { peLang } from '../../../melis-cms/ui-react/src/page-editor-i18n'

const L = ({
  fr: {
    tabProperties: 'Propriétés',
    tabFilters: 'Filtres',
    tabPagination: 'Pagination',
    hintTemplateList: "Gabarit de rendu de la liste d'actualités.",
    labelSite: 'Site',
    hintSite: 'Le site dont on liste les actualités.',
    labelPageNews: 'Page des actualités',
    hintPageNews: "La page vers laquelle pointent les liens d'actualités.",
    placeholderChoosePage: 'Choisir la page…',
    labelSortBy: 'Trier par',
    hintSortBy: 'Le champ de tri.',
    labelOrder: 'Ordre',
    hintOrder: 'Sens du tri.',
    labelLimit: 'Limite',
    hintLimit: "Nombre max d'actualités affichées.",
    labelDateMin: 'Date min',
    hintDateMin: 'Ne montrer que les actualités à partir de cette date.',
    labelDateMax: 'Date max',
    hintDateMax: "Ne montrer que les actualités jusqu'à cette date.",
    labelSearch: 'Recherche',
    hintSearch: 'Filtre texte (facultatif).',
    labelNewsPerPage: 'Actualités par page',
    hintNewsPerPage: "Nombre d'actualités par page.",
    labelPagesBeforeAfter: 'Pages avant / après',
    hintPagesBeforeAfter: "Liens de page de part et d'autre de la page courante.",
    hintTemplateSingle: "Gabarit de rendu de l'actualité.",
    labelNewsItem: 'Actualité',
    hintNewsItem: "L'actualité à afficher.",
  },
  en: {
    tabProperties: 'Properties',
    tabFilters: 'Filters',
    tabPagination: 'Pagination',
    hintTemplateList: 'Rendering template for the news list.',
    labelSite: 'Site',
    hintSite: 'The site whose news items are listed.',
    labelPageNews: 'News page',
    hintPageNews: 'The page the news links point to.',
    placeholderChoosePage: 'Choose the page…',
    labelSortBy: 'Sort by',
    hintSortBy: 'The sort field.',
    labelOrder: 'Order',
    hintOrder: 'Sort direction.',
    labelLimit: 'Limit',
    hintLimit: 'Maximum number of news items displayed.',
    labelDateMin: 'Min date',
    hintDateMin: 'Only show news items from this date onwards.',
    labelDateMax: 'Max date',
    hintDateMax: 'Only show news items up to this date.',
    labelSearch: 'Search',
    hintSearch: 'Text filter (optional).',
    labelNewsPerPage: 'News per page',
    hintNewsPerPage: 'Number of news items per page.',
    labelPagesBeforeAfter: 'Pages before / after',
    hintPagesBeforeAfter: 'Page links on either side of the current page.',
    hintTemplateSingle: 'Rendering template for the news item.',
    labelNewsItem: 'News item',
    hintNewsItem: 'The news item to display.',
  },
} as const)[peLang()]

const COG = 'fa fa-cog', FILTER = 'fa fa-filter', PAGES = 'fa fa-th-list'

/* Shared "Properties" fields for the list-type news plugins (Latest & List). */
function NewsListProperties({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint={L.hintTemplateList} />
    <RemoteSelectField ctx={ctx} name="site_id" label={L.labelSite} hint={L.hintSite} />
    <PageField ctx={ctx} name="pageIdNews" label={L.labelPageNews} hint={L.hintPageNews} placeholder={L.placeholderChoosePage} />
  </div>)
}

/* Shared "Filters" fields (Latest & List; List has no `limit`). */
function NewsFilters({ ctx, withLimit }: { ctx: PluginTabContext; withLimit: boolean }) {
  return (<div>
    <RemoteSelectField ctx={ctx} name="column" label={L.labelSortBy} hint={L.hintSortBy} />
    <RemoteSelectField ctx={ctx} name="order" label={L.labelOrder} hint={L.hintOrder} />
    {withLimit ? <TextField ctx={ctx} name="limit" label={L.labelLimit} type="number" hint={L.hintLimit} /> : null}
    <DateField ctx={ctx} name="date_min" label={L.labelDateMin} hint={L.hintDateMin} />
    <DateField ctx={ctx} name="date_max" label={L.labelDateMax} hint={L.hintDateMax} />
    <TextField ctx={ctx} name="search" label={L.labelSearch} hint={L.hintSearch} />
  </div>)
}

/* ── Latest news ── properties + filters ─────────────────────────────────── */
function LatestNewsFilters({ ctx }: { ctx: PluginTabContext }) { return <NewsFilters ctx={ctx} withLimit={true} /> }

/* ── List news ── properties + pagination + filters ──────────────────────── */
function ListNewsPagination({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TextField ctx={ctx} name="nbPerPage" label={L.labelNewsPerPage} type="number" hint={L.hintNewsPerPage} />
    <TextField ctx={ctx} name="nbPageBeforeAfter" label={L.labelPagesBeforeAfter} type="number" hint={L.hintPagesBeforeAfter} />
  </div>)
}
function ListNewsFilters({ ctx }: { ctx: PluginTabContext }) { return <NewsFilters ctx={ctx} withLimit={false} /> }

/* ── Show news ── properties (single news) ───────────────────────────────── */
function ShowNewsProperties({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint={L.hintTemplateSingle} />
    <RemoteSelectField ctx={ctx} name="newsId" label={L.labelNewsItem} hint={L.hintNewsItem} />
  </div>)
}

/** Register the MelisCmsNews plugins' native config tab(s). Called from melis-cms's PluginForms registry. */
export function registerMelisCmsNewsPlugins(): void {
  registerPluginTab('MelisCmsNewsLatestNewsPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: NewsListProperties })
  registerPluginTab('MelisCmsNewsLatestNewsPlugin', { id: 'filters', title: L.tabFilters, icon: FILTER, order: 1, Component: LatestNewsFilters })

  registerPluginTab('MelisCmsNewsListNewsPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: NewsListProperties })
  registerPluginTab('MelisCmsNewsListNewsPlugin', { id: 'pagination', title: L.tabPagination, icon: PAGES, order: 1, Component: ListNewsPagination })
  registerPluginTab('MelisCmsNewsListNewsPlugin', { id: 'filters', title: L.tabFilters, icon: FILTER, order: 2, Component: ListNewsFilters })

  registerPluginTab('MelisCmsNewsShowNewsPlugin', { id: 'properties', title: L.tabProperties, icon: COG, order: 0, Component: ShowNewsProperties })
}
