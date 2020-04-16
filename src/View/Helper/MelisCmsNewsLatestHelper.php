<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * Creates a list of Latest News
 *
 */
class MelisCmsNewsLatestHelper extends AbstractHelper
{
    public $serviceManager;

    /**
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param $newsListParameters
     * @return mixed
     */
	public function __invoke($newsListParameters)
	{
        $newsListPlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisCmsNewsLatestNewsPlugin');
	    $newsListPluginView = $newsListPlugin->render($newsListParameters);
	    
	    $viewRender = $this->serviceManager->get('ViewRenderer');
	    $newsListHtml = $viewRender->render($newsListPluginView);

		return $newsListHtml;
	}
}