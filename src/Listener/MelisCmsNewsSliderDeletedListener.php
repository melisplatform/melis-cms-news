<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;

use MelisCore\Listener\MelisGeneralListener;

class MelisCmsNewsSliderDeletedListener extends MelisGeneralListener
{
	public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->attachEventListener(
            $events,
			'MelisCmsSlider',
			'meliscmsslider_delete_slider_end',
			function($e){
				
				$sm = $e->getTarget()->getServiceManager();
				$params = $e->getParams();
				$paramData = array();
				if(isset($params['data'])) {
					$paramData = $params['data'];
				}

				$melisCoreDispatchService = $sm->get('MelisCoreDispatch');
				
				list($success, $errors, $datas) = $melisCoreDispatchService->dispatchPluginAction(
					$e,
					'meliscmsnews',
					'slider-deleted-data',
					'MelisCmsNews\Controller\MelisCmsNews',
					array('action' => 'newsSliderDeleted')
					);
			},
			100
		);
	}
}