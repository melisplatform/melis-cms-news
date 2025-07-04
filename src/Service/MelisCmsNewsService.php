<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Service;

use MelisCore\Service\MelisGeneralService;

/**
 *
 * This service handles the news system of Melis.
 *
 */
class MelisCmsNewsService extends MelisGeneralService
{
    const PAGE_TYPE_NEWS_DETAIL = 'NEWS_DETAIL';

    /**
     * Retrieves a list of news
     *
     * @param null $status
     * @param null $langId
     * @param null $dateMin
     * @param null $dateMax
     * @param null $publishDateMin
     * @param null $publishDateMax
     * @param bool $unpublishFilter
     * @param null $start
     * @param null $limit
     * @param null $orderColumn
     * @param null $order
     * @param null $siteId
     * @param null $search
     * @param bool $count
     * @return mixed
     */
    public function getNewsList(
        $status = null,
        $langId = null,
        $dateMin = null,
        $dateMax = null,
        $publishDateMin = null,
        $publishDateMax = null,
        $unpublishFilter = false,
        $start = null,
        $limit = null,
        $orderColumn = null,
        $order = null,
        $siteId = null,
        $search = null,
        $count = false
    ) {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $results = array();

        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_list_start', $arrayParameters);

        // Service implementation start
        $newsTable = $this->getServiceManager()->get('MelisCmsNewsTable');

        $news = $newsTable->getNewsList(
            $arrayParameters['status'],
            $arrayParameters['langId'],
            $arrayParameters['dateMin'],
            $arrayParameters['dateMax'],
            $arrayParameters['publishDateMin'],
            $arrayParameters['publishDateMax'],
            $arrayParameters['unpublishFilter'],
            $arrayParameters['start'],
            $arrayParameters['limit'],
            $arrayParameters['orderColumn'],
            $arrayParameters['order'],
            $arrayParameters['siteId'],
            $arrayParameters['search'],
            $arrayParameters['count']
        )->toArray();

        $results = $news;
        // Service implementation end

        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_list_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Retrieves a news via supplied news ID
     *
     * @param $newsId
     * @param null $langId
     * @return mixed
     */
    public function getNewsById($newsId, $langId = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());

        if (empty($langId)) {
            $results = array();
        } else {
            $results = null;
        }

        $arrayParameters['newsId'] = (int) $newsId;

        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_by_id_start', $arrayParameters);

        // Service implementation start
        /** @var \MelisCmsNews\Model\Tables\MelisCmsNewsTable $newsTable */
        $newsTable = $this->getServiceManager()->get('MelisCmsNewsTable');

        foreach ($newsTable->getNews($arrayParameters['newsId'], $arrayParameters['langId']) as $news) {
            if (empty($langId)) {
                $results[] = $news;
            } else {
                $results = $news;
            }
        }

        // Service implementation end

        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_by_id_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Retrieves news via supplied array of news ID
     *
     * @param $newsId
     * @param null $langId
     * @param array $where
     * @return mixed
     */
    public function getNewsByIdArray(array $newsIdArray, $langId, array $where = [])
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());

        $results = [];

        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_by_id_array_start', $arrayParameters);

        // Service implementation start
        /** @var \MelisCmsNews\Model\Tables\MelisCmsNewsTable $newsTable */
        $newsTable = $this->getServiceManager()->get('MelisCmsNewsTable');

        $results = $newsTable->getNewsByIdArray($arrayParameters['newsIdArray'], $arrayParameters['langId'], $arrayParameters['where']);

        // Service implementation end

        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_by_id_array_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Saves the news, if news id is provided then an update will be performed
     *
     * @param $news
     * @param null $newsId
     * @return mixed
     */
    public function saveNews($news, $newsId = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $results = false;

        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_cms_news_save_news_start', $arrayParameters);

        // Service implementation start
        /** @var \MelisCmsNews\Model\Tables\MelisCmsNewsTable $newsTable */
        $newsTable = $this->getServiceManager()->get('MelisCmsNewsTable');
        try {
            $results = $newsTable->save($arrayParameters['news'], $arrayParameters['newsId']);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        // Service implementation end

        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('melis_cms_news_save_news_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Deletes a news via supplied news ID
     *
     * @param $newsId
     * @return mixed
     */
    public function deleteNewsById($newsId)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $results = false;

        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_cms_news__delete_news_by_id_start', $arrayParameters);

        // Service implementation start
        $newsTable = $this->getServiceManager()->get('MelisCmsNewsTable');
        $newsTextTable = $this->getServiceManager()->get('MelisCmsNewsTextsTable');
        $newsSeoTable = $this->getServiceManager()->get('MelisCmsNewsSeoTable');
        try {
            if ($newsTable->deleteById($arrayParameters['newsId'])) {
                // Remove news text
                $newsTextDeleteResults = empty($newsTextTable->deleteByField('cnews_id', $arrayParameters['newsId'])) ? false : true;
                //Remove seo data
                $seoData = $newsSeoTable->getEntryByField('cnews_id', $arrayParameters['newsId'])->toArray();
                $seoDeleteResults = true;
                //delete seo data if there are any
                if ($seoData) {
                    $seoDeleteResults = empty($newsSeoTable->deleteByField('cnews_id', $arrayParameters['newsId'])) ? false : true;
                }
                if ($newsTextDeleteResults && $seoDeleteResults) {
                    $results = true;
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        // Service implementation end

        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('melis_cms_news__delete_news_by_id_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Returns text via news ID
     * @param int|null $newsId
     * @return mixed
     */
    public function getPostText(int $newsId = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $results = [];

        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_post_text_start', $arrayParameters);

        $newsId = (int)$arrayParameters['newsId'];

        if (!empty($newsId)) {
            $newsTextTable = $this->getServiceManager()->get('MelisCmsNews\Model\Tables\MelisCmsNewsTextsTable');
            try {
                $results = $newsTextTable->getEntryByField('cnews_id', $newsId);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_post_text_end', $arrayParameters);

        return $arrayParameters['results'];
    }

    /**
     * Provides value options for "News details page selector"
     * - Pages with type: Page – News details
     * @param int|null $siteId
     * @return mixed
     */
    public function getNewsDetailsPagesBySite(int $siteId = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());

        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_details_pages_start', $arrayParameters);

        $published = [];
        $saved = [];

        /** @var \MelisEngine\Model\Tables\MelisPagePublishedTable $publishedPages */
        /** @var \MelisEngine\Model\Tables\MelisPageSavedTable $savedPages */
        $siteId = (int)$arrayParameters['siteId'];
        $publishedPages = $this->getServiceManager()->get('MelisEngineTablePagePublished');
        $savedPages = $this->getServiceManager()->get('MelisEngineTablePageSaved');
        $publishedPages = $publishedPages->getPagesByType(self::PAGE_TYPE_NEWS_DETAIL, $siteId);
        $savedPages = $savedPages->getPagesByType(self::PAGE_TYPE_NEWS_DETAIL, $siteId);

        if ($publishedPages->count()) {
            foreach ($publishedPages->toArray() as $index => $page) {
                $published[$page['page_id']] = $page;
            }
        }

        if ($savedPages->count()) {
            foreach ($savedPages->toArray() as $index => $page) {
                $saved[$page['page_id']] = $page;
            }
        }

        /**
         * "Array Union Operator"
         * The array union operator (+) can also be used to merge arrays. If keys in the arrays match, values earlier in
         * the expression will overwrite those later in the expression. This is true for both numeric and string keys.
         */
        $detailPages = $saved + $published;

        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $detailPages;
        // Sending service end event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_details_pages_end', $arrayParameters);

        return $arrayParameters['results'];
    }
}
