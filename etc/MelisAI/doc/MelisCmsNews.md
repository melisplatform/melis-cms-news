---
title: MelisCmsNews module
package: melisplatform/melis-cms-news
doc_type: module-documentation
audience: ai
language: en
module_version: v5.3.8
last_reviewed: 2026-06-08
maintainer: Melis Technology
keywords: [news, cms, melis, back-office, plugin, seo, templating, micro-service, category, tag]
screenshots_dir: ./images
---

# MelisCmsNews Module — Functional Documentation (for AI)

> **Purpose of this document**: describe, functionally and technically, the
> `melisplatform/melis-cms-news` module, so that an AI (or a developer) can understand
> *what the module does*, *which tools it provides*, *how they work* and
> *where the corresponding code lives*.
>
> **Audience**: consumed by the **MelisAI** module (a MelisPlatform module that exposes an
> MCP function to answer user questions). MelisAI fetches this `.md` file and the
> screenshots in `./images/` **on demand** — so the doc is self-contained and §9 acts as
> the filename→content index for retrieving a specific screenshot.
>
> **Status**: reviewed 2026-06-08 against the current source. The module carries no
> semantic version (no `version` in `composer.json`), so treat this doc as describing the
> current `melisplatform/melis-cms-news` source rather than a tagged release.
>
> Screenshots live in `./images/` (relative paths `./images/...`).

---

## 1. Overview

`MelisCmsNews` is the **news / posts management** module of the Melis platform.
It lets editors create, edit, organize and publish news articles from the back-office,
then display them on a Melis site's front-office through templating plugins.

| Item | Value |
|---|---|
| Package name | `melisplatform/melis-cms-news` |
| Type | `melisplatform-module` |
| PHP namespace | `MelisCmsNews\` → `src/` (PSR-4) |
| Melis category | `cms` |
| License | OSL-3.0 |
| PHP required | `^8.1 | ^8.3` |
| Framework | Laminas (ex-Zend Framework 2/3), Melis MVC architecture |

### Dependencies (required Melis modules)

The module does not run standalone. It relies on:

- `melisplatform/melis-core` (`^5.2`) — foundation, general services, events, rights, translations
- `melisplatform/melis-engine` (`^5.2`) — page engine, templates, front rendering
- `melisplatform/melis-front` (`^5.2`) — front-office management
- `melisplatform/melis-cms` (`^5.2`) — CMS, pages, sites management
- `laminas/laminas-paginator` (`^2.18`) — list pagination

**Optional** modules extended when present: `MelisCmsSlider` (slider selector on a news),
`MelisCmsComments` (comments + dashboard plugin), `MelisCmsTag` (tags).

---

## 2. Functional concepts

A **news** (article) in Melis is made of:

- **Properties**: status (published / unpublished), publish / unpublish dates, creation
  date, owning site.
- **Multilingual texts**: title, subtitle and up to 10 paragraphs, **per language**
  (a news has one text entry per language).
- **Media**: up to 3 images and 3 documents attached, stored under `/media/news/`.
- **Categorization**: categories (via the Melis category tree) and tags.
- **SEO**: meta title, meta description, URL, redirect URL, 301 URL, canonical, per
  language. Generates dedicated front routes.
- **Site binding** + detail page ("Page – News details") for rendering individual
  articles on the front-office.

### Data model (MySQL tables)

| Table | Role | Primary key |
|---|---|---|
| `melis_cms_news` | News properties (status, dates, images 1-3, documents 1-3, slider, site) | `cnews_id` |
| `melis_cms_news_texts` | Per-language texts (title, subtitle, paragraphs 1-10, paragraph order) | `cnews_text_id` |
| `melis_cms_news_seo` | Per-language SEO data (URL, redirect, 301, meta title/desc, canonical) | `cnews_seo_id` |
| `melis_cms_news_category` | News ↔ category link (`cnc_cnews_id`, `cnc_cat2_id`, order) | `cnc_id` |

- MySQL Workbench model: `install/sql/Model/MelisCmsNews.mwb`
- Base structure: `install/sql/setup_structure.sql`
- Incremental migrations: `install/dbdeploy/*.sql` (SEO, texts, categories…)

> Note: the **tags** table is managed by the `MelisCmsTag` module; the association is
> declared in `config/module.config.php` under the `melis_cms_tag` key (entity_type `NEWS`).

---

## 3. Tools and elements provided

The module exposes **4 main functional elements**:

1. **The News tool (back-office)** — list + editing of articles
2. **3 front-office templating plugins** — Latest News, List News, Show News
3. **An application service** reusable by other modules + micro-services
4. **Extensions**: SEO, Comments (dashboard), GDPR, Tags, Slider

---

### 3.1 News tool (back-office)

Accessible from the Melis back-office left menu, **CMS → News** section
(icon `fa-newspaper-o`). Declared in `config/app.interface.php`
(tree `meliscore_leftmenu → meliscms_toolstree_section → meliscms_news_tool_section`).

The tool is split into two screens:

#### a) The news list (`MelisCmsNewsList`)

- **Controller**: `src/Controller/MelisCmsNewsListController.php`
- **Table configuration**: `config/app.tools.php` (key `meliscmsnews_list_table`)
- **Views**: `view/melis-cms-news/melis-cms-news-list/*.phtml`

Displays a Melis DataTable of articles with columns: ID, status, title, creation date,
publish date, unpublish date, site.

Available filters:
- **Limit** of displayed rows (`render-news-list-content-filter-limit`)
- **Site** (`render-news-list-content-filter-site`)
- **Text search** (`render-news-list-content-filter-search`)
- **Refresh** (`render-news-list-content-filter-refresh`)

Per-row action buttons:
- **Info** (details) — `renderNewsListContentActionInfoAction`
- **Delete** — `renderNewsListContentActionDeleteAction` / `deleteNewsAction`

Table data is loaded via AJAX from
`/melis/MelisCmsNews/MelisCmsNewsList/renderNewsListData` (`renderNewsListDataAction`).
An **Add** button (`render-news-list-header-right-add`) opens news creation.

![News list with filters and actions](./images/meliscmsnews-tool-news-list.png)
*Caption: the News list — header with the Add button, left filters (limit, site), centered
search box, the results table (ID, status, title, creation/publish/unpublish dates, site)
and per-row Info / Delete actions.*

#### b) Creating a news vs. editing it

The **Add** button opens the **creation** screen, which is the same screen as editing but
**reduced**: only the **Properties**, **Texts** and **SEO** tabs are available at this
stage. The **Medias** and **Preview** tabs are **hidden until the news has been created
(saved at least once)**, because they need a persisted `cnews_id` — media files are stored
against an existing record (under `/media/news/`) and the preview renders the saved news
on its detail page. Once the news is saved, the screen switches to the full edit mode with
all tabs enabled.

![Creating a new news — reduced tab set (no Medias / Preview yet)](./images/meliscmsnews-tool-news-new.png)
*Caption: the creation screen — only the Properties, Texts and SEO tabs are present; the
Medias and Preview tabs are absent until the news is saved for the first time.*

#### c) News editing (`MelisCmsNews`)

- **Controller**: `src/Controller/MelisCmsNewsController.php` (~1800 lines, the core of the tool)
- **Views**: `view/melis-cms-news/melis-cms-news/*.phtml`
- **Interface tree**: `config/app.interface.php` (key `meliscmsnews`)

The editing screen is organized into **tabs** (declared in the `interface` tree):

| Tab | Icon | Content | Main action |
|---|---|---|---|
| **Properties** | `tag` | Publish/unpublish dates, categories, tags, site, slider | `render-news-tabs-properties-details-left-properties` |
| **Medias** | `picture` | Images (max 3) and documents (max 3) | `render-news-tabs-properties-details-left-images` / `...-documents` |
| **Texts** | `pencil` | Title, subtitle, paragraphs 1-10 (WYSIWYG editor), paragraph order | `render-news-tabs-properties-details-right-paragraphs` |
| **SEO** | `search` | Meta title, description, URL, redirect, 301, canonical | `render-news-tabs-seo-details` |
| **Preview** | `imac` | News preview via iframe on the detail page | `preview-tab-iframe` |

Key controller actions:
- **Save**: `saveNewsLetterAction` (saves properties + texts), `saveFileFormAction` (image/document upload)
- **Status**: `renderNewsTabsContentHeaderStatusAction` (toggle published/unpublished, bootstrap switch)
- **Categories**: `renderNewsTabsPropertiesDetailsLeftCategoriesContentAction`, `getCategoryTreeViewAction`, `renderNewsCategoryModalAction`
- **Tags**: `renderNewsTabsPropertiesDetailsLeftTagsContentAction`
- **File removal**: `removeAttachFileAction`
- **Preview**: `previewTabIframeAction`

> Limits (3 images, 3 documents, 10 paragraphs but 4 shown by default) and the upload
> path `/media/news/` are configured in `config/app.interface.php`
> (keys `images_conf`, `documents_conf`, `paragraphs_conf`, `files.imagesPath`).

![News editing — Properties tab](./images/meliscmsnews-tool-news-edit-properties.png)
*Caption: Properties tab — publish/unpublish date pickers, category and tag selectors,
site and slider selection.*

![News editing — Texts tab](./images/meliscmsnews-tool-news-edit-texts.png)
*Caption: Texts tab — title, subtitle and the WYSIWYG paragraph editors (paragraphs 1-10,
reorderable via `cnews_paragraph_order`).*

![News editing — Medias tab](./images/meliscmsnews-tool-news-edit-medias.png)
*Caption: Medias tab — up to 3 images and 3 documents, each with upload and thumbnail
preview, stored under `/media/news/`.*

![News editing — SEO tab](./images/meliscmsnews-tool-news-edit-seo.png)
*Caption: SEO tab — meta title, meta description, URL, redirect URL, 301 URL and canonical
fields (per language).*

![News editing — Preview tab](./images/meliscmsnews-tool-news-edit-preview.png)
*Caption: Preview tab — the saved news rendered inside an iframe on its `NEWS_DETAIL`
detail page.*

---

### 3.2 Front-office plugins (templating)

The module ships **three** plugins that can be dropped into Melis page templates
(`MelisCms` section of the plugin selector). Each plugin provides a *Controller Plugin*,
a *View Helper*, a config file, a `.phtml` rendering template and a configuration form
shown in a modal during page editing.

**How a plugin works** (common to all three): a `front()` method prepares the data, a
`createOptionsForms()` / `getFormData()` pair builds the configuration form, and a
`loadDbXmlToPluginConfig()` / `savePluginConfigToXml()` pair persists the plugin config
as XML inside the page. The configuration modal can have **one or several tabs**
depending on the plugin (declared as `modal_form` entries in the plugin's config file).

Plugin selector thumbnails: `public/plugins/images/*_thumb.jpg`.

![News plugin selector in the page editor](./images/meliscmsnews-page-menu-plugins-selector.png)
*Caption: the Melis page editor's plugin selector (MelisCms section) showing the three News
plugin thumbnails that can be dragged into a page template.*

The three plugins are described individually below.

#### 3.2.1 Latest News plugin

- **Role**: displays the **N latest news** of a site (a short teaser/highlight block),
  with optional filtering. Typically used on a homepage or sidebar.
- **Controller Plugin**: `src/Controller/Plugin/MelisCmsNewsLatestNewsPlugin.php`
- **View Helper**: `MelisCmsNewsLatestHelper` (alias `MelisCmsNewsLatestPlugin`)
- **Config**: `config/plugins/MelisCmsNewsLatestNewsPlugin.config.php`
- **Rendering template**: `view/melis-cms-news/plugins/latestnews.phtml`
- **Config modal — 2 tabs**:
  - **Properties** (`modal-template-form.phtml`): `template_path`, `site_id`, `pageIdNews`
  - **Filters** (`modal-filter-form.phtml`): `column`, `order`, `limit`, `date_min`,
    `date_max`, `search`

![Latest News plugin config — Properties tab](./images/meliscmsnews-page-plugin-latestnews-config-tab-properties.png)
*Caption: Latest News › Properties tab — rendering template, source site and detail-page id.*

![Latest News plugin config — Filters tab](./images/meliscmsnews-page-plugin-latestnews-config-tab-filters.png)
*Caption: Latest News › Filters tab — sort column and direction, limit, date range and search.*

#### 3.2.2 List News plugin

- **Role**: displays a **paginated list** of news for a site, with filtering **and
  pagination**. Used for a full "News" landing/index page.
- **Controller Plugin**: `src/Controller/Plugin/MelisCmsNewsListNewsPlugin.php`
- **View Helper**: `MelisCmsNewsListHelper` (alias `MelisCmsNewsListPlugin`)
- **Config**: `config/plugins/MelisCmsNewsListNewsPlugin.config.php`
- **Rendering templates**: `view/melis-cms-news/plugins/listnews.phtml` +
  `list-paginator.phtml` (pagination control)
- **Config modal — 3 tabs**:
  - **Properties** (`modal-template-form.phtml`): `template_path`, `site_id`, `pageIdNews`
  - **Pagination** (`modal-pagination-form.phtml`): `nbPerPage`, `nbPageBeforeAfter`
  - **Filters** (`modal-filter-form.phtml`): `column`, `order`, `limit`, `date_min`,
    `date_max`, `search`

![List News plugin config — Properties tab](./images/meliscmsnews-page-plugin-newslist-config-tab-properties.png)
*Caption: List News › Properties tab — rendering template, source site and detail-page id.*

![List News plugin config — Pagination tab](./images/meliscmsnews-page-plugin-newslist-tab-pagination.png)
*Caption: List News › Pagination tab — items per page (`nbPerPage`) and pages shown
before/after the current one (`nbPageBeforeAfter`).*

![List News plugin config — Filters tab](./images/meliscmsnews-page-plugin-newslist-config-tab-filters.png)
*Caption: List News › Filters tab — sort column and direction, limit, date range and search.*

#### 3.2.3 Show News plugin

- **Role**: displays the **detail of a single news** article (full content). Placed on
  the `NEWS_DETAIL`-type detail page that the other two plugins link to.
- **Controller Plugin**: `src/Controller/Plugin/MelisCmsNewsShowNewsPlugin.php`
- **View Helper**: `MelisCmsNewsShowNewsHelper` (alias `MelisCmsNewsShowNewsPlugin`)
- **Config**: `config/plugins/MelisCmsNewsShowNewsPlugin.config.php`
- **Rendering template**: `view/melis-cms-news/plugins/shownews.phtml`
- **Config modal — 1 tab**:
  - **Properties** (`modal-template-form.phtml`): `template_path` (the single-news
    detail template)

![Show News plugin config — Properties tab](./images/meliscmsnews-page-plugin-newsdetail-config-tab-properties.png)
*Caption: Show News › Properties tab — the single rendering template for the article detail.*

#### Configuration field reference

- `template_path` — plugin rendering template
- `site_id` — source site of the news
- `pageIdNews` — detail (landing) page the links point to
- `column` / `order` — sort column and direction (ASC/DESC)
- `limit`, `date_min`, `date_max`, `search` — list filtering
- `nbPerPage`, `nbPageBeforeAfter` — pagination sizing (**List News only**)

---

### 3.3 Application service `MelisCmsNewsService`

- **File**: `src/Service/MelisCmsNewsService.php`
- **Service manager alias**: `MelisCmsNewsService`
- Extends `MelisCore\Service\MelisGeneralService` → each method emits `*_start` / `*_end`
  **events** (e.g. `melis_cms_news_get_news_list_start`) allowing other modules to
  intercept and alter the data.

Retrieval and usage from another module:

```php
// Obtain the service
$newsService = $this->getServiceManager()->get('MelisCmsNewsService');

// Paginated list: first 10 published news of site 1, in French, newest first
$news = $newsService->getNewsList(
    status:      1,
    langId:      1,
    start:       0,
    limit:       10,
    orderColumn: 'cnews_publish_date',
    order:       'DESC',
    siteId:      1
);

// Single news in every language (returns an array of language rows)
$allLangs = $newsService->getNewsById(42);

// Single news in one language (returns one row)
$frRow = $newsService->getNewsById(42, 1);
```

> The three front plugins are normally inserted through the **page editor's plugin
> selector** (see §3.2), not called directly; their view-helper aliases
> (`MelisCmsNewsLatestPlugin`, `MelisCmsNewsListPlugin`, `MelisCmsNewsShowNewsPlugin`) are
> wired in `config/module.config.php`.

Main public methods:

| Method | Role |
|---|---|
| `getNewsList($status, $langId, $dateMin, $dateMax, $publishDateMin, $publishDateMax, $unpublishFilter, $start, $limit, $orderColumn, $order, $siteId, $search, $count)` | Filtered/paginated news list |
| `getNewsById($newsId, $langId = null)` | Single news detail (all languages if `$langId` empty) |
| `getNewsByIdArray(array $newsIdArray, $langId, array $where = [])` | Retrieve several news by array of IDs |
| `saveNews($news, $newsId = null)` | Create (or update if `$newsId` provided) a news |
| `deleteNewsById($newsId)` | Delete a news + its texts + its SEO data |
| `getPostText(?int $newsId)` | Retrieve a news' texts |
| `getNewsDetailsPagesBySite(?int $siteId)` | List the `NEWS_DETAIL`-type pages of a site (for the detail page selector) |

#### Service events

Every method fires a `*_start` event (before execution, lets a listener alter the input
parameters) and a `*_end` event (after execution, lets a listener alter `results`). Other
modules can `attach()` to these to extend behaviour without modifying the module.

| Method | Start event | End event |
|---|---|---|
| `getNewsList` | `melis_cms_news_get_news_list_start` | `melis_cms_news_get_news_list_end` |
| `getNewsById` | `melis_cms_news_get_news_by_id_start` | `melis_cms_news_get_news_by_id_end` |
| `getNewsByIdArray` | `melis_cms_news_get_news_by_id_array_start` | `melis_cms_news_get_news_by_id_array_end` |
| `saveNews` | `melis_cms_news_save_news_start` | `melis_cms_news_save_news_end` |
| `deleteNewsById` | `melis_cms_news__delete_news_by_id_start` | `melis_cms_news__delete_news_by_id_end` |
| `getPostText` | `melis_cms_news_get_post_text_start` | `melis_cms_news_get_post_text_end` |
| `getNewsDetailsPagesBySite` | `melis_cms_news_get_news_details_pages_start` | `melis_cms_news_get_news_details_pages_end` |

> Note the **double underscore** in the `deleteNewsById` event names
> (`melis_cms_news__delete_news_by_id_*`) — it is intentional in the source.

Complementary **SEO** service: `src/Service/MelisCmsNewsSeoService.php`
(alias `MelisCmsNewsSeoService`).

#### Tables (Table Gateways)

Declared as aliases in `config/module.config.php`:
`MelisCmsNewsTable`, `MelisCmsNewsTextsTable`, `MelisCmsNewsSeoTable`,
`MelisCmsNewsCategoryTable`, `MelisCmsNewsTagsTable` (in `src/Model/Tables/`).

---

### 3.4 Micro-services (API)

- **File**: `config/app.microservice.php`
- Exposes `MelisCmsNewsService::getNewsList` and `getNewsById` through the Melis
  micro-service system (automatic form + input filter generation).
- Lets these methods be called over HTTP POST with parameter validation
  (`status`, `langId`, `dateMin/Max`, `publishDateMin/Max`, `siteId`, `search`, etc.).

---

## 4. Extensions and integrations

The module integrates with several other Melis modules through **listeners** (event
listeners) registered in `src/Module.php` (`onBootstrap`), and through dedicated
configuration files.

### 4.1 Listeners (`src/Listener/`)

| Listener | Role | Context |
|---|---|---|
| `MelisCmsNewsSEORouteListener` | Creates the front routes of SEO news on module load | front (on module load) |
| `MelisCmsNewsRenderPageListener` | Injects the news content into the detail page rendering | front |
| `MelisCmsNewsMetaPageListener` | Injects SEO meta tags into the front page | front |
| `MelisCmsNewsSeoRedirectUrlListener` | Handles redirects (redirect URL / 301) | front |
| `MelisCmsNewsSliderDeletedListener` | Detaches a deleted slider from the news using it | back-office |
| `MelisCmsNewsFlashMessengerListener` | Interface flash messages | back-office |
| `MelisCmsNewsPreviewTypeListener` | Handles the preview type | back-office |
| `MelisCmsNewsTableColumnDisplayListener` | Customizes the list column display | back-office |
| `MelisCmsNewsToolCreatorEditionTypeListener` | Integration with the Tool Creator | back-office |
| `MelisCmsNewsGdprAutoDeleteActionDeleteListener` | GDPR automatic deletion | back-office |
| `MelisCmsNewsServiceMicroServiceListener` | Micro-service exposure | — |

> Branching logic (`src/Module.php`): if the current route is `melis-backoffice`, the
> back-office listeners are attached; otherwise the front listeners (SEO, page rendering,
> redirects) are attached.

### 4.2 SEO

- SEO form: `config/app.forms.php` (key `meliscmsnews_seo_form`)
- Service: `MelisCmsNewsSeoService`
- Table: `melis_cms_news_seo`
- Generates dedicated front URLs per news and per language, with redirect handling and
  canonical tags.

### 4.3 Comments & Dashboard (`MelisCmsComments` module)

- Config: `config/comments.config.php` + `config/plugins/dashboard/dashboard.latest.comments.php`
- Dashboard plugin: `src/Controller/Plugin/Dashboard/DashboardLatestComments.php`
  (shows the latest comments on the Melis dashboard)
- Views: `view/melis-cms-news/plugins/dashboard/latest-comments/*.phtml`

### 4.4 Tags & Categories

- Tags: association declared in `config/module.config.php` (`melis_cms_tag`,
  entity_type `NEWS`) — uses the `MelisCmsTag` module.
- Categories: table `melis_cms_news_category`, category tree loaded via
  `getCategoryTreeViewAction`, add modal `render-news-category-modal`.

### 4.5 Diagnostic

- `config/diagnostic.config.php` — module health checks (integration with the Melis
  diagnostic system).

---

## 5. Front assets

Declared in `config/app.interface.php` (key `ressources`):

- **JS**: `public/js/tools/news.tool.js` (tool logic),
  `public/assets/switch/bootstrap-switch.js` (status switch)
- **CSS**: `public/css/news.css`
- **Compiled bundle** (production, fewer requests):
  `public/build/css/bundle.css`, `public/build/js/bundle.js`
- Front plugins: `public/plugins/` (CSS, init JS, datetimepicker, moment.js)

---

## 6. Internationalization

- Translation files: `language/en_EN.interface.php`, `language/fr_FR.interface.php`
- All interface keys use the `tr_meliscmsnews_*` prefix.
- Translation loading: `Module::createTranslations()` (based on the Melis locale, with
  possible override via `MelisModuleConfig`).

---

## 7. Quick code map

```
melis-cms-news/
├── composer.json                 → module dependencies & metadata
├── config/
│   ├── module.config.php         → routes, services, controllers, plugins, view helpers, tags
│   ├── app.interface.php         → back-office interface tree (menus, tabs, modals)
│   ├── app.tools.php             → news list table configuration
│   ├── app.forms.php             → forms (properties, files, texts, SEO, site…)
│   ├── app.microservice.php      → micro-service exposure (getNewsList, getNewsById)
│   ├── diagnostic.config.php     → diagnostic tests
│   ├── comments.config.php       → MelisCmsComments integration
│   └── plugins/                  → configs of the 3 front plugins + dashboard comments
├── src/
│   ├── Module.php                → bootstrap, listeners, translations, getConfig()
│   ├── Controller/               → ListController, NewsController, Plugins
│   ├── Service/                  → MelisCmsNewsService, MelisCmsNewsSeoService
│   ├── Model/                    → entities + Tables/ (Table Gateways)
│   ├── Listener/                 → listeners (SEO, GDPR, slider, preview, table column…)
│   ├── Form/Factory/             → select factories (BO / front)
│   └── View/Helper/              → 3 view helpers (Latest, List, ShowNews)
├── view/                         → .phtml templates (back-office + front plugins)
├── public/                       → JS/CSS assets, bundles, plugin images
├── language/                     → en_EN / fr_FR translations
├── install/                      → SQL (structure, MWB model, dbdeploy migrations)
└── etc/                          → MarketPlace (images/xml) + MelisAI/doc (this doc)
```

---

## 8. Typical news lifecycle

1. **Creation**: back-office → News → *Add* button → choose site/language.
2. **Editing**: enter texts (Texts tab), add images/documents (Medias), categories/tags
   (Properties), SEO (SEO tab).
3. **Save**: `saveNewsLetterAction` → `MelisCmsNewsService::saveNews()` →
   `melis_cms_news` + `melis_cms_news_texts` (+ SEO) tables.
4. **Publication**: toggle status + publish/unpublish dates.
5. **Front display**: via the *Latest/List/Show News* plugins on the site pages, with a
   `NEWS_DETAIL`-type detail page and dedicated SEO routes.
6. **Deletion**: `deleteNewsById()` removes news + texts + SEO (categories cascade via
   the FK constraint).

---

## 9. Screenshot index (for on-demand retrieval)

All screenshots live in `./images/` (i.e. `/etc/MelisAI/doc/images/`). This table is the
**filename → content** index the MelisAI MCP uses to fetch a specific screenshot on demand;
each row's caption in the body gives the text-only description of what the image shows.

| Image file | Content |
|---|---|
| `meliscmsnews-tool-news-list.png` | News list — the tool's landing page (table, filters, actions) |
| `meliscmsnews-tool-news-new.png` | Creation screen — reduced tab set (Properties, Texts, SEO; no Medias/Preview yet) |
| `meliscmsnews-tool-news-edit-properties.png` | Editing screen — Properties tab |
| `meliscmsnews-tool-news-edit-texts.png` | Editing screen — Texts tab |
| `meliscmsnews-tool-news-edit-medias.png` | Editing screen — Medias tab |
| `meliscmsnews-tool-news-edit-seo.png` | Editing screen — SEO tab |
| `meliscmsnews-tool-news-edit-preview.png` | Editing screen — Preview tab |
| `meliscmsnews-page-menu-plugins-selector.png` | News plugin selector in the page editor |
| `meliscmsnews-page-plugin-latestnews-config-tab-properties.png` | Latest News plugin config — Properties tab |
| `meliscmsnews-page-plugin-latestnews-config-tab-filters.png` | Latest News plugin config — Filters tab |
| `meliscmsnews-page-plugin-newslist-config-tab-properties.png` | List News plugin config — Properties tab |
| `meliscmsnews-page-plugin-newslist-tab-pagination.png` | List News plugin config — Pagination tab |
| `meliscmsnews-page-plugin-newslist-config-tab-filters.png` | List News plugin config — Filters tab |
| `meliscmsnews-page-plugin-newsdetail-config-tab-properties.png` | Show News (detail) plugin config — Properties tab |

---

*Document for AI consumption (MelisAI MCP) — describes the `melisplatform/melis-cms-news`
module. Last reviewed 2026-06-08 against the current source.*
