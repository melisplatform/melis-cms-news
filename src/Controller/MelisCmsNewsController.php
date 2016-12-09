<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Validator\File\Size;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Upload;
use Zend\File\Transfer\Adapter\Http;

class MelisCmsNewsController extends AbstractActionController
{
    /**
     * renders the page container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsPageAction()
    {
    
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the page header container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsHeaderAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the page header left container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsHeaderLeftAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the page header right container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsHeaderRightAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the tabs content header add button
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsHeaderRightSaveAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the page header title
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsHeaderTitleAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $newsName = '';
        
        if(!empty($this->layout()->news)){
            $newsName = ' - '.$this->layout()->news->cnews_title;
        }
        
        $view->newsName = $newsName;
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the page content container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsPageContentAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the page main content container
     * @return \Zend\View\Model\ViewModel render-coupon-page-tab-main
     */
    public function renderNewsPageTabsMainAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the tabs content header container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsContentHeaderAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the tabs content header left container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsContentHeaderLeftAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the tabs content header status
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsContentHeaderStatusAction()
    {
        $status = '';
        if(!empty($this->layout()->news)){
            if($this->layout()->news->cnews_status){
                $status = 'checked';
            }
        }
        
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->status = $status;
        return $view;
    }
    
    /**
     * renders the tabs content header title
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsContentHeaderTitleAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
 
    /**
     * renders the tabs content details container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsContentDetailsAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the tabs content details main content
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsMainAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the tab left content container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the tab right content container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsRightAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    
    /**
     * renders the tab left content properties
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftPropertiesAction()
    {   
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_properties_form','meliscmsnews_properties_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);        
        $form = $factory->createForm($appConfigForm);
        
        if(!empty($this->layout()->news)){
            $form->setData((array)$this->layout()->news );
        }
        
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->form = $form;
        return $view;
    }
    
    /**
     * renders the tab left content documents
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftDocumentsAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);
        
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $parConf = $melisCoreConfig->getItem('meliscmsnews/conf/documents_conf/');
        $limit = $parConf['max'];
        $name = $parConf['name'];
        $documents = array();
        
        if(!empty($this->layout()->news)){
            for($c = 1; $c <= $limit; $c++){
                $tmp = $name.$c;
                if($this->layout()->news->$tmp){
                    $i = array(
                        'cnews_id' => $this->layout()->news->cnews_id,
                        'documents' => $this->layout()->news->$tmp,
                        'column' => $tmp,
                    );
                    $documents[] = $i;
                }                
            }
        }
        
        $view = new ViewModel();        
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->documents = $documents;
        $view->limit = $limit;
        return $view;
    }
    
    /**
     * renders the tab left content documents
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftImagesAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);
    
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $parConf = $melisCoreConfig->getItem('meliscmsnews/conf/images_conf/');
        $limit = $parConf['max'];
        $name = $parConf['name'];
        $images = array();        
    
        if(!empty($this->layout()->news)){
            for($c = 1; $c <= $limit; $c++){
                $tmp = $name.$c;
                if($this->layout()->news->$tmp){
                    $i = array(
                        'cnews_id' => $this->layout()->news->cnews_id,
                        'image' => $this->layout()->news->$tmp,
                        'column' => $tmp,
                    );
                    $images[] = $i;
                }
            }
        }
    
        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->images = $images;
        $view->limit  = $limit;
        return $view;
    }
    
    /**
     * renders the tab right content paragraphs
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsRightParagraphsAction()
    {
        $newsId = (int) $this->params()->fromQuery('newsId', '');       
        
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_paragraph_form','meliscmsnews_paragraph_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);
        
        // paragraph config in interface
        $parConf = $melisCoreConfig->getItem('meliscmsnews/conf/paragraphs_conf/');
        $limit = $parConf['max'];
        $name = $parConf['name'];
        $forms = array();
        
        if(!empty($this->layout()->news)){
            for($c = 1; $c <= $limit; $c++){
                $tmpForm = clone($form);
                $tmp = $name.$c;
                $tmpForm->get('paragraph')->setAttribute('id',$newsId.'_cnews_paragraph'.$c);
                $i = array(
                    'paragraph' => $this->layout()->news->$tmp,
                    'column' => $tmp,
                );
                $forms[] = $tmpForm->setData($i);
                
            }
        }
        
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->forms = $forms;
        return $view;
    }
    
    /**
     * renders the modal container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderModalAction()
    {
        $id = $this->params()->fromQuery('id');
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        $view->id = $id;
        $view->setTerminal(false);
        return $view;
    }
    
    /**
     * renders the order list modal for updating order status
     * @return \Zend\View\Model\ViewModel
     */
    public function renderModalFormAction()
    {
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_file_form','meliscmsnews_file_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);
        
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int) $this->params()->fromQuery('newsId', '');
        $type = $this->params()->fromQuery('type', '');
        $data = array(
            'cnews_id' => $newsId,
            'type' => $type,
        );
        
        $form->setData($data);
        
        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->form = $form;
        return $view;
        
    }
    
    public function saveFileFormAction()
    {
        $this->getEventManager()->trigger('meliscmsnews_save_news_file_start', $this, array());
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');
        
        $response = array();
        $success = 0;
        $errors  = array();
        $data = array();
        $textMessage = $melisTool->getTranslation('tr_meliscmsnews_save_file_fail');
        $textTitle = $melisTool->getTranslation('tr_meliscmsnews_list_header_title');
        
        if($this->getRequest()->isPost()) {            
            $postValues = get_object_vars($this->getRequest()->getPost());
            $file = $this->attachmentValidator();
            
            if(empty($file['errors'])){
                $news = $newsSvc->getNewsById($postValues['cnews_id'])->getNews();
                
                //check for image or file type
                if($file['type'] == 'file'){
                    $string = 'cnews_documents';
                }else{
                    $string = 'cnews_image';
                }
                
                //file/image limit
                $limit = 3;
                for($c = 1; $c <= $limit; $c++){
                    $tmp = $string . $c;
                    if(empty($news->$tmp)){
                        $data = array(
                            $tmp => $file['fileName']
                        );
                        if($newsSvc->saveNews($data, $postValues['cnews_id'])){
                            $success = 1;
                            $textMessage = $melisTool->getTranslation('tr_meliscmsnews_save_file_success');
                        }else{
                            $textMessage = 'atay';
                        }
                        break;
                    }
                }  
            }else{
                $textMessage = $file['errors'];
            }
            
            //check what to upload
        }
        
        $response = array(
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors,
            'chunk' => $postValues,
        );
        $this->getEventManager()->trigger('meliscmsnews_save_news_file_end', $this, $response);
        return new JsonModel($response);
    } 
    
    public function saveNewsLetterAction()
    {
        $this->getEventManager()->trigger('meliscmsnews_save_news_letter_start', $this, array());
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $response = array();
        $success = 0;
        $errors  = array();
        $data = array();
        $textMessage = $melisTool->getTranslation('tr_meliscmsnews_save_fail');
        $textTitle = $melisTool->getTranslation('tr_meliscmsnews_list_header_title');
        
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        
        if($this->getRequest()->isPost()){
            $postValues = get_object_vars($this->getRequest()->getPost());
            
            $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
            $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_properties_form','meliscmsnews_properties_form');
            $factory = new \Zend\Form\Factory();
            $formElements = $this->serviceLocator->get('FormElementManager');
            $factory->setFormElementManager($formElements);
            $form = $factory->createForm($appConfigForm);            
            $form->setData($postValues);
            
            $parConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_paragraph_form','meliscmsnews_paragraph_form');
            $parFactory = new \Zend\Form\Factory();
            $parFormElements = $this->serviceLocator->get('FormElementManager');
            $parFactory->setFormElementManager($parFormElements);
            $parForm = $parFactory->createForm($parConfigForm);
            
            $parConf = $melisCoreConfig->getItem('meliscmsnews/conf/paragraphs_conf/');
            $limit = $parConf['max'];
            $name = $parConf['name'];
            
            if($form->isValid()){
                $data = $form->getData();
                
                // paragraph config in interface
                for($c = 1; $c <= $limit; $c++){
                    $tmpForm = clone($parForm);
                    $tmp = $name.$c;
                    if(!empty($postValues[$tmp])){
                        $tmpForm->setData(array('paragraph' => $postValues[$tmp]));                        
                        if($tmpForm->isValid()){
                            $data[$tmp] = $tmpForm->getData()['paragraph'];
                        }
                    }                                      
                }
                                
                $id = $data['cnews_id'];
                $data['cnews_status'] = $postValues['cnews_status'];
                unset($data['cnews_id']);
                if(empty($id)){
                    $data['cnews_creation_date'] = date("Y-m-d H:i:s");
                }
                
                $data['cnews_id'] = $newsSvc->saveNews($data, $id);
                if($data['cnews_id']){
                    $textMessage = $melisTool->getTranslation('tr_meliscmsnews_save_success');
                    $success = 1;
                }
                
            }else{
                $errors = $form->getMessages();
                foreach ($errors as $keyError => $valueError)
                {
                    foreach ($appConfigForm['elements'] as $keyForm => $valueForm)
                    {
                        if ($valueForm['spec']['name'] == $keyError &&
                            !empty($valueForm['spec']['options']['label']))
                            $errors[$keyError]['label'] = $valueForm['spec']['options']['label'];
                    }
                }
            }
        }
        
        $response = array(
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors,
            'chunk' => $data,
        );
        $this->getEventManager()->trigger('meliscmsnews_save_news_letter_end', $this, $response);
        return new JsonModel($response);
        
    }
    
    public function removeAttachFileAction()
    {
        $this->getEventManager()->trigger('meliscmsnews_delete_news_file_start', $this, array());
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $response = array();
        $success = 0;
        $errors  = array();
        $data = array();
        $textMessage = $melisTool->getTranslation('tr_meliscmsnews_remove_file_fail');
        $textTitle = $melisTool->getTranslation('tr_meliscmsnews_list_header_title');
        
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');
        
        if($this->getRequest()->isPost()){
            $postValues = get_object_vars($this->getRequest()->getPost());
            $tmp = $newsSvc->getNewsById($postValues['newsId']);
            $data = array(
              $postValues['column'] => ''
            );
            
            if($newsSvc->saveNews($data, $postValues['newsId'])){
                $success = 1;
                $textMessage = $melisTool->getTranslation('tr_meliscmsnews_remove_file_success');
                $col = $postValues['column'];
                $fileUploadPath = 'public'.$tmp->getNews()->$col;
                $this->deleteFileFromDirectory($fileUploadPath);
            }
        }
        
        $response = array(
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors,
            'chunk' => $data,
        );
        $this->getEventManager()->trigger('meliscmsnews_delete_news_file_end', $this, $response);
        return new JsonModel($response);
        
    }
    
    private function deleteFileFromDirectory($fileUploadPath)
    {
        if(file_exists($fileUploadPath)) {
        
            if(is_readable($fileUploadPath) && is_writable($fileUploadPath)) {
        
                unlink($fileUploadPath);
            }
        }
    }
    
    private function attachmentValidator()
    {
        $melisCoreConfig = $this->getServiceLocator()->get('MelisCoreConfig');
        $confUpload = $melisCoreConfig->getItem('meliscmsnews/conf/files');
        $minSize = $confUpload['minUploadSize'];
        $maxSize = $confUpload['maxUploadSize'];    
        $conPath = $confUpload['imagesPath'];
        $upload  = false;
        $textMessage = '';
        $newFileName = '';
        $type = 'file';

        //prepare validators
        $size = new Size(array(
            'min'=> $minSize,
            'max' => $maxSize,
            'messages' => array(
                'fileSizeTooBig' => $this->getTool()->getTranslation('tr_meliscmsnews_save_upload_too_big', array($this->formatBytes($maxSize))),
                'fileSizeTooSmall' => $this->getTool()->getTranslation('tr_meliscmsnews_save_upload_too_small', array($this->formatBytes($minSize))),
                'fileSizeNotFound' => $this->getTool()->getTranslation('tr_meliscmsnews_save_upload_file_does_not_exists'),
            ),
        ));
        
        $imageValidator = new IsImage(array(
            'messages' => array(
                'fileIsImageFalseType' => $this->getTool()->getTranslation('tr_meliscmsnews_save_upload_image_fileIsImageFalseType'),
                'fileIsImageNotDetected' => $this->getTool()->getTranslation('tr_meliscmsnews_save_upload_image_fileIsImageNotDetected'),
                'fileIsImageNotReadable' => $this->getTool()->getTranslation('tr_meliscmsnews_save_upload_image_fileIsImageNotReadable'),
            ),
        ));
        
        $postValues = get_object_vars($this->getRequest()->getPost());
        $uploadedFile = $this->getRequest()->getFiles()->toArray()['cnews_document'];
        
        //set validators for file only or image only
        if($postValues['type'] == 'image'){
            $type = 'image';
            $validator = array($size, $imageValidator);
        }else{
            $validator = array($size);
        }
        
        if(!emptY($uploadedFile['name'])){
            //format name
            $fileName = $uploadedFile['name'];
            $formattedFileName = $this->getFormattedFileName($fileName);
            
            //create folder based on news id
            if($this->createFolder($postValues['cnews_id'])) {
                $adapter = new Http();
            
                // do saving
                $adapter->setValidators($validator, $fileName);
            
                if($adapter->isValid()){
                    $adapter->setDestination('public' . $conPath . $postValues['cnews_id'] . '/');
                    $newFileName = $this->renameIfDuplicateFile($conPath . $postValues['cnews_id'] . '/' . $fileName);
                    $savedDocFileName =  'public'.$newFileName;
                    $adapter->addFilter('File\Rename', array(
                        'target' => $savedDocFileName,
                        'overwrite' => true,
                    ));
            
                    // if uploaded successfully
                    if($adapter->receive()) {
                        $upload = true;
                    }else{
                        $textMessage = 'error upload';
                    }
                }else{
                    foreach($adapter->getMessages() as $message) {
                        $textMessage = $message;
                    }
                }
            }else{
                $textMessage = $this->getTool()->getTranslation('tr_meliscmsnews_save_upload_file_path_rights_error');
            }
        }else{
            $textMessage = $this->getTool()->getTranslation('tr_meliscmsnews_save_upload_empty_file');
        }
        
        $file = array(
            'upload' => $upload,
            'type' => $type,
            'fileName' => $newFileName,
            'errors' => $textMessage,
        );
        
        return $file;
    }
    
    /**
     * Creates a folder inside "public/media/commerce" with full permission (for now)
     * @param String $folderType, enum: category, product, variant
     * @param int $folderId
     * @return bool
     */
    private function createFolder($id)
    {
        $status = false;
        $path = 'public/media/news/'.$id.'/';
        if(file_exists($path)) {
                        chmod ( $path , 0777 );
            $status = true;
        }
        else {
            $status = mkdir($path, 0777, true);
            $this->createFolder($id);
        }
    
        return $status;
    }
    
    public function getFormattedFileName($fileName)
    {
        $file = pathinfo($fileName, PATHINFO_FILENAME);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = $file;
    
        return $newFileName;
    }
    
    public function renameIfDuplicateFile($filePath)
    {
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');
        $docData = $newsTable->checkForDuplicates(pathinfo($filePath, PATHINFO_DIRNAME).'/'.pathinfo($filePath, PATHINFO_FILENAME));
//         echo count($docData);die();
        $totalFile = count($docData) ? '_' .count($docData) : null;
        $fileDir = pathinfo($filePath, PATHINFO_DIRNAME);
        $fileName = pathinfo($filePath, PATHINFO_FILENAME) . $totalFile;
        // replace space with underscores
        $fileName = str_replace(' ', '_', $fileName);
        $fileExt  = pathinfo($filePath, PATHINFO_EXTENSION) ? '.' . pathinfo($filePath, PATHINFO_EXTENSION) : '';
        $newFilePathAndName = $fileDir . '/'. $fileName . $fileExt;
    
        return $newFilePathAndName;
    
    }
    
    private function formatBytes($bytes) {
        $size = $bytes;
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return round(number_format($size / pow(1024, $power), 2, '.', ',')) . ' ' . $units[$power];
    }
    
    /**
     * sets the news letter data to the layout
     * @param unknown $couponId
     */
    private function setTableVariables($newsId)
    {
        $layoutVar = array();
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');
        if($newsId){
            $resultData = $newsSvc->getNewsById($newsId);
            $layoutVar['news'] = $resultData->getNews();
        }
        $this->layout()->setVariables( array_merge( array(
            'newsId' => $newsId,
        ), $layoutVar));
    }
    
    /**
     * Returns the Tool Service Class
     * @return MelisCoreTool
     */
    private function getTool()
    {
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
//         $melisTool->setMelisToolKey('meliscmsnews', 'meliscmsnews');
    
        return $melisTool;
    
    }
}
