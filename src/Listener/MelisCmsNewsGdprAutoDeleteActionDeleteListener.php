<?php
/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2018 Melis Technology (http://www.melistechnology.com)
 *
 */
namespace MelisCmsNews\Listener;

use MelisCore\Listener\MelisCoreGeneralListener;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class MelisCmsNewsGdprAutoDeleteActionDeleteListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();

        $callBackHandler = $sharedEvents->attach(
            '*',
            'melis_cms_user_account_gdpr_auto_delete_action_delete',
            function ($e) {
                $userData = $e->getParam('user_data');
                $data = [
                    'cnews_author_account' => null
                ];
                // update the table info
                $e->getTarget()->getServiceLocator()->get('MelisCmsNewsTable')->update($data, 'cnews_author_account', $userData->uac_id);
                // trigger event for other modules
                $e->getTarget()->getEventManager()->trigger('melis_cms_news_gdpr_auto_delete_action_delete', $e->getTarget(), ['user_data', $userData]);
            },
            -1000);

        $this->listeners[] = $callBackHandler;
    }
}