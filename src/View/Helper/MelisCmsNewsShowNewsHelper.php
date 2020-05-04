<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

/** Creates News Details */
class MelisCmsNewsShowNewsHelper extends AbstractHelper
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
     * @param $newsParameters
     * @return mixed
     */
    public function __invoke($newsParameters)
    {
        $newsPlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisCmsNewsShowNewsPlugin');
        $newsPluginView = $newsPlugin->render($newsParameters);

        $viewRender = $this->serviceManager->get('ViewRenderer');
        $newsHtml = $viewRender->render($newsPluginView);

        return $newsHtml;
    }
}