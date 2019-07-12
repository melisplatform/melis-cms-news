<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use MelisCore\Listener\MelisCoreGeneralListener;

class MelisCmsNewsTableColumnDisplayListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents      = $events->getSharedManager();

        $this->listeners[] = $sharedEvents->attach(
            '*',
            'melis_toolcreator_col_display_options',
            function ($e) {

                $sm = $e->getTarget()->getServiceLocator();
                $params = $e->getParams();
                $params['valueOptions']['news_title'] = $sm->get('translator')->translate('tr_meliscmsnews_news_title');
            }
        );

        $this->listeners[] = $sharedEvents->attach(
            '*',
            'melis_tool_column_display_news_title',
            function($e){

                $sm = $e->getTarget()->getServiceLocator();
                $params = $e->getParams();

                $params['data'] = '<span class="text-'.($params['data'] ? 'success' : 'danger').'"><i class="fa fa-fw fa-circle"></i></span>';
            }
        );
    }
}