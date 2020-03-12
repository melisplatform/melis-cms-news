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
use Zend\Form\Element\Select;

/**
 * MelisCms news select factory
 */
class MelisCmsNewsBOSelectFactory extends MelisSelectFactory
{

    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceManager = $formElementManager->getServiceLocator();

        $element = new Select;
        $element->setValueOptions($this->loadValueOptions($formElementManager));
        $element->setEmptyOption($serviceManager->get('translator')->translate('tr_meliscore_common_choose'));
        return $element;
    }

	protected function loadValueOptions(ServiceLocatorInterface $formElementManager)
	{
	    $serviceManager = $formElementManager->getServiceLocator();
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