<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use MelisCore\Listener\MelisCoreGeneralListener;

class MelisCmsNewsFlashMessengerListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
	
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents      = $events->getSharedManager();
        
        $callBackHandler = $sharedEvents->attach(
        	'MelisCmsNews',
        	array(
        	    'meliscmsnews_delete_news_end',
        	    'meliscmsnews_save_news_letter_end',
        	    'meliscmsnews_save_news_file_end',
        	    'meliscmsnews_delete_news_file_end',
        	    
        	),
        	function($e){

        		$sm = $e->getTarget()->getServiceLocator();
        		
        		$flashMessenger = $sm->get('MelisCoreFlashMessenger');
        		$params = $e->getParams();
        		$results = $e->getTarget()->forward()->dispatch(
        		    'MelisCore\Controller\MelisFlashMessenger',
        		    array_merge(array('action' => 'log'), $params))->getVariables();

        	},
        -1000);
        
        $this->listeners[] = $callBackHandler;
    }
}