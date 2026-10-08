# Changelog

## v6.0.8 - 2026-10-05
### Security
* Page-editor: internationalize the React UI (was hardcoded French)
### Added
* **plugin-config:** full-React config tabs for melis-cms-news plugins
### Fixed
* **plugins:** news list/latest news modal selects the page's site by default (0011039)

## v6.0.7 - 2026-09-25
### Fixed
* **news:** don't crash the legacy news tabs when MelisCmsTags is absent

## v6.0.6 - 2026-09-23
### Security
* **security:** declare the tool key on the News workflow comments controller (audit item 7.0)
* **security:** parameterised SQL for date filters, ORDER BY and hand-quoted values (audit item 11.0)

## v6.0.5 - 2026-08-20
### Security
* Gate mutating/data actions in legacy tool controllers (CWE-862)
### Fixed
* **news-react:** stop the right sidebar fields looking greyed out
* **news:** duplicate file check missed apostrophes and image3
* **react:** per-language texts + shared delete modal
### Docs
* **melisai:** React back-office AI documentation for MelisCmsNews

## v6.0.3 - 2026-08-12
### Fixed
* **news:** don't break news when the optional MelisCmsTags module is absent

## v6.0.2 - 2026-08-11
### Added
* **news-react:** refresh button next to the New/Old toggle + auto-refresh on back
### Fixed
* **news-react:** don't write cnews_author_account when the column is absent

## v6.0.1 - 2026-08-10
### Added
* **composer:** add docs link and authors block, swap zf2 keyword for laminas, bump php constraint to ^8.3|^8.5
### Dependencies & build
* **deps:** bump postcss from 8.5.15 to 8.5.26 in /ui-react

## v6.0.0 - 2026-08-10
### Security
* **security:** add SECURITY.md (private vulnerability reporting policy)
* Fix audit findings
* **rights:** put the News rights key back on the left-menu path
### Added
* **cms-news:** React news tool updates (form/list/preview/i18n) + rebuild brick
* **marketplace:** add React back-office screenshots (etc/MarketPlace/images/react)
* **news-react:** mobile-responsive list + form (essential columns, stacked sidebar, aligned filters)
* **webservices:** expose MelisCmsNewsSeoService::getPageLink
* **webservices:** translatable service description for the token WS listing
* **cms-react:** listes infinies keyset + tri server-side + icones de tri unifiees
* **news-react:** ouverture externe d'un article + onglet de prévisualisation
* **news-comments:** all-statuses moderation, delete dialog, pagination
* **news-react:** author field + native comments moderation
* **cms-news:** legacy category picker reflects real active/inactive status
* **cms-news:** React category picker shows real active/inactive status
* **react:** add a "Reset filters" button to the tool list page(s)
* **news:** reflete le /:id du sous-onglet d'edition dans l'URL (cosmetique replaceState)
* **news-react:** éditeur TinyMCE (config tool) unique, calendrier localisé, libellés
* **news-react:** section Tags dans l'éditeur News (module MelisCmsTags)
* **news-react:** workflow de validation, upload médias, i18n FR/EN, fix scroll
* **news:** persistent brick (no reload on top-tab switch)
* **news-react:** capacités d'outil (list/create/edit/delete/export)
* **news:** categories modulaires (category2) + reordre SEO/Categories/Slider + dates selon la langue du BO
* **news:** sous-onglets internes (style Slider) + paragraphes 1-10 + drag'n'drop + switch statut published/unpublished + drapeaux/dates dynamiques + gating Workflow(SmallBusiness)/Slider
* **react-api:** enforce des droits News via denyUnlessAccess() (meliscmsnews_left_menu) sur chaque endpoint
* **react-brick:** native React News UI (List + Form) as a modular brick
* Add MelisAI module documentation for AI consumption
* Add explicit nullable param types (PHP 8.4 deprecation)
* Added closing tags
### Fixed
* **mobile:** touch-compatible column drag-and-drop
* **security:** harden legacy file/dir creation & output escaping
* **news-react:** narrow-viewport regressions from the merged responsive work
* **news-react:** reliable paragraph drag-and-drop with clear drop target
* **news-seo:** don't bind newsId to page routes when the news seo url is empty
* **cms-news:** React category dot uniform green, matching legacy
* **cms-news:** align React categories with legacy (site scope + inactive)
* **news-react:** jstree pour vue « Old », XLSX externalisé, date_min SQL
* **cms-news:** FO news filter 500 — convert date_min to MySQL format
* **news:** jstree chargé en vue old (react-tool-page) → modale Catégories OK
* **news-react:** panneaux colonnes/export scrollables (évite le débordement)
* Keep the GET query when injecting newsId on front pages
* **react:** keep the legacy "Old" view legacy (no more hijack to the React editor)
* **news-react:** nom du site = libellé (site_label) partout
* **news-react:** repli des tags sur une traduction non vide (fini les tags blancs)
* **react:** outil Languages en page blanche — collision de route /languages + ErrorBoundary
* **react:** update NewsFormPage + rebuild brick
* **react:** vue Old News en pleine hauteur (iframe full size)
* **react-brick:** don't override host theme (light/dark) from injected CSS
* Fixed closing tag problem
### Changed
* **news-react:** modale Workflow MUTUALISÉE (fournie par small-business)
* Updated for com extension
* 8805
### Dependencies & build
* **composer:** bump melis-core/melis-engine/melis-front/melis-cms constraint to ^6.0
* Local WIP snapshot before reconcile (20260806-114605)
* **ui-react:** commit pending news list/form UI files (already in parent local6-2)
### Docs
* **meliscmsnews:** rewrite as two-part doc (functional guide + technical reference with examples)
* Rename news screenshots to convention and refresh doc

## v5.3.8 - 2025-07-15
### Changed
* 101 updates

## v5.3.7 - 2025-07-04
### Changed
* Casting news id param service

## v5.3.6 - 2025-04-08
### Added
* Add category in news
### Fixed
* Fix add news error
* Fix missing category bug
* Fix error with saving news tags
### Changed
* Redirect if unpublish news
* Update MelisCmsNewsController.php
* Missing style bug
* GetNewsByIdArray
* Integrate with tags
* Filter categories by site
* Preselect categories
* Integrate categories

## v5.3.1 - 2024-10-08
### Dependencies & build
* Update composer.json

## v5.3.0 - 2024-09-25
### Fixed
* Fix issue 6598 regression
### Changed
* Check fix for bootstrapSwitch issue
* Issue on bootstrapSwitch
* Bs5 tab
* Update jQuery 3.7.1 migration
* JQuery 3.7.1 migration
* Update on jQuery migration

## v5.2.0 - 2024-06-06
* Maintenance release.

## v5.1.1 - 2024-04-08
### Added
* Added handling for empty news data
### Fixed
* Fix on numeric separator issue
* Fix bug in listing news having text entries in multiple languages
### Changed
* Tinymce update
* Tinymce type tool full toolbar buttons
* Checking if affected with issue 4906
* Remove the added scripts for iframe issue
* Check issue on news tool iframe
* Checking issue on owl carousel iframe
* Checking owl carousel inside iframe issue
* Update on tinymce declaration with in phtml
* Update on minitemplate
* Update on tinymce 6.7.0
### Dependencies & build
* Rebuilt the asset bundle

## v5.1.0 - 2024-02-13
### Changed
* MelisCmsNewsBOSelectFactory error
* Update listener
* AllowDynamicProperties + fix deprecated preg_replace
* Updae php version to php 8.3

## v5.0.2 - 2023-05-23
### Changed
* Saving new seo issue fixed

## v5.0.1 - 2022-11-09
* Maintenance release.

## v5.0.0 - 2022-06-22
### Added
* Added files for the workflow feature
* Added space on error message
* Added dbdeploy files
### Fixed
* Fix bug
* Fix ticket
### Changed
* Hide workflow button if SB is not active
* Use Small Business workflow functionality for the news module
* Updated tiny mce setup option
* Updated filels
* Updated error data
* Updated build
* Updated/added files for news seo
* Changed deprecated ArraySerializable to ArraySerializableHydrator and updated other functions affected by php 8
