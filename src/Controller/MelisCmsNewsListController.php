<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller;

use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Laminas\Session\Container;
use MelisCore\Controller\MelisAbstractActionController;

class MelisCmsNewsListController extends MelisAbstractActionController
{
    /**
     * renders the page container
     * @return \Laminas\View\Model\ViewModel
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
     * @return \Laminas\View\Model\ViewModel
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
     * @return \Laminas\View\Model\ViewModel
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
     * @return \Laminas\View\Model\ViewModel
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
     * @return \Laminas\View\Model\ViewModel
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
     * @return \Laminas\View\Model\ViewModel
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
     * @return \Laminas\View\Model\ViewModel
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
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsListContentFilterLimitAction()
    {
        return new ViewModel();
    }

    /**
     * renders the coupon list content news filter site
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsListContentFilterSiteAction()
    {
        $tableSite = $this->getServiceManager()->get('MelisEngineTableSite');
        $sites = $tableSite->fetchAll();
        $siteId = $this->getRequest()->getPost('cnews_site_id');

        $options = '<option  value="">'.$this->getTool()->getTranslation('tr_meliscmsliderdetails_common_label_choose').'</option>';
        foreach($sites as $site){
            $selected  = ($site->site_id == $siteId)? 'selected' : '';
            $siteLabel = $site->site_label ?? $site->site_name;
            $options .= '<option value="'.$site->site_id.'" '.$selected.'>'.$siteLabel .'</option>';
        }

        $view =  new ViewModel();
        $view->options = $options;
        return $view;
    }

    /**
     * renders the coupon list content news filter search
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsListContentFilterSearchAction()
    {
        return new ViewModel();
    }

    /**
     * renders the coupon list content news filter refresh
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsListContentFilterRefreshAction()
    {
        return new ViewModel();
    }

    /**
     * renders the coupon list content news action info
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsListContentActionInfoAction()
    {
        $view = new ViewModel();
        $view->title = $this->getTool()->getTranslation('tr_meliscms_common_edit');

        return $view;
    }

    /**
     * renders the coupon list content news action edit
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsListContentActionEditAction()
    {
        return new ViewModel();
    }

    /**
     * renders the coupon list content news action edit
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsListContentActionDeleteAction()
    {
        $view = new ViewModel();
        $view->title = $this->getTool()->getTranslation('tr_meliscms_common_delete');

        return $view;
    }

    /**
     * Renders the workflow action button
     * @return ViewModel
     */
    public function renderNewsListContentActionWorkflowAction()
    {
        $view = new ViewModel();
        $view->title = $this->getTool()->getTranslation('tr_meliscmsnews_action_workflow');
        return $view;
    }
    /**
     * renders the coupon list page news
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsListContentTableAction()
    {
        $columns = $this->getTool()->getColumns();
        $columns['actions'] = array('text' => 'Action');
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        $view->tableColumns = $columns;
        $view->getToolDataTableConfig = $this->getTool()->getDataTableConfiguration('#newsList', true, false, array('order' => '[[ 0, "desc" ]]'));
        return $view;
    }

    /**
     * Retrieves the news data list
     * @return \Laminas\View\Model\JsonModel
     */
    public function renderNewsListDataAction()
    {
        $success = 0;
        $colId = array();
        $dataCount = 0;
        $draw = 0;
        $dataFiltered = 0;
        $tableData = array();

        $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');

        if($this->getRequest()->isPost()) {

            $cnews_site_id = $this->getRequest()->getPost('cnews_site_id');
            $cnews_site_id = !empty($cnews_site_id)? $cnews_site_id : null;

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

            $locale = 'en_EN';
            $container = new Container('meliscore');
            $lang_id = null;
            if ($container) {
                $melisEngineLangTable = $this->getServiceManager()->get('MelisEngineTableCmsLang');
                $locale = $container['melis-lang-locale'];
                $currentLangData = $melisEngineLangTable->getEntryByField('lang_cms_locale', $locale);

                $currentLang = $currentLangData->current();

                if (!empty($currentLang)) {
                    $lang_id = $currentLang->lang_cms_id;
                }
            }

            $tmp = $newsSvc->getNewsList(null, null, null, null, null, null, null, null, null, null,  null, $cnews_site_id, $search);
            $dataFiltered = count($tmp);

            $news = $newsSvc->getNewsList(null, null, null, null, null, null, null, $start, $length, $selCol, $sortOrder, $cnews_site_id, $search);

            $dataArray = [];
            $idArray = [];

            // get news with lang_id equals to current lang id of platform
            foreach($news as $new) {
                if ($new['cnews_lang_id'] == $lang_id) {
                    $dataArray[] = $new;
                    $idArray[] = $new['cnews_id'];
                }
            }

            // get news with lang_id not equals to current lang id of platform but doesnt exist on above id array
            foreach ($news as $new) {
                if (!in_array($new['cnews_id'], $idArray)) {
                    if ($new['cnews_lang_id'] !== $lang_id) {
                        $dataArray[] = $new;
                    }
                }
            }

            // sort by column
            if (!empty($selCol) && empty(!$sortOrder)) {
                $sortOrder = ($sortOrder == 'asc') ? SORT_ASC : SORT_DESC;
                array_multisort(array_column($dataArray, $selCol), $sortOrder, $dataArray);
            } else {
                array_multisort(array_column($dataArray, 'cnews_id'), SORT_DESC, $dataArray);
            }

            $dataCount = count($dataArray);
            $c = 0;
            foreach($dataArray as $new) {
                $status = '<span class="text-success"><i class="fa fa-fw fa-circle"></i></span>';
                if(!$new['cnews_status']){
                    $status = '<span class="text-danger"><i class="fa fa-fw fa-circle"></i></span>';
                }

//                $new->cnews_title = !empty($data->cnews_title) ? $data->cnews_title : $new->cnews_title;
                $tableData[$c]['DT_RowId'] = $new['cnews_id'];
                $tableData[$c]['cnews_id'] = $new['cnews_id'];
                $tableData[$c]['cnews_status'] = $status;
                $tableData[$c]['cnews_title'] = $this->getTool()->escapeHtml($new['cnews_title']);
                $tableData[$c]['cnews_creation_date'] = $this->getTool()->dateFormatLocale($new['cnews_creation_date']);
                $tableData[$c]['cnews_publish_date'] = $this->getTool()->dateFormatLocale($new['cnews_publish_date']);
                $tableData[$c]['cnews_unpublish_date'] = $this->getTool()->dateFormatLocale($new['cnews_unpublish_date']);
                $tableData[$c]['site_label'] = $new['site_label'];
                $c++;
            }

        }

        return new JsonModel(array (
            'draw'              => (int) $draw,
            'recordsTotal'      => $dataCount,
            'recordsFiltered'   => $dataFiltered,
            'data'              => $tableData,
        ));
    }

    /**
     * Deletes the news letter
     * @return \Laminas\View\Model\JsonModel
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

        $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');

        if($this->getRequest()->isPost()){
            $postValues = $this->getRequest()->getPost()->toArray();
            $newsId = $postValues['newsId'] ?? null;
            //if news id is not given from the post values, try to get from the route param from the forward dispatch event during rollback when seo saving is not successful
            if (empty($newsId)) {
                $newsId = $this->params()->fromRoute('newsId');
            }

            $id = $newsId ;
            $tmp = $newsSvc->getNewsById($newsId);
            if (!empty($tmp) && is_array($tmp)) {
                $tmp = $tmp[0];
            }

            //delete db data
            if ($newsSvc->deleteNewsById($newsId)) {
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
        $melisTool = $this->getServiceManager()->get('MelisCoreTool');
        $melisTool->setMelisToolKey('meliscmsnews', 'meliscmsnews_list_table');
        return $melisTool;

    }
}