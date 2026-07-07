<?php

namespace MelisCmsNews\Controller;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\Session\Container as SessionContainer;
use MelisCore\Controller\MelisAbstractActionController;
use MelisReactApi\Controller\CapabilityGuardTrait;

/**
 * API REST pour les actualités (MelisCmsNews).
 *
 * Routes :
 *   GET    /melis/react-api/languages           → langues CMS disponibles
 *   GET    /melis/react-api/news                → liste paginée
 *   GET    /melis/react-api/news/:id            → détail complet (SEO, paragraphes, images, catégories)
 *   POST   /melis/react-api/news/save           → créer / mettre à jour (SEO, paragraphes, catégories)
 *   DELETE /melis/react-api/news/delete/:id     → supprimer
 *   GET    /melis/react-api/news/stats          → statistiques
 *   GET    /melis/react-api/news/categories     → catégories disponibles
 *   GET    /melis/react-api/news/preview/:id    → URL de prévisualisation
 *   GET    /melis/react-api/sites               → liste des sites
 */
class MelisCmsNewsReactApiController extends MelisAbstractActionController
{
    use CapabilityGuardTrait;

    // Outil Actualités — clé des capacités (cf. config/react.capabilities.php + denyUnlessCan()).
    private const MELIS_KEY = 'meliscmsnews_left_menu';

    // ─── GET /languages ─────────────────────────────────────────────────────

    public function languagesAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $db   = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
            $rows = $db->query(
                'SELECT lang_cms_id, lang_cms_locale, lang_cms_name FROM melis_cms_lang ORDER BY lang_cms_id',
                []
            );

            $languages = [];
            foreach ($rows as $row) {
                $r           = (array) $row;
                $languages[] = [
                    'id'     => (int)    ($r['lang_cms_id']     ?? 0),
                    'locale' => (string) ($r['lang_cms_locale'] ?? ''),
                    'name'   => (string) ($r['lang_cms_name']   ?? ''),
                ];
            }

            return $this->jsonResponse(['success' => true, 'data' => $languages]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── GET /news ──────────────────────────────────────────────────────────

    public function listAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        if ($deny = $this->denyUnlessCan('list')) { return $deny; }

        try {
            $page   = max(1, (int) $this->params()->fromQuery('page', 1));
            $limit  = min(9999, max(1, (int) $this->params()->fromQuery('limit', 20)));
            $search = $this->params()->fromQuery('search', null);
            $rawStatus = $this->params()->fromQuery('status', '');
            $status = ($rawStatus !== '' && $rawStatus !== null) ? (int) $rawStatus : null;
            $rawSite = $this->params()->fromQuery('siteId', '');
            $siteId = ($rawSite !== '' && $rawSite !== null) ? (int) $rawSite : null;
            // langId optional — null = toutes langues, valeur explicite ou session
            $rawLang = $this->params()->fromQuery('langId', '');
            $langId  = ($rawLang !== '' && $rawLang !== null) ? (int) $rawLang : null;
            $start  = ($page - 1) * $limit;

            $service = $this->getServiceManager()->get('MelisCmsNewsService');

            $items = $service->getNewsList(
                $status, $langId,
                null, null, null, null, 0,
                $start, $limit,
                'cnews_id', 'DESC',
                $siteId, $search ?: null, false
            );

            $total = $service->getNewsList(
                $status, $langId,
                null, null, null, null, 0,
                0, null,
                'cnews_id', 'DESC',
                $siteId, $search ?: null, true
            );

            $rows = [];
            foreach (($items ?: []) as $row) {
                $r      = is_object($row) ? (array) $row : $row;
                $rows[] = $this->formatNewsItem($r);
            }

            return $this->jsonResponse([
                'success' => true,
                'data'    => [
                    'items' => $rows,
                    'total' => is_numeric($total) ? (int) $total : count($rows),
                    'page'  => $page,
                    'limit' => $limit,
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── GET /news/:id ──────────────────────────────────────────────────────

    public function getAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $id       = (int) $this->params('id');
            $rawLang  = $this->params()->fromQuery('langId', '');
            $langId   = ($rawLang !== '' && $rawLang !== null)
                ? (int) $rawLang
                : $this->getCurrentLangId();

            if (!$id) {
                return $this->jsonResponse(['success' => false, 'error' => 'Missing id'], 400);
            }

            $service = $this->getServiceManager()->get('MelisCmsNewsService');

            // Le CONTENU (titre, sous-titre, paragraphes) est stocké PAR LANGUE dans
            // melis_cms_news_texts (clé cnews_id + cnews_lang_id). On charge la traduction
            // de la langue demandée. Si elle n'existe pas encore, on NE retombe PAS sur une
            // autre langue : on garde les champs INDÉPENDANTS de la langue (site, statut, dates,
            // slider — table melis_cms_news) et on laisse le TEXTE VIDE pour que l'utilisateur
            // saisisse la nouvelle traduction (le save créera la ligne de cette langue).
            $langRow = $service->getNewsById($id, $langId); // ligne base+texte de CETTE langue (ou vide)

            if (!empty($langRow)) {
                $row = (array) $langRow;
            } else {
                $anyRows = $service->getNewsById($id); // toutes les langues → champs de base
                if (empty($anyRows)) {
                    return $this->jsonResponse(['success' => false, 'error' => 'Not found'], 404);
                }
                $row = (array) current($anyRows);
                // Vide le texte spécifique à la langue (garde site/statut/dates/slider).
                foreach ([
                    'cnews_text_id', 'cnews_lang_id', 'cnews_title', 'cnews_subtitle',
                    'cnews_paragraph1', 'cnews_paragraph2', 'cnews_paragraph3', 'cnews_paragraph4',
                    'cnews_paragraph5', 'cnews_paragraph6', 'cnews_paragraph7', 'cnews_paragraph8',
                    'cnews_paragraph9', 'cnews_paragraph10', 'cnews_paragraph_order',
                ] as $k) {
                    $row[$k] = null;
                }
            }

            // SEO (separate table, language-specific)
            $seoRow = $this->getNewsSeoRow($id, $langId);

            // Categories assigned to this news item
            $categories = $this->getNewsCategoryIds($id);

            return $this->jsonResponse([
                'success' => true,
                'data'    => $this->formatNewsDetail($row, $seoRow, $categories),
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── POST /news/save ────────────────────────────────────────────────────

    public function saveAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $body   = json_decode($this->getRequest()->getContent(), true) ?? [];
            $langId = isset($body['langId']) && $body['langId']
                ? (int) $body['langId']
                : $this->getCurrentLangId();
            $id     = isset($body['id']) && $body['id'] ? (int) $body['id'] : null;

            // Enforcement capacité : créer (nouvel article) vs éditer (existant).
            if ($deny = $this->denyUnlessCan($id ? 'edit' : 'create')) { return $deny; }

            if (empty($body['title'])) {
                return $this->jsonResponse(['success' => false, 'error' => 'Le titre est obligatoire'], 422);
            }
            if (empty($body['siteId'])) {
                return $this->jsonResponse(['success' => false, 'error' => 'Le site est obligatoire'], 422);
            }

            $service = $this->getServiceManager()->get('MelisCmsNewsService');

            // Save main news record
            $newsData = [
                'cnews_status'         => (int) ($body['status']   ?? 0),
                'cnews_site_id'        => (int)  $body['siteId'],
                'cnews_publish_date'   => $body['publishDate']   ?: null,
                'cnews_unpublish_date' => $body['unpublishDate'] ?: null,
                'cnews_slider_id'      => isset($body['sliderId']) && $body['sliderId']
                    ? (int) $body['sliderId']
                    : null,
            ];

            $newsId = $service->saveNews($newsData, $id);

            if (!$newsId) {
                return $this->jsonResponse(['success' => false, 'error' => 'Échec de la sauvegarde'], 500);
            }

            // Save multilingual text content (paragraphs 1-10, dans l'ordre reçu).
            // Le front envoie déjà les paragraphes RÉORDONNÉS (drag'n'drop) : on les écrit
            // séquentiellement dans les colonnes 1..N et on enregistre cnews_paragraph_order
            // (liste des colonnes remplies, dans l'ordre) pour que le rendu front respecte
            // l'ordre. Les colonnes non utilisées sont vidées.
            $paragraphs = array_values($body['paragraphs'] ?? []);
            $textData   = [
                'cnews_title'    => $body['title']    ?? '',
                'cnews_subtitle' => $body['subtitle'] ?? '',
            ];
            $usedCols = [];
            for ($i = 0; $i < 10; $i++) {
                $col     = 'cnews_paragraph' . ($i + 1);
                $content = isset($paragraphs[$i]) ? (string) $paragraphs[$i] : '';
                $textData[$col] = $content;
                if ($content !== '') { $usedCols[] = $col; }
            }
            $textData['cnews_paragraph_order'] = implode('-', $usedCols);
            $this->saveNewsText((int) $newsId, $langId, $textData);

            // Save SEO (language-specific)
            if (!empty($body['seo'])) {
                $this->saveNewsSeo((int) $newsId, $langId, $body['seo']);
            }

            // Sync categories
            if (array_key_exists('categoryIds', $body)) {
                $this->syncNewsCategories((int) $newsId, (array) $body['categoryIds']);
            }

            return $this->jsonResponse([
                'success' => true,
                'data'    => ['id' => (int) $newsId],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── DELETE /news/delete/:id ─────────────────────────────────────────────

    public function deleteAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        if ($deny = $this->denyUnlessCan('delete')) { return $deny; }

        try {
            $id = (int) $this->params('id');
            if (!$id) {
                return $this->jsonResponse(['success' => false, 'error' => 'Missing id'], 400);
            }

            $service = $this->getServiceManager()->get('MelisCmsNewsService');
            $service->deleteNewsById($id);

            return $this->jsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── GET /news/stats ────────────────────────────────────────────────────

    public function statsAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $db  = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
            $row = $db->query(
                'SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN cnews_status = 1 THEN 1 ELSE 0 END) AS published,
                    SUM(CASE WHEN cnews_status = 0 THEN 1 ELSE 0 END) AS draft
                 FROM melis_cms_news',
                []
            )->current();

            $r = is_object($row) ? (array) $row : ($row ?? []);

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'total'     => (int) ($r['total']     ?? 0),
                    'published' => (int) ($r['published'] ?? 0),
                    'draft'     => (int) ($r['draft']     ?? 0),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── GET /news/categories ───────────────────────────────────────────────

    public function categoriesAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $rawLang = $this->params()->fromQuery('langId', '');
            $langId  = ($rawLang !== '' && $rawLang !== null)
                ? (int) $rawLang
                : $this->getCurrentLangId();

            $db   = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
            $rows = $db->query(
                'SELECT c.cat2_id, c.cat2_father_cat_id, t.catt2_name
                 FROM melis_cms_category2 c
                 LEFT JOIN melis_cms_category2_trans t
                        ON t.catt2_category_id = c.cat2_id
                       AND t.catt2_lang_id = ?
                 WHERE c.cat2_status = 1
                 ORDER BY c.cat2_order',
                [$langId]
            );

            $categories = [];
            foreach ($rows as $row) {
                $r            = (array) $row;
                $categories[] = [
                    'id'          => (int)    ($r['cat2_id']            ?? 0),
                    'fatherCatId' => (int)    ($r['cat2_father_cat_id'] ?? -1),
                    'name'        => (string) ($r['catt2_name']         ?? ''),
                ];
            }

            return $this->jsonResponse(['success' => true, 'data' => $categories]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── GET /news/preview/:id ──────────────────────────────────────────────

    public function previewUrlAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $id = (int) $this->params('id');
            if (!$id) {
                return $this->jsonResponse(['success' => false, 'error' => 'Missing id'], 400);
            }

            // Retrieve the siteId for this news
            $db     = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
            $result = $db->query(
                'SELECT cnews_site_id FROM melis_cms_news WHERE cnews_id = ?',
                [$id]
            )->current();

            $siteId = $result ? (int) ((array) $result)['cnews_site_id'] : null;

            $previewUrl = null;

            if ($siteId) {
                $service = $this->getServiceManager()->get('MelisCmsNewsService');
                $pages   = $service->getNewsDetailsPagesBySite($siteId);

                if (!empty($pages)) {
                    $page   = is_object(current($pages)) ? (array) current($pages) : current($pages);
                    $pageId = $page['tree_page_id'] ?? null;

                    if ($pageId) {
                        $previewUrl = '/id/' . (int) $pageId . '?newsId=' . $id;
                    }
                }
            }

            return $this->jsonResponse([
                'success' => true,
                'data'    => ['previewUrl' => $previewUrl],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── GET /sites ─────────────────────────────────────────────────────────

    public function sitesAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $db   = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
            $rows = $db->query(
                'SELECT site_id, site_name FROM melis_cms_site ORDER BY site_name',
                []
            );

            $sites = [];
            foreach ($rows as $row) {
                $r       = (array) $row;
                $sites[] = [
                    'id'   => (int)    ($r['site_id']   ?? 0),
                    'name' => (string) ($r['site_name'] ?? ''),
                ];
            }

            return $this->jsonResponse(['success' => true, 'data' => $sites]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── Format helpers ──────────────────────────────────────────────────────

    private function formatNewsItem(array $row): array
    {
        return [
            'id'            => (int)    ($row['cnews_id']            ?? 0),
            'title'         => (string) ($row['cnews_title']         ?? ''),
            'subtitle'      => (string) ($row['cnews_subtitle']      ?? ''),
            'status'        => (int)    ($row['cnews_status']        ?? 0),
            'siteId'        => (int)    ($row['cnews_site_id']       ?? 0),
            'siteName'      => (string) ($row['site_name']           ?? ''),
            'creationDate'  => (string) ($row['cnews_creation_date'] ?? ''),
            'publishDate'   => $row['cnews_publish_date']   ?: null,
            'unpublishDate' => $row['cnews_unpublish_date'] ?: null,
        ];
    }

    /**
     * Renvoie les paragraphes NON VIDES d'une ligne de texte, dans l'ordre d'affichage
     * défini par cnews_paragraph_order (liste de noms de colonnes séparés par '-').
     * Ordre absent/incomplet → complété par l'ordre naturel cnews_paragraph1..10.
     *
     * @return string[]
     */
    private function orderedParagraphs(array $row): array
    {
        $natural = [];
        for ($i = 1; $i <= 10; $i++) { $natural[] = 'cnews_paragraph' . $i; }

        $order = [];
        if (!empty($row['cnews_paragraph_order'])) {
            foreach (explode('-', (string) $row['cnews_paragraph_order']) as $col) {
                $col = trim($col);
                if ($col !== '' && in_array($col, $natural, true) && !in_array($col, $order, true)) {
                    $order[] = $col;
                }
            }
        }
        // Complète avec les colonnes manquantes (ordre naturel) pour ne rien perdre.
        foreach ($natural as $col) {
            if (!in_array($col, $order, true)) { $order[] = $col; }
        }

        $paragraphs = [];
        foreach ($order as $col) {
            $val = isset($row[$col]) ? (string) $row[$col] : '';
            if ($val !== '') { $paragraphs[] = $val; }
        }
        return $paragraphs;
    }

    private function formatNewsDetail(array $row, array $seoRow, array $categoryIds): array
    {
        return array_merge($this->formatNewsItem($row), [
            // Paragraphes (colonnes BDD paragraph1-10) renvoyés DANS L'ORDRE d'affichage.
            // cnews_paragraph_order (legacy) = liste de noms de colonnes séparés par '-'
            // (ex. "cnews_paragraph2-cnews_paragraph1"). Vide → ordre naturel 1..10.
            'paragraphs'  => $this->orderedParagraphs($row),
            // Images et documents
            'image1'      => $row['cnews_image1']      ?: null,
            'image2'      => $row['cnews_image2']      ?: null,
            'image3'      => $row['cnews_image3']      ?: null,
            'document1'   => $row['cnews_documents1']  ?: null,
            'document2'   => $row['cnews_documents2']  ?: null,
            'document3'   => $row['cnews_documents3']  ?: null,
            // Slider
            'sliderId'    => isset($row['cnews_slider_id']) && $row['cnews_slider_id']
                ? (int) $row['cnews_slider_id']
                : null,
            // Catégories (IDs assignés)
            'categoryIds' => $categoryIds,
            // SEO
            'seo'         => [
                'url'             => (string) ($seoRow['cnews_seo_url']              ?? ''),
                'urlRedirect'     => (string) ($seoRow['cnews_seo_url_redirect']     ?? ''),
                'url301'          => (string) ($seoRow['cnews_seo_url_301']          ?? ''),
                'metaTitle'       => (string) ($seoRow['cnews_seo_meta_title']       ?? ''),
                'metaDescription' => (string) ($seoRow['cnews_seo_meta_description'] ?? ''),
                'canonical'       => (string) ($seoRow['cnews_seo_canonical']        ?? ''),
            ],
        ]);
    }

    // ─── Private data helpers ────────────────────────────────────────────────

    /** Retourne la ligne SEO pour un news + langue donnés (tableau vide si absent). */
    private function getNewsSeoRow(int $newsId, int $langId): array
    {
        try {
            $db  = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
            $row = $db->query(
                'SELECT * FROM melis_cms_news_seo WHERE cnews_id = ? AND cnews_seo_lang_id = ? LIMIT 1',
                [$newsId, $langId]
            )->current();

            return $row ? (array) $row : [];
        } catch (\Throwable) {
            return [];
        }
    }

    /** Retourne les IDs de catégories assignés à une news. */
    private function getNewsCategoryIds(int $newsId): array
    {
        try {
            $db   = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
            $rows = $db->query(
                'SELECT cnc_cat2_id FROM melis_cms_news_category WHERE cnc_cnews_id = ? ORDER BY cnc_order',
                [$newsId]
            );

            $ids = [];
            foreach ($rows as $row) {
                $r     = (array) $row;
                $ids[] = (int) $r['cnc_cat2_id'];
            }

            return $ids;
        } catch (\Throwable) {
            return [];
        }
    }

    // ─── Private save helpers ────────────────────────────────────────────────

    private function saveNewsText(int $newsId, int $langId, array $textData): void
    {
        $db       = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
        $existing = $db->query(
            'SELECT cnews_text_id FROM melis_cms_news_texts WHERE cnews_id = ? AND cnews_lang_id = ? LIMIT 1',
            [$newsId, $langId]
        )->current();

        $textsTable = $this->getServiceManager()->get('MelisCmsNewsTextsTable');

        if ($existing) {
            $row = (array) $existing;
            // MelisGenericTable::update($datas, $whereField, $whereValue)
            $textsTable->update($textData, 'cnews_text_id', (int) $row['cnews_text_id']);
        } else {
            $textData['cnews_id']      = $newsId;
            $textData['cnews_lang_id'] = $langId;
            // MelisGenericTable::save($datas) with no id → INSERT
            $textsTable->save($textData);
        }
    }

    private function saveNewsSeo(int $newsId, int $langId, array $seo): void
    {
        $db       = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
        $existing = $db->query(
            'SELECT cnews_seo_id FROM melis_cms_news_seo WHERE cnews_id = ? AND cnews_seo_lang_id = ? LIMIT 1',
            [$newsId, $langId]
        )->current();

        $data = [
            'cnews_seo_url'              => $seo['url']             ?? null,
            'cnews_seo_url_redirect'     => $seo['urlRedirect']     ?? null,
            'cnews_seo_url_301'          => $seo['url301']          ?? null,
            'cnews_seo_meta_title'       => $seo['metaTitle']       ?? null,
            'cnews_seo_meta_description' => $seo['metaDescription'] ?? null,
            'cnews_seo_canonical'        => $seo['canonical']       ?? null,
        ];

        if ($existing) {
            $seoId = (int) ((array) $existing)['cnews_seo_id'];
            $db->query(
                'UPDATE melis_cms_news_seo SET
                    cnews_seo_url = ?,
                    cnews_seo_url_redirect = ?,
                    cnews_seo_url_301 = ?,
                    cnews_seo_meta_title = ?,
                    cnews_seo_meta_description = ?,
                    cnews_seo_canonical = ?
                 WHERE cnews_seo_id = ?',
                [
                    $data['cnews_seo_url'],
                    $data['cnews_seo_url_redirect'],
                    $data['cnews_seo_url_301'],
                    $data['cnews_seo_meta_title'],
                    $data['cnews_seo_meta_description'],
                    $data['cnews_seo_canonical'],
                    $seoId,
                ]
            );
        } else {
            $data['cnews_id']          = $newsId;
            $data['cnews_seo_lang_id'] = $langId;
            $db->query(
                'INSERT INTO melis_cms_news_seo
                    (cnews_id, cnews_seo_lang_id, cnews_seo_url, cnews_seo_url_redirect,
                     cnews_seo_url_301, cnews_seo_meta_title, cnews_seo_meta_description, cnews_seo_canonical)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $data['cnews_id'],
                    $data['cnews_seo_lang_id'],
                    $data['cnews_seo_url'],
                    $data['cnews_seo_url_redirect'],
                    $data['cnews_seo_url_301'],
                    $data['cnews_seo_meta_title'],
                    $data['cnews_seo_meta_description'],
                    $data['cnews_seo_canonical'],
                ]
            );
        }
    }

    private function syncNewsCategories(int $newsId, array $categoryIds): void
    {
        $db = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');

        // Delete existing assignments
        $db->query(
            'DELETE FROM melis_cms_news_category WHERE cnc_cnews_id = ?',
            [$newsId]
        );

        // Re-insert selected categories
        foreach (array_values($categoryIds) as $order => $catId) {
            $catId = (int) $catId;
            if ($catId <= 0) continue;

            $db->query(
                'INSERT INTO melis_cms_news_category (cnc_cnews_id, cnc_cat2_id, cnc_order) VALUES (?, ?, ?)',
                [$newsId, $catId, $order]
            );
        }
    }

    // ─── Common helpers ──────────────────────────────────────────────────────

    private function getCurrentLangId(): int
    {
        $container = new SessionContainer('meliscore');
        return (int) ($container['melis-lang-id'] ?? 1);
    }

    private function jsonResponse(array $data, int $status = 200): HttpResponse
    {
        /** @var HttpResponse $response */
        $response = $this->getResponse();
        $response->setStatusCode($status);
        $response->getHeaders()->addHeaders([
            'Content-Type'           => 'application/json; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        $response->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $response;
    }

    private function errorResponse(\Throwable $e, int $status = 500): HttpResponse
    {
        return $this->jsonResponse([
            'success' => false,
            'error'   => $e->getMessage(),
            'file'    => basename($e->getFile()) . ':' . $e->getLine(),
        ], $status);
    }

    private function isAuthenticated(): bool
    {
        return $this->getServiceManager()->get('MelisCoreAuth')->hasIdentity();
    }

    /**
     * Rights guard for the News tool: every endpoint requires ACCESS to the tool
     * (`meliscmsnews_left_menu`), not merely an authenticated session — a user without the tool in
     * their rights can't read/write news via the API (mirrors the Users tool guard). Returns the
     * deny response or null.
     */
    private function denyUnlessAccess(): ?HttpResponse
    {
        if (!$this->isAuthenticated()) {
            return $this->jsonResponse(['success' => false, 'error' => 'Unauthenticated'], 401);
        }
        try {
            if (!$this->getServiceManager()->get('MelisCoreRights')->canAccess('meliscmsnews_left_menu')) {
                return $this->jsonResponse(['success' => false, 'error' => 'Forbidden'], 403);
            }
        } catch (\Throwable) {}
        return null;
    }
}
