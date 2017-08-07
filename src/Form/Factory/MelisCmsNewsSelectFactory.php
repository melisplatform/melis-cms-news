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
 * MelisCms news select factory
 */
class MelisCmsNewsSelectFactory extends MelisSelectFactory
{
	protected function loadValueOptions(ServiceLocatorInterface $formElementManager)
	{
		$sliders = array();
	    $serviceManager = $formElementManager->getServiceLocator();		
		$newsSvc        = $serviceManager->get('MelisCmsNewsService');
		$newsData       = [];
		foreach($newsSvc->getNewsList() as $news){
		    if($news->cnews_status) {
                $newsData[$news->cnews_id] = $news->cnews_title;
            }
		}
		return $newsData;
	}

}