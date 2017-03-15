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

class MelisCmsNewsListController extends AbstractActionController
{
    /**
     * renders the page container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListPageAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        return $view;
    }
    

    /**
     * renders the header container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListHeaderAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        return $view;
    }
    
    /**
     * renders the news list page left header container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListHeaderLeftAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        return $view;
    }
    
    /**
     * renders the news list page right header container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListHeaderRightAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        return $view;
    }
    
    /**
     * renders the news list page right header container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListHeaderRightAddAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        return $view;
    }
    
    /**
     * renders the news list page title
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListHeaderTitleAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        return $view;
    }
    
    /**
     * renders the news list page content container
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListContentAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        return $view;
    }
    
    /**
     * renders the coupon list content news filter limit
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListContentFilterLimitAction()
    {
        return new ViewModel();
    }
    
    /**
     * renders the coupon list content news filter search
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListContentFilterSearchAction()
    {
        return new ViewModel();
    }
    
    /**
     * renders the coupon list content news filter refresh
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListContentFilterRefreshAction()
    {
        return new ViewModel();
    }
    
    /**
     * renders the coupon list content news action info
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListContentActionInfoAction()
    {
        return new ViewModel();
    }
    
    /**
     * renders the coupon list content news action edit
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListContentActionEditAction()
    {
        return new ViewModel();
    }
    
    /**
     * renders the coupon list content news action edit
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListContentActionDeleteAction()
    {
        return new ViewModel();
    }
    
    /**
     * renders the coupon list page news
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsListContentTableAction()
    {       
        $columns = $this->getTool()->getColumns();        
        $columns['actions'] = array('text' => 'Action');        
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        $view->tableColumns = $columns;        
        $view->getToolDataTableConfig = $this->getTool()->getDataTableConfiguration('#newsList', true);       
        return $view;
    }
    
    /**
     * Retrieves the news data list
     * @return \Zend\View\Model\JsonModel 
     */
    public function renderNewsListDataAction()
    {
        $success = 0;
        $colId = array();
        $dataCount = 0;
        $draw = 0;
        $dataFiltered = 0;
        $tableData = array();
        
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');
        
        if($this->getRequest()->isPost()) {
            $colId = array_keys($this->getTool()->getColumns());
            
            $sortOrder = $this->getRequest()->getPost('order');
            $sortOrder = $sortOrder[0]['dir'];
            
            $selCol = $this->getRequest()->getPost('order');
            $selCol = $colId[$selCol[0]['column']];
            $colOrder = $selCol. ' ' . $sortOrder;
            
            $draw = (int) $this->getRequest()->getPost('draw');
            
            $start = (int) $this->getRequest()->getPost('start');
            $length =  (int) $this->getRequest()->getPost('length');
            
            $search = $this->getRequest()->getPost('search');
            $search = $search['value'];
            
            $postValues = $this->getRequest()->getPost();
            
            $tmp = $newsSvc->getNewsList(null, null, null, null, null, null, null, null,  null, $search);
             
            $dataFiltered = count($tmp);
            
            $news = $newsSvc->getNewsList(null, null, null, null, null, $start, $length, $selCol, $sortOrder, $search);
            
            $dataCount = count($news);
            $c = 0;
            
            foreach($news as $new){
                
               $status = '<span class="text-success"><i class="fa fa-fw fa-circle"></i></span>';
                if(!$new->cnews_status){
                    $status = '<span class="text-danger"><i class="fa fa-fw fa-circle"></i></span>';
                }
                
                $tableData[$c]['DT_RowId'] = $new->cnews_id; 
                $tableData[$c]['cnews_id'] = $new->cnews_id;               
                $tableData[$c]['cnews_status'] = $status;
                $tableData[$c]['cnews_title'] = $new->cnews_title;
                $tableData[$c]['cnews_creation_date'] = $this->getTool()->dateFormatLocale($new->cnews_creation_date);
                $tableData[$c]['cnews_publish_date'] = $this->getTool()->dateFormatLocale($new->cnews_publish_date);
                $c++;
            }
        }
        
        return new JsonModel(array (
            'draw' => (int) $draw,
            'recordsTotal' => $dataCount,
            'recordsFiltered' =>  $dataFiltered,
            'data' => $tableData,
        ));
    }
    
    /**
     * Deletes the news letter
     * @return \Zend\View\Model\JsonModel
     */
    public function deleteNewsAction()
    {
        $this->getEventManager()->trigger('meliscmsnews_delete_news_start', $this, array());
        $response = array();
        $id = null;
        $success = 0;
        $errors  = array();
        $data = array();
        $textMessage = 'tr_meliscmsnews_news_delete_fail';
        $textTitle = 'tr_meliscmsnews_list_header_title';
        
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        
        if($this->getRequest()->isPost()){ 
            $postValues = get_object_vars($this->getRequest()->getPost());
            $id = $postValues['newsId'];
            $tmp = $newsSvc->getNewsById($postValues['newsId']);
            //delete db data
            if($newsSvc->deleteNewsById($postValues['newsId'])){
                //delete directory files
                $iConf = $melisCoreConfig->getItem('meliscmsnews/conf/images_conf/');
                $iLimit = $iConf['max'];
                $fileUploadPath = null;
                //delete images
                for($c = 1; $c <= $iLimit; $c++){
                    $iColumn = $iConf['name'].$c;
                    if(!empty($tmp->$iColumn)){
                        $fileUploadPath = 'public'. $tmp->$iColumn;
                        if(file_exists($fileUploadPath)) {
                            if(is_readable($fileUploadPath) && is_writable($fileUploadPath)) {
                                unlink($fileUploadPath);
                            }
                        }
                    }
                }
                
                $fConf = $melisCoreConfig->getItem('meliscmsnews/conf/documents_conf/');
                $fLimit = $fConf['max'];
                $fileUploadPath = null;
                //delete documents
                for($c = 1; $c <= $iLimit; $c++){
                    $iColumn = $fConf['name'].$c;
                    if(!empty($tmp->$iColumn)){
                        $fileUploadPath = 'public'. $tmp->$iColumn;
                        if(file_exists($fileUploadPath)) {
                            if(is_readable($fileUploadPath) && is_writable($fileUploadPath)) {
                                unlink($fileUploadPath);
                            }
                        }
                    }
                }
                //remove emptied folder
                if(!empty($fileUploadPath)){
                    $fileDir = pathinfo($fileUploadPath, PATHINFO_DIRNAME);
                    rmdir($fileDir);
                }
                
                $success = 1;
                $textMessage = 'tr_meliscmsnews_news_delete_success';
            }
        }
        
        $response = array(
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors,
            'chunk' => $data,
        );
        
        $this->getEventManager()->trigger('meliscmsnews_delete_news_end',
            $this, array_merge($response, array('typeCode' => 'CMS_NEWS_DELETE', 'itemId' => $id)));
        
        return new JsonModel($response);
    }
    
    /**
     * Returns the Tool Service Class
     * @return MelisCoreTool
     */
    private function getTool()
    {
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $melisTool->setMelisToolKey('meliscmsnews', 'meliscmsnews_list_table');
        return $melisTool;
    
    }
}
