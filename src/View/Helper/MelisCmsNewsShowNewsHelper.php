<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/** Creates News Details */
class MelisCmsNewsShowNewsHelper extends AbstractHelper
{
    public $serviceManager;
    public $renderMode;
    public $preview;

    public function __construct($sm, $renderMode, $preview)
    {
        $this->serviceManager = $sm;
        $this->renderMode = $renderMode;
        $this->preview = $preview;
    }


    public function __invoke($newsParameters)
    {
        $newsPlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisCmsNewsShowNewsPlugin');
        $newsPluginView = $newsPlugin->render($newsParameters);

        $viewRender = $this->serviceManager->get('ViewRenderer');
        $newsHtml = $viewRender->render($newsPluginView);

        return $newsHtml;
    }
}