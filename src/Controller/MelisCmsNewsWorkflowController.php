<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller;

use MelisCore\Controller\MelisAbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Laminas\Session\Container;

/**
 * This class renders Melis CMS Page tab properties
 */
class MelisCmsNewsWorkflowController extends MelisAbstractActionController
{

    /*
    * retrieves the workflow actions for the given news
    */
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


    /**
     * Renders the workflowmodal
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderWorkflowModalAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
 
        // Get the form properly loaded               
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');  
        $commentFormConfig = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_comment_form', 'meliscmsnews_comment_form');
        $commentNewsForm = $factory->createForm($commentFormConfig);

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->melisKey = $melisKey;
        $view->setVariable('commentNewsForm', $commentNewsForm);

        return $view;
    }

    public function renderWorkflowModalContentAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');

        $view = new ViewModel();
        $view->melisKey = $melisKey;

        return $view;
    }

    public function renderWorkflowModalContentActionsAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');

        $view = new ViewModel();
        $view->melisKey = $melisKey;

        return $view;
    }

    public function renderWorkflowModalContentActionsAskAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $type = $this->params()->fromRoute('wfType', $this->params()->fromQuery('wfType', ''));
        $idItem = $this->params()->fromRoute('wfId', $this->params()->fromQuery('wfId', ''));

        $isDemandExisting = false;//$this->isDemandAlreadyExisting($type, $idItem);

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->isDemandExisting = $isDemandExisting;

        return $view;
    }

    public function renderWorkflowModalContentActionsValidateAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $type = $this->params()->fromRoute('wfType', $this->params()->fromQuery('wfType', ''));
        $idItem = $this->params()->fromRoute('wfId', $this->params()->fromQuery('wfId', ''));

        $isAnswerExisting = false;

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->isAnswerExisting = $isAnswerExisting;

        return $view;
    }

    public function renderWorkflowModalContentActionsRefuseAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $type = $this->params()->fromRoute('wfType', $this->params()->fromQuery('wfType', ''));
        $idItem = $this->params()->fromRoute('wfId', $this->params()->fromQuery('wfId', ''));

        $isAnswerExisting = false;

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->isAnswerExisting = $isAnswerExisting;

        return $view;
    }

    public function renderWorkflowModalContentActionsZoneAskAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');

        $translator = $this->getServiceManager()->get('translator');
        $rolesTable = $this->getServiceManager()->get('MelisCmsNewsUserRole');
        $roles = $rolesTable->fetchAll();
        if (!empty($roles))
            $roles = $roles->toArray();
        else
            $roles = array();

        $finalResults = array(
            array('urole_id' => -1, 'urole_name' => $translator->translate('tr_meliscmsnews_workflow_All roles'))
        );
        foreach ($roles as $role) {
            if ($role['urole_id'] != 1) // Custom role is not a choice
                array_push($finalResults, $role);
        }

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->roles = $finalResults;

        return $view;
    }


    public function getUserListPerRoleAction()
    {
        $roleId = $this->params()->fromRoute('roleId', $this->params()->fromQuery('roleId', ''));

        $usersTable = $this->getServiceManager()->get('MelisCoreTableUser');

        if ($roleId == -1)
            $users = $usersTable->fetchAll();
        else
            $users = $usersTable->getUsersByRole($roleId);

        if (!empty($users)) {
            $users = $users->toArray();
            $results = array();
            foreach ($users as $user)
                $results[] = array('usr_id' => $user['usr_id'], 'usr_fullname' => $user['usr_firstname'] . ' ' . $user['usr_lastname']);
            $users = $results;
        }
        else
            $users = array();

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->users = $users;

        return $view;
    }

    public function renderWorkflowModalContentHistoricAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $type = $this->params()->fromRoute('wfType', $this->params()->fromQuery('wfType', ''));
        $idItem = $this->params()->fromRoute('wfId', $this->params()->fromQuery('wfId', ''));
        $details = $this->params()->fromRoute('wfDetails', $this->params()->fromQuery('wfDetails', ''));
        $openingJs = $this->params()->fromRoute('wfOpeningJs', $this->params()->fromQuery('wfOpeningJs', ''));

        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];
        $melisTranslation = $this->getServiceManager()->get('MelisCoreTranslation');

        $melisCmsNewsWorkflowTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowTable');
        $workflowItems = $melisCmsNewsWorkflowTable->getWorkflowByTypeAndId($type, $idItem);

        $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
        $userAuthDatas =  $melisCoreAuth->getStorage()->read();
        $currentUserId = (int) $userAuthDatas->usr_id;

        if (!empty($workflowItems)) {
            $workflowItems = $this->orderWorkflowItemsForHistoricModal($workflowItems);

            if (!empty($workflowItems[$type]))
                $workflowItems = $workflowItems[$type];
            else
                $workflowItems = array();
        } else {
            $workflowItems = array();
        }

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->workflowItems = $workflowItems;
        $view->currentUserId = $currentUserId;

        return $view;
    }

    public function saveWorkflowAskAction()
    {
        $roleId = $this->params()->fromRoute('roles', $this->params()->fromQuery('roles', ''));
        $userId = $this->params()->fromRoute('user', $this->params()->fromQuery('user', ''));
        $type = $this->params()->fromRoute('wfType', $this->params()->fromQuery('wfType', ''));
        $idItem = $this->params()->fromRoute('wfId', $this->params()->fromQuery('wfId', ''));
        $details = $this->params()->fromRoute('wfDetails', $this->params()->fromQuery('wfDetails', ''));
        $openingJs = $this->params()->fromRoute('wfOpeningJs', $this->params()->fromQuery('wfOpeningJs', ''));
        $translator = $this->getServiceManager()->get('translator');
        $datas = array(
            'roleId' => $roleId,
            'userId' => $userId,
            'type' => $type,
            'idItem' => $idItem,
            'details' => $details,
            'openingJs' => $openingJs,
        );

        // Get the MelisCms Module session as page is saved in it
        $container = new Container('meliscmsnews');

        $this->getEventManager()->trigger('meliscmsnews_workflow_ask_start', $this, $datas);

        $workFlowId = null;
        $success = 0;
        $errors = array();
        $datas = array();

        // Update from the different save actions done
        if (!empty($container['action-workflow'])) {
            if (!empty($container['action-workflow']['success']))
                $success = $container['action-workflow']['success'];
            if (!empty($container['action-workflow']['errors']))
                $errors = $container['action-workflow']['errors'];
            if (!empty($container['action-workflow']['datas']))
                $datas = $container['action-workflow']['datas'];
        }
        
        if (!empty($datas['workFlowId'])){
            $workFlowId = $datas['workFlowId'];
        }
        
        unset($container['action-workflow']);

        if ($success == 1)
            $textMessage = 'tr_meliscmsnews_Workflow demand sent';
        else
            $textMessage = 'tr_meliscmsnews_Workflow An error occured while saving your workflow action';

        $response = array(
            'success' => $success,
            'textTitle' => 'tr_meliscmsnews_Workflow Validation Demand',
            'textMessage' => $textMessage,
            'errors' => $errors,
            'datas' => $datas,
        );

        $this->getEventManager()->trigger('meliscmsnews_workflow_ask_end', $this, array_merge($response, array('typeCode' => 'NEWS_WORKFLOW_DEMAND', 'itemId' => $workFlowId)));

        // Final Json sent back
        return new JsonModel($response);
    }

    public function workflowActionAskAction()
    {
        $translator = $this->getServiceManager()->get('translator');

        $roleId = $this->params()->fromRoute('roles', $this->params()->fromQuery('roles', ''));
        $userId = $this->params()->fromRoute('user', $this->params()->fromQuery('user', ''));
        $type = $this->params()->fromRoute('wfType', $this->params()->fromQuery('wfType', ''));
        $idItem = $this->params()->fromRoute('wfId', $this->params()->fromQuery('wfId', ''));
        $details = $this->params()->fromRoute('wfDetails', $this->params()->fromQuery('wfDetails', ''));
        $openingJs = $this->params()->fromRoute('wfOpeningJs', $this->params()->fromQuery('wfOpeningJs', ''));
        $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
        $user = $melisCoreAuth->getIdentity();
        $fromUserId = $user->usr_id;

        $errors = array();
        if (empty($roleId))
            $errors['roleId'] = array(
                'empty' => $translator->translate('tr_meliscmsnews_workflow_error_A role must be choosen'),
                'label' => $translator->translate('tr_meliscmsnews_workflow_user_role')
            );
        if (empty($userId))
            $errors['userId'] = array(
                'empty' => $translator->translate('tr_meliscmsnews_workflow_error_A user must be choosen'),
                'label' => $translator->translate('tr_meliscmsnews_workflow_user')
            );
        if (empty($type) || empty($idItem) || empty($details) || empty($openingJs))
            $errors['datastech'] = array(
                'empty' => $translator->translate('tr_meliscmsnews_workflow_error_Technical datas not properly set'),
                'label' => $translator->translate('tr_meliscmsnews_workflow_Technical Datas')
            );

        if (!empty($errors))  {
            $result = array(
                'success' => 0,
                'errors' => array($errors),
            );
        } else {
            $isDemandExisting = false;//$this->isDemandAlreadyExisting($type, $idItem);

            if ($isDemandExisting) {
                $errors['datastech'] = array(
                    'empty' => $translator->translate('tr_meliscmsnews_workflow_error_Validation demand already existing'),
                    'label' => $translator->translate('tr_meliscmsnews_dashboard_Workflow')
                );

                $result = array(
                    'success' => 0,
                    'errors' => array($errors),
                );
            } else {
                $melisCmsNewsWorkflowTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowTable');
                $melisCmsNewsWorkflowEventsTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowEventsTable');

                $dataWf = array(
                    'cnews_wf_process_finished' => 0,
                    'cnews_wf_item_key_id' => $idItem,
                    'cnews_wf_type' => $type,
                    'cnews_wf_details' => $details,
                    'cnews_wf_opening_js' => $openingJs,
                    'cnews_wf_date' => date('Y-m-d H:i:s'),
                );

                $idWorkflowTmp = $melisCmsNewsWorkflowTable->save($dataWf);

                if ($roleId == -1)
                    $roleId = null;

                $dataWfEvents = array(
                    'cnews_wfe_action' => 'VALIDATION',
                    'cnews_wfe_wf_id' => $idWorkflowTmp,
                    'cnews_wfe_from_user_id' => $fromUserId,
                    'cnews_wfe_to_user_id' => $userId,
                    'cnews_wfe_to_role_id' => $roleId,
                    'cnews_wfe_date' => date('Y-m-d H:i:s'),
                );

                $idWorkflowEventsTmp = $melisCmsNewsWorkflowEventsTable->save($dataWfEvents);

                // WORKFLOW EMAILING
                // Fetching User Data By Id
                $userTable = $this->getServiceManager()->get('MelisCoreTableUser');
                $userToData = $userTable->getEntryById($userId);
                $userToData = $userToData->current();

                // Fetching Current User Data
                $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
                $userAuthDatas =  $melisCoreAuth->getStorage()->read();
                $currentUserId = (int) $userAuthDatas->usr_id;
                $userFromData = $userTable->getEntryById($currentUserId);
                $userFromData = $userFromData->current();

                // Tags to be replace at email content with the corresponding value
                $tags = array(
                    'USER_TO' => $userToData->usr_firstname.' '.$userToData->usr_lastname,
                    'USER_FROM' => $userFromData->usr_firstname.' '.$userFromData->usr_lastname,
                    'TYPE' => $type,
                    'DETAILS' => $details,
                );

                $name_to = $tags['USER_TO'];
                $email_to = $userToData->usr_email;
                $langId = $userToData->usr_lang_id;

                $melisEmailBO = $this->getServiceManager()->get('MelisCoreBOEmailService');
                $melisEmailBO->sendBoEmailByCode('WF_DEMAND',  $tags, $email_to, $name_to, $langId);

                $result = array(
                    'success' => 1,
                    'datas' => array(
                        'workFlowId' => $idWorkflowTmp
                    ),
                    'errors' => array(),
                );
            }
        }

        return new JsonModel($result);
    }

    public function isDemandAlreadyExisting($type, $idItem)
    {
        $melisCmsNewsWorkflowTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowTable');
       
        $wfMainId = 0;
        $datasWF = $melisCmsNewsWorkflowTable->getUnvalidatedWorkflowByTypeAndId($type, $idItem, 1);
        if (!empty($datasWF)) {
            $datasWF = $datasWF->toArray();
            print_r($datasWF);
            if (!empty($datasWF)) {
                if ($datasWF[0]['cnews_wfe_action'] == 'VALIDATION')
                    $wfMainId = $datasWF[0]['cnews_wf_id'];
            }
        } else {
            $datasWF = array();
        }

        if (!empty($wfMainId))
            return true;
        else
            return false;
    }

    public function isAnswerAlreadyExisting($wfEventId)
    {
        $melisCmsNewsWorkflowEventsTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowEventsTable');

        $wfeData = $melisCmsNewsWorkflowEventsTable->getEntryByField('cnews_wfe_wf_id', (int) $wfEventId)->toArray();

        if ($wfeData) {
            $wfeDataCount = count($wfeData);
            if ($wfeDataCount === 2) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    public function saveWorkflowActionsAction()
    {
        $wfaction = $this->params()->fromRoute('wfaction', $this->params()->fromQuery('wfaction', ''));
        $type = $this->params()->fromRoute('type', $this->params()->fromQuery('type', ''));
        $idItem = $this->params()->fromRoute('idItem', $this->params()->fromQuery('idItem', ''));
        $details = $this->params()->fromRoute('details', $this->params()->fromQuery('details', ''));
        $openingJs = $this->params()->fromRoute('openingJs', $this->params()->fromQuery('openingJs', ''));
        $datas = array(
            'wfaction' => $wfaction,
            'type' => $type,
            'idItem' => $idItem,
            'details' => $details,
            'openingJs' => $openingJs,
        );

        // Get the MelisCms Module session as page is saved in it
        $container = new Container('meliscmsnews');

        $this->getEventManager()->trigger('meliscmsnews_workflow_actions_start', $this, $datas);

        $translator = $this->getServiceManager()->get('translator');        
        $success = 0;
        $errors = array();
        $datas = array();

        // Update from the different save actions done
        if (!empty($container['action-workflow'])) {
            if (!empty($container['action-workflow']['success']))
                $success = $container['action-workflow']['success'];
            if (!empty($container['action-workflow']['errors']))
                $errors = $container['action-workflow']['errors'];
            if (!empty($container['action-workflow']['datas']))
                $datas = $container['action-workflow']['datas'];
        }
        
        $wfEventId = null;
        if (!empty($datas['wfEventId'])) {
            $wfEventId = $datas['wfEventId'];
        }
        
        $logTypeCode = '';
        if (!empty($datas['wfAction'])){
            $logTypeCode = 'NEWS_WORKFLOW_'.$datas['wfAction'];
        }
        unset($container['action-workflow']);

        if ($success == 1)
            $textMessage = 'tr_meliscmsnews_Workflow answer sent';
        else
            $textMessage = 'tr_meliscmsnews_Workflow An error occured while saving your workflow action';

        $response = array(
            'success' => $success,
            'textTitle' => 'tr_meliscmsnews_Workflow Validation Answer',
            'textMessage' => $textMessage,
            'errors' => $errors,
            'datas' => $datas,
        );

        $this->getEventManager()->trigger('meliscmsnews_workflow_actions_end', $this, array_merge($response, array('typeCode' => $logTypeCode, 'itemId' => $wfEventId)));

        // Final Json sent back
        return new JsonModel($response);
    }

    public function workflowActionRefuseValidateAction()
    {
        $translator = $this->getServiceManager()->get('translator');
        $wfaction = $this->params()->fromRoute('wfaction', $this->params()->fromQuery('wfaction', ''));
        $type = $this->params()->fromRoute('wfType', $this->params()->fromQuery('wfType', ''));
        $idItem = $this->params()->fromRoute('wfId', $this->params()->fromQuery('wfId', ''));
        $details = $this->params()->fromRoute('wfDetails', $this->params()->fromQuery('wfDetails', ''));
        $openingJs = $this->params()->fromRoute('wfOpeningJs', $this->params()->fromQuery('wfOpeningJs', ''));
        $wfEventId = $this->params()->fromQuery('wfe-id', '');
        $wfMainId  = $this->params()->fromQuery('wfe_wf_id', '');
        $wfMainId = $wfMainId ?? 0;
        $toUserId = 0;
        $errors = array();

        if (empty($wfaction))
            $errors['wfaction'] = array(
                'empty' => $translator->translate('tr_meliscmsnews_workflow_error_An action must be choosen'),
                'label' => $translator->translate('tr_meliscmsnews_workflow_validate') . '/' . $translator->translate('tr_meliscmsnews_workflow_refuse')
            );
        if (empty($type) || empty($idItem) || empty($details) || empty($openingJs))
            $errors['datastech'] = array(
                'empty' => $translator->translate('tr_meliscmsnews_workflow_error_Technical datas not properly set'),
                'label' => $translator->translate('tr_meliscmsnews_workflow_Technical Datas')
            );

        if (!empty($errors)) {
            $result = array(
                'success' => 0,
                'errors' => array($errors),
            );
        } else {
            $melisCmsNewsWorkflowTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowTable');
            $melisCmsNewsWorkflowEventsTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowEventsTable');
            $melisCmsNewsWorkflowEventsData  = $melisCmsNewsWorkflowEventsTable->getEntryById((int) $wfEventId)->toArray();

            if($melisCmsNewsWorkflowEventsData) {
                $melisCmsNewsWorkflowEventsData = $melisCmsNewsWorkflowEventsData[0];
                $wfMainId = $melisCmsNewsWorkflowEventsData['cnews_wfe_wf_id'];
                $toUserId = $melisCmsNewsWorkflowEventsData['cnews_wfe_from_user_id'];
            }

            if ($wfaction == 'validate') {
                $actionText = 'VALIDATED';
            } else {
                if ($wfaction == 'refuse') {
                    $actionText = 'REFUSED';
                } else {
                    $errors['datastech'] = array(
                        'empty' => $translator->translate('tr_meliscmsnews_workflow_error_Action not recognized'),
                        'label' => $translator->translate('tr_meliscmsnews_workflow_error_Workflow Action')
                    );

                    $result = array(
                        'success' => 0,
                        'errors' => array($errors),
                    );
                }
            }
                

            if ($actionText != '') {
                $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
                $user = $melisCoreAuth->getIdentity();
                $fromUserId = $user->usr_id;

                $dataWfEvents = array(
                    'cnews_wfe_action' => $actionText,
                    'cnews_wfe_wf_id' => $wfMainId,
					'cnews_wfe_from_user_id' => $fromUserId,
					'cnews_wfe_to_user_id' => $toUserId,
                    'cnews_wfe_to_role_id' => null,
                    'cnews_wfe_date' => date('Y-m-d H:i:s'),
                );

                $melisCmsNewsWorkflowEventsTable->save($dataWfEvents);

                $processFinish = $this->isProcessFinished((int) $wfMainId);
                // Updating process as finished in wfprocessFinish
                $dataWf = array(
                    'cnews_wf_process_finished' => $processFinish
                );
                $melisCmsNewsWorkflowTable->save($dataWf, $wfMainId);


                // WORKFLOW EMAILING
                // Fetching User Data By Id
                $userTable = $this->getServiceManager()->get('MelisCoreTableUser');
                $userToData = $userTable->getEntryById($toUserId);
                $userToData = $userToData->current();

                // Fetching Current User Data
                $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
                $userAuthDatas =  $melisCoreAuth->getStorage()->read();
                $currentUserId = (int) $userAuthDatas->usr_id;
                $userFromData = $userTable->getEntryById($fromUserId);
                $userFromData = $userFromData->current();

                // Tags to be replace at email content with the corresponding value
                $melisWorkflowData = $melisCmsNewsWorkflowTable->getEntryById($wfMainId)->current();
                $tags = array(
                    'USER_TO' => $userToData->usr_firstname.' '.$userToData->usr_lastname,
                    'USER_FROM' => $userFromData->usr_firstname.' '.$userFromData->usr_lastname,
                    'TYPE' => $melisWorkflowData->cnews_wf_type,
                    'DETAILS' => $melisWorkflowData->cnews_wf_details,
                );

                $name_to = $tags['USER_TO'];
                $email_to = $userToData->usr_email;
                $langId = $userToData->usr_lang_id;

                $melisEmailBO = $this->getServiceManager()->get('MelisCoreBOEmailService');
                if ($actionText=='VALIDATED') {
                    $melisEmailBO->sendBoEmailByCode('WF_VALIDATED',  $tags, $email_to, $name_to, $langId);
                } elseif ($actionText=='REFUSED') {
                    $melisEmailBO->sendBoEmailByCode('WF_REFUSED',  $tags, $email_to, $name_to, $langId);
                }

                $result = array(
                    'success' => 1,
                    'datas' => array(
                        'wfEventId' => $wfEventId,
                        'wfAction' => $actionText
                    ),
                    'errors' => array(),
                );
            }

        }

        return new JsonModel($result);
    }

    public function addWorkflowCommentsAction()
    {
        $translator = $this->getServiceManager()->get('translator');
        $success = 0;
        $errors  = array();
        $textTitle = $translator->translate('tr_meliscmsnews_comments_content');
        $textMessage = $translator->translate('tr_meliscmsnews_comments_modal_content');

        if ($this->getRequest()->isPost()) {

            $commentForm = $this->getCommentNewsForm();
            $postValues = $this->getRequest()->getPost()->toArray();
            $commentForm->setData($postValues);

            if ($commentForm->isValid()) {
                $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
                $user = $melisCoreAuth->getIdentity();
                $sName = $user->usr_firstname . ' ' . $user->usr_lastname;
                $commentTitle = sprintf($translator->translate('tr_meliscmsnews_comments_title_fixed_title_wf_'.$postValues['action']), $sName);
                unset($postValues['action']);
                $newsCommentService = $this->getServiceManager()->get('MelisCmsNewsCommentService');
                $success = (int) $newsCommentService->setNewsComments(array_merge($postValues, array('cnews_com_title' => $commentTitle)), 2);
                $textMessage = $translator->translate('tr_meliscmsnews_comments_content_ok');
            } else {
                $errors = $commentForm->getMessages();
            }

            foreach ($errors as $keyError => $valueError) {
                foreach ($appConfigForm['elements'] as $keyForm => $valueForm) {
                    if ($valueForm['spec']['name'] == $keyError &&
                        !empty($valueForm['spec']['options']['label']))
                        $errors[$keyError]['label'] = $valueForm['spec']['options']['label'];
                }
            }
        }

        return new JsonModel(array(
            'success' => $success,
            'errors' => $errors,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage
        ));
    }

    /*
    * retrieves the news comment form
    */
    private function getCommentNewsForm() 
    {
        // Get the form properly loaded               
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');  
        $commentFormConfig = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_comment_form', 'meliscmsnews_comment_form');
        $commentNewsForm = $factory->createForm($commentFormConfig);

        return $commentNewsForm;
    }

    /*
    *  if the demand has been validated or refused, the process is set to finished
    */
    public function isProcessFinished($wfId)
    {
        $workflowEventTable = $this->getServiceManager()->get('MelisCmsNewsWorkflowEventsTable');
        $data = $workflowEventTable->getEntryByField('cnews_wfe_wf_id', $wfId);
        $isProcessFinished = false;

        foreach ($data as $wfData) {
            if ($wfData->cnews_wfe_action == 'VALIDATED' || $wfData->cnews_wfe_action == 'REFUSED') {
                $isProcessFinished = true;
                break;
            }
        }

        return (int) $isProcessFinished;
    }

    /*used in the dashboard plugin*/
    public function workflowCommentModalAction()
    {
        $melisKey = $this->getRequest()->getQuery('melisKey');
        $melisId = $this->getRequest()->getQuery('id');

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->melisId = $melisId;
        $view->setTerminal(true);
        return $view;
    }

    public function workflowCommentModalContentAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = $this->getRequest()->getQuery('cnews_com_news_id');
        $action = $this->getRequest()->getQuery('action');
        $pluginId = $this->getRequest()->getQuery('pluginId');

        // comment form
        $commentForm = $this->getCommentNewsForm();

        // Changing the form Attribute id
        $commentForm->setAttribute('id', 'news-db-wf-comment-modal-'.$pluginId);

        $commentForm->add(array(
            'type' => 'hidden',
            'name' => 'cnews_com_news_id',
        ));

        $commentForm->add(array(
            'type' => 'hidden',
            'name' => 'action',
        ));

        $commentForm->remove("cnews_com_title");

        $data = array(array("cnews_com_news_id" => $newsId));

        isset($data) ? $commentForm->setData($data) : array();

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->action = $action;
        $view->pluginId = $pluginId;
        $view->setVariable('commentForm', $commentForm);
        return $view;
    }
}
