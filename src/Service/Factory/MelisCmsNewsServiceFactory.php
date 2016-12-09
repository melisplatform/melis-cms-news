<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Service\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use MelisCmsNews\Service\MelisCmsNewsService;

class MelisCmsNewsServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{ 
	    $MelisCmsNewsService = new MelisCmsNewsService();
	    $MelisCmsNewsService->setServiceLocator($sl);
	    return $MelisCmsNewsService;
	}

}