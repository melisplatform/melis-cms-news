<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;


use MelisCore\Listener\MelisCoreGeneralListener;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

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
                /**
                 * Add NEWS_DETAIL option under page type form element
                 *  'NEWS_DETAIL' => 'tr_meliscmsnews_preview_page_type'
                 */
                $appConfigForm = $e->getParam('appConfigForm');
                if (!empty($appConfigForm)) {
                    foreach ($appConfigForm['elements'] as $idx => $element) {
                        if ($element['spec']['name'] === 'page_type') {
                            /** @var \Zend\ServiceManager\ServiceLocatorInterface $sm */
                            $sm = $e->getTarget()->getServiceLocator();
                            $translator = $sm->get('translator');
                            $appConfigForm['elements'][$idx]['spec']['options']['value_options']['NEWS_DETAIL'] = $translator->translate('tr_meliscmsnews_preview_page_type');
                            $e->setParam('appConfigForm', $appConfigForm);

                            break;
                        }
                    }
                }

                return $e->getParam('appConfigForm');
            },
            200
        );

        $this->listeners[] = $callBackHandler;
    }
}