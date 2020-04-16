<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use MelisCore\Listener\MelisCoreGeneralListener;

class MelisCmsNewsFlashMessengerListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents      = $events->getSharedManager();
        $identifier = 'MelisCmsNews';
        $eventsName = [
            'meliscmsnews_delete_news_end',
        	'meliscmsnews_save_news_letter_end',
        	'meliscmsnews_save_news_file_end',
        	'meliscmsnews_delete_news_file_end',
        ];

        $priority = -1000;

        foreach ($eventsName As $event)
            $this->listeners[] = $sharedEvents->attach($identifier, $event, [$this, 'logMessages'], $priority);
    }

    /**
     * @param EventInterface $event
     */
    public function logMessages(EventInterface $event)
    {
        $sm = $event->getTarget()->getServiceManager();

        $flashMessenger = $sm->get('MelisCoreFlashMessenger');
        $params = $event->getParams();
        $results = $event->getTarget()->forward()->dispatch(
            \MelisCore\Controller\MelisFlashMessengerController::class,
            array_merge(array('action' => 'log'), $params))->getVariables();
    }
}