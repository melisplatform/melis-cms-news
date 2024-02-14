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
use Psr\Container\ContainerInterface;
use Laminas\Form\Element\Select;

/**
 * MelisCms news select factory
 */
class MelisCmsNewsBOSelectFactory extends MelisSelectFactory
{

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return Select
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $element = new Select;
        $element->setValueOptions($this->loadValueOptions($container));
        $element->setEmptyOption($container->get('translator')->translate('tr_meliscore_common_choose'));
        return $element;
    }

    /**
     * @param ServiceManager $serviceManager
     * @return array
     */
	protected function loadValueOptions(ServiceManager $serviceManager)
	{
		$newsSvc        = $serviceManager->get('MelisCmsNewsService');
		$newsData       = [];
		
        // Get the locale used from meliscore session
        $container = new Container('meliscore');
        $langId = $container['melis-lang-id'];
		
		foreach($newsSvc->getNewsList() as $news){
            $newsData[$news['cnews_id']] = $news['cnews_id'] . ' - ' . $news['cnews_title'];
		}
		return $newsData;
	}

}