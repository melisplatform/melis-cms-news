<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Form\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use MelisCore\Form\Factory\MelisSelectFactory;

/**
 * MelisCmsNews site news select factory
 */
class MelisCmsSiteNewsSelectFactory extends MelisSelectFactory
{
    protected function loadValueOptions(ServiceLocatorInterface $formElementManager)
    {
        $newsData= array();
        $serviceManager = $formElementManager->getServiceLocator();
        $siteTbl        = $serviceManager->get('MelisEngineTableSite');
        $newsSrv        = $serviceManager->get('MelisCmsNewsService');
        
        foreach ($siteTbl->fetchAll() As $val)
        {
            $newsRes = $newsSrv->getNewsList(1, null, null, null, null, 1, null, null, 'cnews_title', 'ASC', $val->site_id);
            
            foreach ($newsRes As $news)
            {
                $newsData[$news->cnews_id] = $val->site_name.' - '.$news->cnews_title;
            }
        }
        
        return $newsData;
    }
}