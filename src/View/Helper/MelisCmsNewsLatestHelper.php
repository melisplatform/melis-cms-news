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

/**
 * Creates a list of Latest News
 *
 */
class MelisCmsNewsLatestHelper extends AbstractHelper
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
	
	
	public function __invoke($newsListParameters)
	{
        $newsListPlugin = $this->serviceManager->get('ControllerPluginManager')->get('MelisCmsNewsLatestNewsPlugin');
	    $newsListPluginView = $newsListPlugin->render($newsListParameters);
	    
	    $viewRender = $this->serviceManager->get('ViewRenderer');
	    $newsListHtml = $viewRender->render($newsListPluginView);

		return $newsListHtml;
	}
}