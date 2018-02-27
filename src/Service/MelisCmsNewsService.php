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
     * This service retrieves  a list of news
     * 
     * @param int|null $start The mysql start, mainly used for pagination 
     * @param int|null $limit The mysql limit, mainly used for pagination
     * @param varchar|null $order The mysql ordering function, sets the order on how datas are retrieved
     * @param varchar|null $search Searches the table for the provided query, searcheable columns : mcslide_id, mcslide_name
     * 
     * @return array MelisCmsSlider[]
     */
    
    public function getNewsList($status = null, $langId = null, $dateMin = null, $dateMax = null, $publishDateMin = null, 
                                $publishDateMax = null, $unpublishFilter = false, $start = null, $limit = null, 
                                $orderColumn = null, $order = null, $siteId = null, $search = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $results = array();
   
        // Sending service start event
        $arrayParameters = $this->sendEvent('meliscmsslider_service_get_news_list_start', $arrayParameters);
   
        // Service implementation start
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');

        $news = $newsTable->getNewsList(
            $arrayParameters['status'], $arrayParameters['langId'], $arrayParameters['dateMin'], $arrayParameters['dateMax'], $arrayParameters['publishDateMin'], 
            $arrayParameters['publishDateMax'], $arrayParameters['unpublishFilter'], $arrayParameters['start'], $arrayParameters['limit'], 
            $arrayParameters['orderColumn'], $arrayParameters['order'], $arrayParameters['siteId'], $arrayParameters['search']
        );

        foreach($news as $new){            
            $results[] = $new;
        }
        // Service implementation end
        
        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('meliscmsslider_service_get_news_list_end', $arrayParameters);
         
        return $arrayParameters['results'];
    }
    
    /**
     * This service retrieves a specific slider
     * 
     * @param int $newsId The news id to be fetch
     * 
     * @returns MelisCmsNews[]
     */
    public function getNewsById($newsId, $langId = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $results = array();
        
        // Sending service start event
        $arrayParameters = $this->sendEvent('meliscmsslider_service_get_news_by_id_start', $arrayParameters);
       
        // Service implementation start
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');

        foreach($newsTable->getNews($arrayParameters['newsId'], $arrayParameters['langId']) as $news){                   
            $results = $news;
        }
        
        // Service implementation end
    
        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('meliscmsslider_service_get_news_by_id_end', $arrayParameters);
         
        return $arrayParameters['results'];
    }
    
    /**
     * This service saves the news, if news id is provided then an update will be performed
     * 
     * @param array $news , the array representing the table melis_cms_news
     * @param int $newsId the news id
     * 
     * @return  int|false  newsId on successfull save otherwise false
     */
    public function saveNews($news, $newsId = null)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $results = false;
        
        // Sending service start event
        $arrayParameters = $this->sendEvent('meliscmsslider_service_save_news_start', $arrayParameters);
        
        // Service implementation start
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');
        try{
            $results = $newsTable->save($arrayParameters['news'], $arrayParameters['newsId']);
        }catch(\Exception $e){
            echo $e->getMessage();
        }         
        // Service implementation end
        
        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('meliscmsslider_service_save_news_end', $arrayParameters);
         
        return $arrayParameters['results'];
    }
    
    /**
     * This service deletes the news
     *
     * @param int $newsId The news id to be deleted
     *
     * @return booelan true|false true on success, false on error
     */
    public function deleteNewsById($newsId)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $results = false;
    
        // Sending service start event
        $arrayParameters = $this->sendEvent('meliscmsslider_service_delete_news_by_id_start', $arrayParameters);
    
        // Service implementation start
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');
        try{
            $newsTable->deleteById($arrayParameters['newsId']);            
            $results = true;
        }catch(\Exception $e){
            echo $e->getMessage();
        }
         
        // Service implementation end
    
        // Adding results to parameters for events treatment if needed
        $arrayParameters['results'] = $results;
        // Sending service end event
        $arrayParameters = $this->sendEvent('meliscmsslider_service_delete_news_by_id_start', $arrayParameters);
         
        return $arrayParameters['results'];
    }
  
}