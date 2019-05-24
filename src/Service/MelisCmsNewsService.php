<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Service;

use MelisCore\Service\MelisCoreGeneralService;

/**
 *
 * This service handles the slider system of Melis.
 *
 */
class MelisCmsNewsService extends MelisCoreGeneralService
{
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
        $search = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $results = array();

        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_list_start', $arrayParameters);

        // Service implementation start
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');

        $news = $newsTable->getNewsList(
            $arrayParameters['status'], $arrayParameters['langId'], $arrayParameters['dateMin'], $arrayParameters['dateMax'], $arrayParameters['publishDateMin'],
            $arrayParameters['publishDateMax'], $arrayParameters['unpublishFilter'], $arrayParameters['start'], $arrayParameters['limit'],
            $arrayParameters['orderColumn'], $arrayParameters['order'], $arrayParameters['siteId'], $arrayParameters['search']
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
        $results = array();

        // Sending service start event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_by_id_start', $arrayParameters);

        // Service implementation start
        /** @var \MelisCmsNews\Model\Tables\MelisCmsNewsTable $newsTable */
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');

        foreach ($newsTable->getNews($arrayParameters['newsId'], $arrayParameters['langId']) as $news) {
            $results = $news;
        }

        // Service implementation end

        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('melis_cms_news_get_news_by_id_end', $arrayParameters);

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
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');
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
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');
        $newsTextTable = $this->getServiceLocator()->get('MelisCmsNewsTextsTable');
        try {
            if ($newsTable->deleteById($arrayParameters['newsId'])) {
                // Remove news text
                $results = empty($newsTextTable->deleteByField('cnews_id', $arrayParameters['newsId'])) ? false : true;
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

        $newsId = (int)$arrayParameters['where'];

        if (!empty($where['cnews_id'])) {
            $newsTextTable = $this->getServiceLocator()->get('MelisCmsNews\Model\Tables\MelisCmsNewsTextsTable');
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
}
