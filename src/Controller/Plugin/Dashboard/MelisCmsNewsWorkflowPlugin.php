<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller\Plugin\Dashboard;

use MelisCore\Controller\DashboardPlugins\MelisCoreDashboardTemplatingPlugin;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container;

class MelisCmsNewsWorkflowPlugin extends MelisCoreDashboardTemplatingPlugin
{
    public function __construct()
    {
        $this->pluginModule = 'meliscmsnews';
        parent::__construct();
    }
    
    /**
     * Makes the rendering of the Workflow Dashboard
     * @return \Laminas\View\Model\ViewModel
     */
    public function workflow()
    {
        /** @var \MelisCore\Service\MelisCoreDashboardPluginsRightsService $dashboardPluginsService */
        $dashboardPluginsService = $this->getServiceManager()->get('MelisCoreDashboardPluginsService');
        //get the class name to make it as a key to the plugin
        $path = explode('\\', __CLASS__);
        $className = array_pop($path);

        $isAccessible = $dashboardPluginsService->canAccess($className);

        $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
        $user = $melisCoreAuth->getIdentity();
        
        $userId = null;
        $roleId = null;
        
        if(!empty($user)){
            $userId = $user->usr_id;
            $roleId = $user->usr_role_id;
        }
        
        $melisCmsNewsWorkflowTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowTable');
        
        // get demands asked to me
        $workflowItems = $melisCmsNewsWorkflowTable->getWorkflowDemandsToByUserIdAndRoleId($userId, $roleId);
        $tabItemsDemandsForMe = $this->orderWorkflowItemsForDashboard($workflowItems);
        
        // get demands i asked
        $workflowItems = $melisCmsNewsWorkflowTable->getWorkflowDemandsFromByUserIdAndRoleId($userId);
        $tabItemsDemandsToOthers = $this->orderWorkflowItemsForDashboard($workflowItems);
        
        /**
         * Send back the view and add the form config inside
         */
        $view = new ViewModel();
        $view->setTemplate('MelisCmsNews/dashboard-plugin/workflow');
        $view->tabItemsDemandsForMe = $tabItemsDemandsForMe;
        $view->tabItemsDemandsToOthers = $tabItemsDemandsToOthers;
        $view->isAccessable = $isAccessible;
        return $view;
    }
    
    /**
     * @param $workflowItems
     * @return array
     */
    private function orderWorkflowItemsForDashboard($workflowItems)
    {
        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];
        
        $translator = $this->getServiceManager()->get('translator');
        $melisTranslation = $this->getServiceManager()->get('MelisCoreTranslation');
        $melisUserTable = $this->getServiceManager()->get('MelisCoreTableUser');
        $melisUserRole = $this->getServiceManager()->get('MelisCmsNewsUserRole');
        $melisCmsNewsWorkflowTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowTable');
        $melisCmsNewsWorkflowEventsTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowEventsTable');
        
        // get demands asked to me
        $demands = array();
        $nbItems = 0;
        if (!empty($workflowItems)) {
            $workflowItems = $workflowItems->toArray();
            foreach ($workflowItems as $wf) {
                $wfEvents = $melisCmsNewsWorkflowEventsTable->getWorkflowEventsById($wf['cnews_wf_id']);
                
                // Limit to 8 in the dashboard (or more only if not validated)
                if ($nbItems >= $this->pluginConfig['datas']['limit']) {
                    /**
                     * if there's already more items than MELIS_WF_DASHBOARD_LIMIT
                     * then we'll add them only if there're not validated yet so that they don't disappear
                     * Items are ordered by wf_process_finished asc meaning the first to come will be the
                     * not finished, so we just have to check if it's finished and we cn stop because we're
                     * sure the next ones will be too
                     */
                    if ($wf['cnews_wf_process_finished'])
                        break;
                }
                
                if (empty($demands[$wf['cnews_wf_type']]))
                    $demands[$wf['cnews_wf_type']] = array();
                    
                if (empty($demands[$wf['cnews_wf_type']][$wf['cnews_wf_id']]))
                    $demands[$wf['cnews_wf_type']][$wf['cnews_wf_id']] = array();
                        
                $results = $wfEvents->toArray();
                $finalResult = array();
                foreach ($results as $wfItem) {                    
                    
                    $wfItem['cnews_wf_id'] = $wf['cnews_wf_id'];
                    $wfItem['cnews_wf_process_finished'] = $wf['cnews_wf_process_finished'];
                    $wfItem['cnews_wf_item_key_id'] = $wf['cnews_wf_item_key_id'];
                    $wfItem['cnews_wf_type'] = $wf['cnews_wf_type'];
                    $wfItem['cnews_wf_details'] = $wf['cnews_wf_details'];
                    $wfItem['cnews_wf_opening_js'] = $wf['cnews_wf_opening_js'];
                    
                    // Deleted user as Default value
                    $userTo = $translator->translate('tr_meliscore_user_deleted').' ('.$wfItem['cnews_wfe_to_user_id'].')';
                    $userDatas = $melisUserTable->getEntryById($wfItem['cnews_wfe_to_user_id']);
                    if (!empty($userDatas)) {
                        $userDatas = $userDatas->toArray();
                        if (!empty($userDatas))
                            $userTo = $userDatas[0]['usr_firstname'] . ' ' . $userDatas[0]['usr_lastname'];
                    }
                    
                    // Deleted user as Default value
                    $userFrom = $translator->translate('tr_meliscore_user_deleted').' ('.$wfItem['cnews_wfe_from_user_id'].')';
                    $userDatas = $melisUserTable->getEntryById($wfItem['cnews_wfe_from_user_id']);
                    if (!empty($userDatas)) {
                        $userDatas = $userDatas->toArray();
                        if (!empty($userDatas))
                            $userFrom = $userDatas[0]['usr_firstname'] . ' ' . $userDatas[0]['usr_lastname'];
                    }
                    
                    $roleName = '';
                    $roleDatas = $melisUserRole->getEntryById($wfItem['cnews_wfe_to_role_id']);
                    if (!empty($roleDatas)) {
                        $roleDatas = $roleDatas->toArray();
                        if (!empty($roleDatas))
                            $roleName = $roleDatas[0]['urole_name'];
                    }
                    
                    $date = strftime($melisTranslation->getDateFormatByLocate($locale), strtotime($wfItem['cnews_wfe_date']));
                    $date = substr($date, 0, strlen($date) - 3);
                    $wfItem['cnews_wfe_date'] = $date;
                    
                    $wfItem['cnews_wfe_action'] = $translator->translate($wfItem['cnews_wfe_action']);
                    $wfItem['cnews_wfe_to_user_name'] = $userTo;
                    $wfItem['cnews_wfe_to_role_name'] = $roleName;
                    $wfItem['cnews_wfe_from_user_name'] = $userFrom;
                    
                    array_push($finalResult, $wfItem);
                    
                }
                
                $demands[$wf['cnews_wf_type']][$wf['cnews_wf_id']] = $finalResult;
                $nbItems++;
            }
        }
        
        return $demands;
    }
    
    private function orderWorkflowItemsForHistoricModal($workflowItems, $type = 'demand')
    {
        $translator = $this->getServiceManager()->get('translator');
        $melisUserTable = $this->getServiceManager()->get('MelisCoreTableUser');
        $melisUserRole = $this->getServiceManager()->get('MelisCmsNewsUserRole');
        
        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];
        $melisTranslation = $this->getServiceManager()->get('MelisCoreTranslation');
        
        $tabItems = array();
        if (!empty($workflowItems)) {
            $workflowItems = $workflowItems->toArray();
            
            foreach ($workflowItems as $wfItem) {
                if (empty($tabItems[$wfItem['cnews_wf_type']]))
                    $tabItems[$wfItem['cnews_wf_type']] = array();
                    
                // Deleted user as Default value
                $userTo = $translator->translate('tr_meliscore_user_deleted').' ('.$wfItem['cnews_wfe_to_user_id'].')';
                $userDatas = $melisUserTable->getEntryById($wfItem['cnews_wfe_to_user_id']);
                
                if (!empty($userDatas)) {
                    $userDatas = $userDatas->toArray();
                    if (!empty($userDatas))
                        $userTo = $userDatas[0]['usr_firstname'] . ' ' . $userDatas[0]['usr_lastname'];
                }
                
                // Deleted user as Default value
                $userFrom = $translator->translate('tr_meliscore_user_deleted').' ('.$wfItem['cnews_wfe_from_user_id'].')';
                $userDatas = $melisUserTable->getEntryById($wfItem['wfe_from_user_id']);
                if (!empty($userDatas)) {
                    $userDatas = $userDatas->toArray();
                    if (!empty($userDatas))
                        $userFrom = $userDatas[0]['usr_firstname'] . ' ' . $userDatas[0]['usr_lastname'];
                }
                
                $roleName = '';
                $roleDatas = $melisUserRole->getEntryById($wfItem['cnews_wfe_to_role_id']);
                if (!empty($roleDatas)) {
                    $roleDatas = $roleDatas->toArray();
                    if (!empty($roleDatas))
                        $roleName = $roleDatas[0]['urole_name'];
                }
                
                $date = $lastPublishedDate = strftime($melisTranslation->getDateFormatByLocate($locale), strtotime($wfItem['cnews_wfe_date']));
                $date = substr($date, 0, strlen($date) - 3);
                $wfItem['cnews_wfe_date'] = $date;
                
                $wfItem['cnews_wfe_action'] = $translator->translate($wfItem['cnews_wfe_action']);
                $wfItem['cnews_wfe_to_user_name'] = $userTo;
                $wfItem['cnews_wfe_to_role_name'] = $roleName;
                $wfItem['cnews_wfe_from_user_name'] = $userFrom;
                
                $isAnswerExisting = $this->isAnswerAlreadyExisting($wfItem['cnews_wfe_wf_id']);
                $wfItem['isAnswerExisting'] = $isAnswerExisting;
                
                array_push($tabItems[$wfItem['cnews_wf_type']], $wfItem);
            }
        }
        
        return $tabItems;
    }
    
        
        

        
        
        
        
}