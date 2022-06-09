<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use MelisCore\Listener\MelisGeneralListener;

class MelisCmsNewsWorkflowSaveDemandListener extends MelisGeneralListener implements ListenerAggregateInterface
{
	public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->attachEventListener(
            $events,
			'MelisCmsNews',
			'meliscmsnews_workflow_ask_start',
			function($e){

				$sm = $e->getTarget()->getServiceManager();
				$melisCoreDispatchService = $sm->get('MelisCoreDispatch');

				$params = $e->getParams();
				list($success, $errors, $datas) = $melisCoreDispatchService->dispatchPluginAction(
					$e,
					'meliscmsnews',
					'action-workflow',
					'MelisCmsNews\Controller\MelisCmsNewsWorkflow',
					array_merge(array('action' => 'workflowActionAsk'), $params)
				);
				
			},
			100
		);
	}
}