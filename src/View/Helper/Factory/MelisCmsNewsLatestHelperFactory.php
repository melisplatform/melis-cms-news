<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\View\Helper\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use MelisCmsNews\View\Helper\MelisCmsNewsLatestHelper;

class MelisCmsNewsLatestHelperFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
		$serviceLoc = $sl->getServiceLocator();
		$router = $serviceLoc->get('router');
		$request = $serviceLoc->get('request');
		$routeMatch = $router->match($request);
		
		if (!empty($routeMatch))
		{
		    $renderMode = $routeMatch->getParam('renderMode');
		    $preview = $routeMatch->getParam('preview');
		}
		else
		{
		    $renderMode = 'front';
		    $preview = false;
		}
		$helper = new MelisCmsNewsLatestHelper($serviceLoc, $renderMode, $preview);

		return $helper;
	}

}