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
use MelisCore\Listener\MelisGeneralListener;

class MelisCmsNewsFlashMessengerListener extends MelisGeneralListener
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

        $this->attachEventListener($events, $identifier, $eventsName, [$this, 'logMessages'], $priority);
    }
}