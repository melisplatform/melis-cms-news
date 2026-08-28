// Full-React config TABS for the MelisCmsNews plugins. Source lives in melis-cms-news (this module),
// imported into melis-cms's SPA build and registered into the shared tab registry (PluginFormKit).
// Each tab reads/writes the shared values via ctx; one Save posts everything to the plugin's
// savePluginConfigToXml() (byte-compatible XML). Mirrors each plugin's legacy modal_form.
import {
  registerPluginTab, type PluginTabContext,
  TemplateField, PageField, TextField, DateField, RemoteSelectField,
} from '../../../melis-cms/ui-react/src/PluginFormKit'

const COG = 'fa fa-cog', FILTER = 'fa fa-filter', PAGES = 'fa fa-th-list'

/* Shared "Properties" fields for the list-type news plugins (Latest & List). */
function NewsListProperties({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint="Gabarit de rendu de la liste d'actualités." />
    <RemoteSelectField ctx={ctx} name="site_id" label="Site" hint="Le site dont on liste les actualités." />
    <PageField ctx={ctx} name="pageIdNews" label="Page des actualités" hint="La page vers laquelle pointent les liens d'actualités." placeholder="Choisir la page…" />
  </div>)
}

/* Shared "Filters" fields (Latest & List; List has no `limit`). */
function NewsFilters({ ctx, withLimit }: { ctx: PluginTabContext; withLimit: boolean }) {
  return (<div>
    <RemoteSelectField ctx={ctx} name="column" label="Trier par" hint="Le champ de tri." />
    <RemoteSelectField ctx={ctx} name="order" label="Ordre" hint="Sens du tri." />
    {withLimit ? <TextField ctx={ctx} name="limit" label="Limite" type="number" hint="Nombre max d'actualités affichées." /> : null}
    <DateField ctx={ctx} name="date_min" label="Date min" hint="Ne montrer que les actualités à partir de cette date." />
    <DateField ctx={ctx} name="date_max" label="Date max" hint="Ne montrer que les actualités jusqu'à cette date." />
    <TextField ctx={ctx} name="search" label="Recherche" hint="Filtre texte (facultatif)." />
  </div>)
}

/* ── Latest news ── properties + filters ─────────────────────────────────── */
function LatestNewsFilters({ ctx }: { ctx: PluginTabContext }) { return <NewsFilters ctx={ctx} withLimit={true} /> }

/* ── List news ── properties + pagination + filters ──────────────────────── */
function ListNewsPagination({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TextField ctx={ctx} name="nbPerPage" label="Actualités par page" type="number" hint="Nombre d'actualités par page." />
    <TextField ctx={ctx} name="nbPageBeforeAfter" label="Pages avant / après" type="number" hint="Liens de page de part et d'autre de la page courante." />
  </div>)
}
function ListNewsFilters({ ctx }: { ctx: PluginTabContext }) { return <NewsFilters ctx={ctx} withLimit={false} /> }

/* ── Show news ── properties (single news) ───────────────────────────────── */
function ShowNewsProperties({ ctx }: { ctx: PluginTabContext }) {
  return (<div>
    <TemplateField ctx={ctx} hint="Gabarit de rendu de l'actualité." />
    <RemoteSelectField ctx={ctx} name="newsId" label="Actualité" hint="L'actualité à afficher." />
  </div>)
}

/** Register the MelisCmsNews plugins' native config tab(s). Called from melis-cms's PluginForms registry. */
export function registerMelisCmsNewsPlugins(): void {
  registerPluginTab('MelisCmsNewsLatestNewsPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: NewsListProperties })
  registerPluginTab('MelisCmsNewsLatestNewsPlugin', { id: 'filters', title: 'Filtres', icon: FILTER, order: 1, Component: LatestNewsFilters })

  registerPluginTab('MelisCmsNewsListNewsPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: NewsListProperties })
  registerPluginTab('MelisCmsNewsListNewsPlugin', { id: 'pagination', title: 'Pagination', icon: PAGES, order: 1, Component: ListNewsPagination })
  registerPluginTab('MelisCmsNewsListNewsPlugin', { id: 'filters', title: 'Filtres', icon: FILTER, order: 2, Component: ListNewsFilters })

  registerPluginTab('MelisCmsNewsShowNewsPlugin', { id: 'properties', title: 'Propriétés', icon: COG, order: 0, Component: ShowNewsProperties })
}
