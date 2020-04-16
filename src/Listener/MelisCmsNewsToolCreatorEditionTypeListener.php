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
use MelisCore\Listener\MelisCoreGeneralListener;

class MelisCmsNewsToolCreatorEditionTypeListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents      = $events->getSharedManager();

        $this->listeners[] = $sharedEvents->attach(
            '*',
            'melis_toolcreator_input_edition_type_options',
            function ($e) {
                $sm = $e->getTarget()->getServiceManager();
                $params = $e->getParams();
                $params['valueOptions']['MelisCmsNewsBOSelect'] = $sm->get('translator')->translate('tr_meliscmsnews_news_title');
            }
        );
    }
}