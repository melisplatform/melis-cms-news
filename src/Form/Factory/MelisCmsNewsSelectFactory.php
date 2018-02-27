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
use Zend\Session\Container;

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
		
		$container = new Container('melisplugins');
		$langId = $container['melis-plugins-lang-id'];
		
		foreach($newsSvc->getNewsList(null, $langId) as $news){
		    if($news->cnews_status) {
                $newsData[$news->cnews_id] = $news->cnews_title;
            }
		}
		return $newsData;
	}

}