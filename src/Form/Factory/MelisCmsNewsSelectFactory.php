<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Form\Factory;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\Container;
use MelisCore\Form\Factory\MelisSelectFactory;

/**
 * MelisCms news select factory
 */
class MelisCmsNewsSelectFactory extends MelisSelectFactory
{
	protected function loadValueOptions(ServiceManager $serviceManager)
	{
		$sliders = array();
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