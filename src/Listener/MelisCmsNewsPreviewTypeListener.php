<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;


use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use MelisCore\Listener\MelisCoreGeneralListener;

/**
 * This listener listens to MelisCmsNews events in order to add entries in the
 * flash messenger
 */
class MelisCmsNewsPreviewTypeListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{

    /**
     * Customizes the Page properties form config
     * - Adding a NEWS_DETAIL option under the page type select element
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();

        $callBackHandler = $sharedEvents->attach(
            '*',
            'modify_page_properties_form_config',
            function ($e) {
                $params = $e->getParams();

                /**
                 * Add NEWS_DETAIL option under page type form element
                 *  'NEWS_DETAIL' => 'tr_meliscmsnews_preview_page_type'
                 */
                if (!empty($params['appConfigForm'])) {
                    foreach ($params['appConfigForm']['elements'] as $index => $element) {
                        if ($element['spec']['name'] === 'page_type') {
                            /** @var \Zend\ServiceManager\ServiceLocatorInterface $sm */
                            $sm = $e->getTarget()->getServiceLocator();
                            $translator = $sm->get('translator');
                            $params['appConfigForm']['elements'][$index]['spec']['options']['value_options']['NEWS_DETAIL'] = $translator->translate('tr_meliscmsnews_preview_page_type');

                            break;
                        }
                    }
                }

                return $params['appConfigForm'];
            },
            100
        );

        $this->listeners[] = $callBackHandler;
    }
}