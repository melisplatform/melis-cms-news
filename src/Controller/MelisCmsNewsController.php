<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller;


use Laminas\File\Transfer\Adapter\Http;
use Laminas\Form\ElementInterface;
use Laminas\Form\Factory;
use Laminas\Session\Container;
use Laminas\Validator\File\IsImage;
use Laminas\Validator\File\Size;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use MelisCore\Controller\MelisAbstractActionController;

class MelisCmsNewsController extends MelisAbstractActionController
{
    /**
     * renders the page container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsPageAction()
    {

        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * sets the news letter data to the layout
     * @param $newsId
     */
    private function setTableVariables($newsId)
    {
        $layoutVar = [];
        $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');
        if ($newsId) {
            $resultData = $newsSvc->getNewsById($newsId);
            $layoutVar['news'] = $resultData;
        }
        $this->layout()->setVariables(array_merge(array(
            'newsId' => $newsId,
        ), $layoutVar));
    }

    /**
     * renders the page header container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsHeaderAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the page header left container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsHeaderLeftAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the page header right container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsHeaderRightAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the tabs content header add button
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsHeaderRightSaveAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the page header title
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsHeaderTitleAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $newsName = '';

        $view->newsName = $newsName;
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the page content container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsPageContentAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', 0);
         /**
         * Checks if Melis Small Business module is disabled
         */
        $sbIsDisabled = !in_array('MelisSmallBusiness', $this->getServiceManager()->get('MelisAssetManagerModulesService')->getActiveModules());
        $view->activeModules = $this->getServiceManager()->get('MelisAssetManagerModulesService')->getActiveModules();
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->sbIsDisabled = $sbIsDisabled;   
        return $view;
    }

    /**
     * renders the page main content container
     * @return \Laminas\View\Model\ViewModel render-coupon-page-tab-main
     */
    public function renderNewsPageTabsMainAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the tabs content header container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsContentHeaderAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the tabs content header left container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsContentHeaderLeftAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        /**
         * Checks if Melis Small Business module is disabled
         */
        $sbIsDisabled = !in_array('MelisSmallBusiness', $this->getServiceManager()->get('MelisAssetManagerModulesService')->getActiveModules());
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->sbIsDisabled = $sbIsDisabled;   
        return $view;
    }

    /**
     * renders the tabs content header status
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsContentHeaderStatusAction()
    {
        $status = '';
        if (!empty($this->layout()->news) && is_array($this->layout()->news)) {
            if ($this->layout()->news[0]->cnews_status) {
                $status = 'checked';
            }
        }

        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->status = $status;
        return $view;
    }

    /**
     * renders the tabs content header workflow
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsContentHeaderWorkflowAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
               
        $newsTitle = "";
        if (!empty($newsId)) {
            $newsService = $this->getServiceManager()->get('MelisCmsNewsService');
            $newsTextData = $newsService->getPostText($newsId)->current();
            if (!empty($newsTextData)) {
                $newsTitle = $newsTextData->cnews_title;
            }
        }

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->label = $this->getTool()->getTranslation('tr_meliscmsnews_action_workflow');
        $view->newsTitle = $newsTitle;
        return $view;
    }
    /**
     * renders the tabs content header title
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsContentHeaderTitleAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the tabs content details container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsContentDetailsAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the tabs content details main content
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsMainAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the tab left content container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftAction()
    {
        /**
         * True when MelisCmsComments module is DISABLED
         */
        $commentsModuleIsDisabled = !in_array('MelisCmsComments', $this->getServiceManager()->get('MelisAssetManagerModulesService')->getActiveModules());

        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->commentsModuleIsDisabled = $commentsModuleIsDisabled;

        return $view;
    }

    /**
     * renders the tab right content container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsRightAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * renders the tab left content properties
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftPropertiesAction()
    {
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $tool = $this->getServiceManager()->get('MelisCoreTool');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_properties_form', 'meliscmsnews_properties_form');
        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        if (!empty($this->layout()->news) && is_array($this->layout()->news)) {
            $newsData = (array)$this->layout()->news[0];
            $newsData['cnews_publish_date'] = $tool->dateFormatLocale($newsData['cnews_publish_date']);
            $newsData['cnews_unpublish_date'] = $tool->dateFormatLocale($newsData['cnews_unpublish_date']);
            $form->setData($newsData);
        }

        $datePickerInit = $tool->datePickerInit('newsPublishDate');
        $datePickerInit .= $tool->datePickerInit('newsUnpublishDate');

        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->form = $form;
        $view->datePickerInit = $datePickerInit;
        return $view;
    }

    /**
     * renders the tab left content documents
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftDocumentsAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);

        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $parConf = $melisCoreConfig->getItem('meliscmsnews/conf/documents_conf/');
        $limit = $parConf['max'];
        $name = $parConf['name'];
        $documents = [];

        if (!empty($this->layout()->news) && is_array($this->layout()->news)) {
            for ($c = 1; $c <= $limit; $c++) {
                $tmp = $name . $c;
                if ($this->layout()->news[0]->$tmp) {
                    $i = array(
                        'cnews_id' => $this->layout()->news[0]->cnews_id,
                        'documents' => $this->layout()->news[0]->$tmp,
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
     * renders the tab left content site select
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftSitesAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);

        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');

        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_site_select_form', 'meliscmsnews_site_select_form');
        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        /** Emphasizing site as a mandatory field by adding an asterisk */
        $form->get('cnews_site_id')->setLabel($form->get('cnews_site_id')->getLabel() . ' *');

        if (!empty($this->layout()->news) && is_array($this->layout()->news)) {
            $form->setData((array)$this->layout()->news[0]);
        }

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->form = $form;
        return $view;
    }

    /**
     * renders the tab left content documents
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftImagesAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);

        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $parConf = $melisCoreConfig->getItem('meliscmsnews/conf/images_conf/');
        $limit = $parConf['max'];
        $name = $parConf['name'];
        $images = [];

        if (!empty($this->layout()->news) && is_array($this->layout()->news)) {
            for ($c = 1; $c <= $limit; $c++) {
                $tmp = $name . $c;
                if ($this->layout()->news[0]->$tmp) {
                    $i = array(
                        'cnews_id' => $this->layout()->news[0]->cnews_id,
                        'image' => $this->layout()->news[0]->$tmp,
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
        $view->limit = $limit;
        return $view;
    }

    /**
     * renders the tab right content paragraphs
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsRightParagraphsAction()
    {
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $forms = [];

        /**
         * Overriding the Title element's label: Added asterisk to emphasize as mandatory field
         */
        $titleFormConfig = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_site_title_subtitle_form', 'meliscmsnews_site_title_subtitle_form');
        $titleLabel = $titleFormConfig['elements'][0]['spec']['options']['label'];
        $titleFormConfig['elements'][0]['spec']['options']['label'] = $titleLabel . ' *';
        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $formsTitleSubtitle = $factory->createForm($titleFormConfig);

        $newsTextsTable = $this->getServiceManager()->get('MelisCmsNewsTextsTable');

        $data = ($newsId) ? (array)$newsTextsTable->getNewsDataByCnd(['cnews_id' => $newsId])->toArray() : '';
        $lang_id = $this->getLangId();
        $melisEngineLangTable = $this->getServiceManager()->get('MelisEngineTableCmsLang');
        $melisEngineLang = $melisEngineLangTable->fetchAll();
        $languages = $melisEngineLang->toArray();

        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');

        /** @var \MelisCmsNews\Model\Tables\MelisCmsNewsTable $newsTable */
        $siteId = null;
        $newsTable = $this->getServiceManager()->get('MelisCmsNewsTable');
        $newsTable = $newsTable->getNews($newsId);
        if (!empty($newsTable)) {
            $newsTable = $newsTable->toArray();
            $newsTable = reset($newsTable);
            $siteId = empty($newsTable['cnews_site_id']) ? $siteId : $newsTable['cnews_site_id'];
        }

        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->forms = $forms;
        $view->languages = $languages;
        $view->lang_id = $lang_id;
        $view->data = $data;
        $view->formsTitleSubtitle = $formsTitleSubtitle;
        $view->siteId = $siteId;

        return $view;
    }

    /**
     * Get Language
     * @return int
     */
    private function getLangId()
    {
        $container = new Container('meliscore');
        $currentLang = '';

        if ($container) {
            $melisEngineLangTable = $this->getServiceManager()->get('MelisEngineTableCmsLang');
            $locale = $container['melis-lang-locale'];
            $currentLangData = $melisEngineLangTable->getEntryByField('lang_cms_locale', $locale);

            $currentLang = $currentLangData->current();
        }

        return !empty($currentLang) ? $currentLang->lang_cms_id : 1;
    }

    /**
     * Saving news details
     * @return JsonModel
     */
    public function saveNewsLetterAction()
    {
        $this->getEventManager()->trigger('meliscmsnews_save_news_letter_start', $this, []);
        $melisTool = $this->getServiceManager()->get('MelisCoreTool');
        $id = null;
        $success = 0;
        $errors = [];
        $data = [];
        $textMessage = 'tr_meliscmsnews_save_fail';
        $textTitle = 'tr_meliscmsnews_list_header_title';
        $logTypeCode = '';
        $postValues = [];
        /** @var \MelisCmsNews\Service\MelisCmsNewsService $newsSvc */
        $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');
        $tool = $this->getServiceManager()->get('MelisCoreTool');

        if ($this->getRequest()->isPost()) {
            $postValues = $this->getRequest()->getPost()->toArray();
            // Cache the fields to be sanitized
            $sanitize = [
                'cnews_title' => $postValues['cnews_title'],
                'cnews_subtitle' => $postValues['cnews_subtitle'],
                'cnews_lang_id' => $postValues['cnews_lang_id'],
            ];
            $postValues = array_merge($postValues, $melisTool->sanitizePost($sanitize));

            $logTypeCode = !empty($postValues['cnews_id']) ? 'CMS_NEWS_UPDATE' : 'CMS_NEWS_ADD';

            /** Publish & Un-publish date checking */
            if (!empty($postValues['cnews_publish_date']) && !empty($postValues['cnews_unpublish_date'])) {
                // convert date to generic for date comparison
                $pubDate = $tool->localeDateToSql($postValues['cnews_publish_date'], '', 'en_EN');
                $unpubDate = $tool->localeDateToSql($postValues['cnews_unpublish_date'], '', 'en_EN');

                if (strtotime($pubDate) > strtotime($unpubDate)) {
                    $errors['cnews_unpublish_date'] = [
                        'isGreaterThan' => $this->getTool()->getTranslation('tr_meliscmsnews_form_unpublish_error'),
                        'label' => $this->getTool()->getTranslation('tr_meliscmsnews_form_unpublish'),
                    ];
                }
            }

            if (empty($postValues['cnews_id'])) {
                $postValues['cnews_creation_date'] = date("Y-m-d H:i:s");
            }

            /** Publish only date checking */
            if (empty($postValues['cnews_publish_date'])) {
                $postValues['cnews_publish_date'] = date("Y-m-d H:i:s");

                $pubDate = $tool->localeDateToSql($postValues['cnews_publish_date'], '', 'en_EN');
                $unpubDate = $tool->localeDateToSql($postValues['cnews_unpublish_date'], '', 'en_EN');

                if (!empty($postValues['cnews_publish_date']) && !empty($postValues['cnews_unpublish_date'])) {
                    if (strtotime($pubDate) > strtotime($unpubDate)) {
                        $errors['cnews_unpublish_date'] = [
                            'isGreaterThan' => $this->getTool()->getTranslation('tr_meliscmsnews_form_unpublish_error_date_today'),
                            'label' => $this->getTool()->getTranslation('tr_meliscmsnews_form_unpublish'),
                        ];
                    }
                }
            }

            $form = $this->getForm('meliscmsnews_properties_form');
            $form->setData($postValues);
            $data['cnews_id'] = 0;

            // check if any title exists
            $titleExist = false;
            for ($i = 0; $i < (int)$postValues['formCount']; $i++) {
                if (!empty($postValues['cnews_title'][$i])) {
                    $titleExist = true;
                    break;
                }
            }
            $titleErr = '';
            if (!$titleExist) {
                $titleErr = $this->getTool()->getTranslation('tr_meliscmsnews_form_error_empty');
            }

            /** Site Id checking */
            $siteForm = $this->getForm('meliscmsnews_site_select_form');
            $siteForm->setData($postValues);
            if (!$siteForm->isValid()) {
                $formErrors = $siteForm->getMessages();
                foreach ($formErrors as $fieldName => $fieldErrors) {
                    $errors[$fieldName] = $fieldErrors;
                    $errors[$fieldName]['label'] = $siteForm->get($fieldName)->getLabel();
                }
            }

            if (empty($errors) && $titleExist && $form->isValid()) {
                $newsTbl = $form->getData();
                $cnews_id = $newsTbl['cnews_id'];
                $newsTbl['cnews_status'] = $postValues['cnews_status'];
                $newsTbl['cnews_slider_id'] = empty($postValues['cnews_slider_id']) ? NULL : $postValues['cnews_slider_id'];
                $newsTbl['cnews_site_id'] = empty($postValues['cnews_site_id']) ? NULL : $postValues['cnews_site_id'];
                unset($newsTbl['cnews_id']);
                $newsTbl['cnews_publish_date'] = $tool->localeDateToSql($newsTbl['cnews_publish_date']);
                $newsTbl['cnews_unpublish_date'] = $tool->localeDateToSql($newsTbl['cnews_unpublish_date']);
                // user account
                if (isset($postValues['cnews_author_account']) && !empty($postValues['cnews_author_account'])) {
                    $newsTbl['cnews_author_account'] = $postValues['cnews_author_account'];
                }

                $data['cnews_id'] = $newsSvc->saveNews($newsTbl, $cnews_id);

                $lang_id = $this->getLangId();
                $data['cnews_title'] = '';
                if (!empty($data['cnews_id'])) {
                    /** @var \MelisCmsNews\Model\Tables\MelisCmsNewsTextsTable $newsTextsTable */
                    $newsTextsTable = $this->getServiceManager()->get('MelisCmsNewsTextsTable');
                    for ($i = 0; $i < (int)$postValues['formCount']; $i++) {
                        if ($postValues['cnews_lang_id'][$i] == $lang_id) {
                            $data['cnews_title'] = $postValues['cnews_title'][$i];
                        }

                        $newsTxt['cnews_title'] = $postValues['cnews_title'][$i];
                        $newsTxt['cnews_subtitle'] = $postValues['cnews_subtitle'][$i];
                        $newsTxt['cnews_lang_id'] = $postValues['cnews_lang_id'][$i];
                        $newsTxt['cnews_paragraph_order'] = $postValues['cnews_paragraph_order'][$i];

                        // get all paragraph values
                        for ($c = 1; $c <= 10; $c++) {
                            $newsTxt['cnews_paragraph' . $c] = $postValues['cnews_paragraph' . $c][$i];
                        }

                        // get news title to be displayed on new tab
                        if (empty($data['cnews_title'])) {
                            $data['cnews_title'] = !empty($postValues['cnews_title'][0]) ? $postValues['cnews_title'][0] : $postValues['cnews_title'][1];
                        }

                        if (empty($postValues['cnews_id'])) {
                            $newsTxt['cnews_id'] = $data['cnews_id'];
                            $newsTextsTable->save($newsTxt);
                        } else {
                            // check for current record of news texts
                            $currentData = $newsTextsTable->getEntryByField('cnews_id',$postValues['cnews_id'])->toArray();
                            // check record for every language
                            $textId = null;
                            if (! empty($currentData)) {
                                foreach ($currentData as $idx => $val) {
                                    if ($val['cnews_lang_id'] == $newsTxt['cnews_lang_id']) {
                                        $textId = $val['cnews_text_id'];
                                    }
                                }
                            }
                            if (!is_null($textId)) {
                                // update the text
                                $newsTextsTable->updateNewsText(
                                    $newsTxt, [
                                        'cnews_id' => $postValues['cnews_id'],
                                        'cnews_lang_id' => $newsTxt['cnews_lang_id']
                                    ]
                                );
                            } else {
                                // make a new record
                                $newsTxt['cnews_id'] = $postValues['cnews_id'];
                                $newsTextsTable->save($newsTxt);
                            }
                        }
                    }
                    //save seo tab
                    list('success' => $isSeoSuccess, 'errors' => $errors) = $this->saveNewsSeo($data['cnews_id']);
                    //rollback here when seo saving is not successful
                    if ($isSeoSuccess != 1) {
                        //delete news and news texts
                        $deleteNews = $this->forward()->dispatch(
                            'MelisCmsNews\Controller\MelisCmsNewsList',
                            [
                                'action' => 'deleteNews',
                                'newsId' => $data['cnews_id']                                
                            ]
                        )->getVariables();
                        $postValues['cnews_id'] = null;
                        $data['cnews_id'] = 0;
                    } else {
                    /** Updating post values for the listener: "meliscmsnews_get_postvalues" */
                    if (!empty($postValues['cnews_id'])) {
                        $postValues['cnews_id'] = $data['cnews_id'];
                    }

                        $textMessage = 'tr_meliscmsnews_save_success';
                        $success = 1;
                    }
                    $id = $data['cnews_id'];
                }
            } else {
                $formErrors = $form->getMessages();
                foreach ($formErrors as $fieldName => $fieldErrors) {
                    $errors[$fieldName] = $fieldErrors;
                    $errors[$fieldName]['label'] = $form->get($fieldName)->getLabel();
                }

                if (!$titleExist) {
                    $errors['cnews_title'] = [
                        'isEmpty' => $titleErr,
                        'label' => $this->getTool()->getTranslation('tr_meliscmsnews_plugin_filter_order_column_title')
                    ];
                }
            }
        }

        $response = [
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors,
            'chunk' => $data,
        ];

        $this->getEventManager()->trigger('meliscmsnews_get_postvalues', $this, $postValues);

        $this->getEventManager()->trigger('meliscmsnews_save_news_letter_end',
            $this, array_merge($response, ['typeCode' => $logTypeCode, 'itemId' => $id]));

        return new JsonModel($response);
    }

    /**
     * Returns the Tool Service Class
     * @return array|object
     */
    private function getTool()
    {
        $melisTool = $this->getServiceManager()->get('MelisCoreTool');

        return $melisTool;
    }

    /**
     * Returns a form from MelisCmsNews
     * @param $formName
     * @return ElementInterface
     */
    private function getForm(string $formName): ElementInterface
    {
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getItem('MelisCmsNews/forms/' . $formName);
        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        return $form;
    }

    /**
     * renders the modal container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderModalAction()
    {
        $id = $this->params()->fromQuery('id');
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $view->melisKey = $melisKey;
        $view->id = $id;
        $view->setTerminal(true);
        return $view;
    }

    /**
     * renders the order list modal for updating order status
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderModalFormAction()
    {
//        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
//        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_file_form', 'meliscmsnews_file_form');
//        $factory = new Factory();
//        $formElements = $this->getServiceManager()->get('FormElementManager');
//        $factory->setFormElementManager($formElements);
//        $form = $factory->createForm($appConfigForm);
        $form = $this->getForm('meliscmsnews_file_form');
        $file = '';
        $default = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAAPAAA/+EDLWh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS41LWMwMTQgNzkuMTUxNDgxLCAyMDEzLzAzLzEzLTEyOjA5OjE1ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozRkNFMzU3RDg2QUYxMUU1OEM4OENCQkI2QTc0MTkwRSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozRkNFMzU3Qzg2QUYxMUU1OEM4OENCQkI2QTc0MTkwRSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChNYWNpbnRvc2gpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDEwNzlDODNCQThDMTFFMjg5NTlFMDAzODgzMjZDMkIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDEwNzlDODRCQThDMTFFMjg5NTlFMDAzODgzMjZDMkIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAAGBAQEBQQGBQUGCQYFBgkLCAYGCAsMCgoL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mhNky5fTkBYwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/9k=';

        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $isNew = $this->params()->fromQuery('isNew', '');
        $type = $this->params()->fromQuery('type', '');

        $column = $this->params()->fromQuery('column');

        if (!$isNew) {
            $this->setTableVariables($newsId);
            $file = $this->layout()->news[0]->$column;
        }

        $data = [
            'cnews_id' => $newsId,
            'type' => $type,
            'cnews_document' => $file,
            'column' => $column
        ];

        $form->setData($data);

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->form = $form;
        $view->file = $file;
        $view->type = $type;
        $view->default = $default;

        return $view;
    }

    public function saveFileFormAction()
    {
        $this->getEventManager()->trigger('meliscmsnews_save_news_file_start', $this, []);
        $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');

        $id = null;
        $success = 0;
        $errors = [];
        $textMessage = 'tr_meliscmsnews_save_file_fail';
        $textTitle = 'tr_meliscmsnews_list_header_title';
        $logTypeCode = '';
        $postValues = [];
        if ($this->getRequest()->isPost()) {
            $postValues = $this->getRequest()->getPost()->toArray();
            $file = $this->attachmentValidator();

            if ($file['type'] == 'image') {
                $logTypeCode = 'CMS_NEWS_UPLOAD_IMG';
            } else {
                $logTypeCode = 'CMS_NEWS_UPLOAD_FILE';
            }

            if (empty($file['errors'])) {
                $id = $postValues['cnews_id'];
                $news = $newsSvc->getNewsById($postValues['cnews_id']);

                if (!empty($news) && is_array($news))
                    $news = $news[0];

                //check for image or file type
                if ($file['type'] == 'file') {
                    $string = 'cnews_documents';
                } else {
                    $string = 'cnews_image';
                }

                //file/image limit
                //used if insert
                $limit = 3;
                if (empty($postValues['column'])) {
                    for ($c = 1; $c <= $limit; $c++) {
                        $tmp = $string . $c;
                        if (empty($news->$tmp)) {
                            $data = [
                                $tmp => $file['fileName']
                            ];
                            if ($newsSvc->saveNews($data, $postValues['cnews_id'])) {
                                $success = 1;
                                $textMessage = 'tr_meliscmsnews_save_file_success';
                            }
                            break;
                        }
                    }
                } else {
                    $data = [
                        $postValues['column'] => $file['fileName']
                    ];
                    if ($newsSvc->saveNews($data, $postValues['cnews_id'])) {
                        $news = (array)$news;
                        if (!empty($news[$postValues['column']])) {
                            // if the file exists, delete the file after update
                            if (file_exists('public' . $news[$postValues['column']])) {
                                unlink('public' . $news[$postValues['column']]);
                            }
                        }

                        $success = 1;
                        $textMessage = 'tr_meliscmsnews_save_file_success';
                    }
                }

            } else {
                $textMessage = $file['errors'];
            }
        }

        $response = [
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors,
            'chunk' => $postValues,
        ];

        $this->getEventManager()->trigger('meliscmsnews_save_news_file_end',
            $this, array_merge($response, ['typeCode' => $logTypeCode, 'itemId' => $id]));

        return new JsonModel($response);
    }

    private function attachmentValidator()
    {
        try {
            $tool = $this->getTool();
            $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
            $confUpload = $melisCoreConfig->getItem('meliscmsnews/conf/files');
            $minSize = $confUpload['minUploadSize'];
            $maxSize = $confUpload['maxUploadSize'];
            $conPath = $confUpload['imagesPath'];
            $upload = false;
            $textMessage = '';
            $newFileName = '';
            $type = 'file';

            //prepare validators
            $size = new Size([
                'min' => $minSize,
                'max' => $maxSize,
                'messages' => [
                    'fileSizeTooBig' => $tool->getTranslation('tr_meliscmsnews_save_upload_too_big', [$this->formatBytes($maxSize)]),
                    'fileSizeTooSmall' => $tool->getTranslation('tr_meliscmsnews_save_upload_too_small', [$this->formatBytes($minSize)]),
                    'fileSizeNotFound' => $tool->getTranslation('tr_meliscmsnews_save_upload_file_does_not_exists'),
                ],
            ]);

            $imageValidator = new IsImage([
                'messages' => [
                    'fileIsImageFalseType' => $tool->getTranslation('tr_meliscmsnews_save_upload_image_fileIsImageFalseType'),
                    'fileIsImageNotDetected' => $tool->getTranslation('tr_meliscmsnews_save_upload_image_fileIsImageNotDetected'),
                    'fileIsImageNotReadable' => $tool->getTranslation('tr_meliscmsnews_save_upload_image_fileIsImageNotReadable'),
                ],
            ]);

            $postValues = $this->getRequest()->getPost()->toArray();
            $uploadedFile = $this->getRequest()->getFiles()->toArray()['cnews_document'];

            //set validators for file only or image only
            if ($postValues['type'] == 'image') {
                $type = 'image';
                $validator = [$size, $imageValidator];
            } else {
                $validator = [$size];
            }

            if (!empty($uploadedFile['name'])) {
                //format name
                $fileName = $uploadedFile['name'];

                //create folder based on news id
                if ($this->createFolder($postValues['cnews_id'])) {
                    $adapter = new Http();

                    // do saving
                    $adapter->setValidators($validator, $fileName);

                    /** Ensuring file is an image is  */
                    if ($type === 'image' && !empty($uploadedFile['tmp_name'])) {
                        $sourceImg = @imagecreatefromstring(@file_get_contents($uploadedFile['tmp_name']));
                        if ($sourceImg === false) {
                            return [
                                'upload' => false,
                                'type' => $type,
                                'fileName' => '',
                                'errors' => 'tr_meliscmsnews_save_upload_image_fileIsImageFalseType'
                            ];
                        }
                    }

                    if ($adapter->isValid()) {
                        $adapter->setDestination('public' . $conPath . $postValues['cnews_id'] . '/');
                        $newFileName = $this->renameIfDuplicateFile($conPath . $postValues['cnews_id'] . '/' . $fileName);
                        $savedDocFileName = 'public' . $newFileName;
                        $adapter->addFilter('Laminas\Filter\File\Rename', [
                            'target' => $savedDocFileName,
                            'overwrite' => true,
                        ]);

                        // if uploaded successfully
                        if ($adapter->receive()) {
                            $upload = true;
                        } else {
                            $textMessage = 'error upload';
                        }
                    } else {
                        foreach ($adapter->getMessages() as $message) {
                            $textMessage = $message;
                        }
                    }
                } else {
                    $textMessage = $tool->getTranslation('tr_meliscmsnews_save_upload_file_path_rights_error');
                }
            } else {
                $textMessage = $tool->getTranslation('tr_meliscmsnews_save_upload_empty_file');
            }

            $file = [
                'upload' => $upload,
                'type' => $type,
                'fileName' => $newFileName,
                'errors' => $textMessage,
            ];

            return $file;
        }catch (\Exception $ex){
            exit($ex->getMessage());
        }
    }

    private function formatBytes($bytes)
    {
        $size = $bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return round(number_format($size / pow(1024, $power), 2, '.', ',')) . ' ' . $units[$power];
    }

    /**
     * Creates a folder inside "public/media/commerce" with full permission (for now)
     * enum: category, product, variant
     * @param $id
     * @return bool
     */
    private function createFolder($id)
    {
        $path = 'public/media/news/' . $id . '/';
        if (file_exists($path)) {
            chmod($path, 0777);
            $status = true;
        } else {
            $status = mkdir($path, 0777, true);
            $this->createFolder($id);
        }

        return $status;
    }

    public function renameIfDuplicateFile($filePath)
    {
        $newsTable = $this->getServiceManager()->get('MelisCmsNewsTable');       
        $docData = $newsTable->checkForDuplicates(pathinfo($filePath, PATHINFO_DIRNAME) . '/' . str_replace("'","''", pathinfo($filePath, PATHINFO_FILENAME)));
        $totalFile = count($docData) ? '_' . count($docData) : null;
        $fileDir = pathinfo($filePath, PATHINFO_DIRNAME);
        $fileName = pathinfo($filePath, PATHINFO_FILENAME) . $totalFile;
        // replace space with underscores
        $fileName = str_replace(' ', '_', $fileName);
        $fileExt = pathinfo($filePath, PATHINFO_EXTENSION) ? '.' . pathinfo($filePath, PATHINFO_EXTENSION) : '';
        $newFilePathAndName = $fileDir . '/' . $fileName . $fileExt;

        return $newFilePathAndName;

    }

    public function removeAttachFileAction()
    {
        $this->getEventManager()->trigger('meliscmsnews_delete_news_file_start', $this, []);
        $id = null;
        $success = 0;
        $errors = [];
        $data = [];
        $textTitle = 'tr_meliscmsnews_common_label_remove_file';
        $textMessage = 'tr_meliscore_error_message';
        $logTypeCode = '';
        $type = null;

        $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');

        if ($this->getRequest()->isPost()) {
            $postValues = $this->getRequest()->getPost()->toArray();
            $id = $postValues['newsId'];
            $type = $postValues['type'];

            if (in_array($type, ['file', 'image'])) {
                $logTypeCode = 'CMS_NEWS_' . strtoupper($type) . '_DELETE';
                $textTitle = 'tr_meliscmsnews_delete_' . $type . '_title';
            }

            $tmp = $newsSvc->getNewsById($postValues['newsId']);

            if (!empty($tmp) && is_array($tmp)) {
                $tmp = $tmp[0];
            }

            $data = [
                $postValues['column'] => ''
            ];

            if ($newsSvc->saveNews($data, $postValues['newsId'])) {
                $success = 1;

                if (in_array($type, ['file', 'image'])) {
                    $textMessage = 'tr_meliscmsnews_delete_' . $type . '_success';
                }

                $col = $postValues['column'];
                $fileUploadPath = 'public' . $tmp->$col;
                $this->deleteFileFromDirectory($fileUploadPath);
            }
        }

        if ($success == 0) {
            if (in_array($type, ['file', 'image'])) {
                $textMessage = 'tr_meliscmsnews_delete_' . $type . '_unable';
            }
        }

        $response = [
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors,
            'chunk' => $data,
        ];

        $this->getEventManager()->trigger('meliscmsnews_delete_news_file_end',
            $this, array_merge($response, ['typeCode' => $logTypeCode, 'itemId' => $id]));

        return new JsonModel($response);

    }

    private function deleteFileFromDirectory($fileUploadPath)
    {
        if (file_exists($fileUploadPath)) {

            if (is_readable($fileUploadPath) && is_writable($fileUploadPath)) {

                unlink($fileUploadPath);
            }
        }
    }

    public function newsSliderDeletedAction()
    {
        $success = 0;
        $errors = [];
        $data = [];
        $newsTable = $this->getServiceManager()->get('MelisCmsNewsTable');
        $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');

        if ($this->getRequest()->isPost()) {
            $postValues = $this->getRequest()->getPost()->toArray();
            foreach ($newsTable->getEntryByField('cnews_slider_id', $postValues['sliderId']) as $news) {
                $tmp = [
                    'cnews_slider_id' => NULL
                ];
                $newsSvc->saveNews($tmp, $news->cnews_id);
                $success = 1;
            }

        }

        $results = [
            'success' => $success,
            'errors' => $errors,
            'datas' => $data,
        ];

        return new JsonModel($results);
    }

    public function getFormattedFileName($fileName)
    {
        $file = pathinfo($fileName, PATHINFO_FILENAME);
        $newFileName = $file;

        return $newFileName;
    }

    /**
     * Preview tab container view
     * @return ViewModel
     */
    public function previewTabContainerAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');

        $view = new ViewModel();
        $view->newsId = $newsId;
        $view->melisKey = $melisKey;

        return $view;
    }

    /**
     * Preview tab content container view
     * @return ViewModel
     */
    public function previewTabContentContainerAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');

        $view = new ViewModel();
        $view->newsId = $newsId;
        $view->melisKey = $melisKey;

        return $view;
    }

    /**
     * Preview tab's header
     * @return ViewModel
     */
    public function previewTabContentAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $pageId = (int)$this->params()->fromQuery('pageId', '');
        $tool = $this->getTool();
        $view = new ViewModel();
        $namespace = '';
        $newsURI = null;

        $labels = [
            'seeInTab' => $tool->getTranslation('tr_meliscmsnews_preview_load_intab'),
            'seeInNewTab' => $tool->getTranslation('tr_meliscmsnews_preview_load_newtab'),
            'newsTitlePrefix' => $tool->getTranslation('tr_meliscmsnews_preview_title_prefix'),
            'newsTitle' => '',
        ];

        /** @var \MelisCmsNews\Service\MelisCmsNewsService $newsSvc */
        $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');
        $detailPages = [];

        if (!empty($newsId)) {
            $langId = $this->getLangId();
            $newsDetails = $newsSvc->getNewsById($newsId);

            if (!empty($newsDetails) && is_array($newsDetails)) {
                $newsDetails = $newsDetails[0];
            }

            if (empty($newsDetails->cnews_title)) {
                /**
                 * Get the post's title, preferably that of the platform's language
                 */
                $postText = $newsSvc->getPostText($newsId)->toArray();

                foreach ($postText as $text) {
                    if ($text['cnews_lang_id'] === $langId) {
                        $labels['newsTitle'] = $text['cnews_title'];
                        break;
                    }
                }
                /**
                 * Get the fallback title = the next language with a title maintained
                 */
                if (empty($labels['newsTitle'])) {
                    foreach ($postText as $text) {
                        if (!empty($text['cnews_title'])) {
                            $labels['newsTitle'] = $text['cnews_title'];
                            break;
                        }
                    }
                }
            } else {
                $labels['newsTitle'] = $newsDetails->cnews_title;
            }

            /** Get pages of 'news detail' type via site id */
            $siteId = empty($newsDetails->cnews_site_id) ? null : $newsDetails->cnews_site_id;
            if (!empty($siteId)) {
                $detailPages = $newsSvc->getNewsDetailsPagesBySite($siteId);
            }
        }

        $newsDetailPageSelector = null;
        $detailPagesCount = count($detailPages);
        if ($detailPagesCount > 1) {
            /**
             * Case: Multiple 'Page - News details' found
             *  - Build & show the "News details page" selector
             */
            $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
            $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_page_detail_selector', 'meliscmsnews_page_detail_selector');
            $factory = new Factory();
            $formElements = $this->getServiceManager()->get('FormElementManager');
            $factory->setFormElementManager($formElements);
            /** @var \Laminas\Form\Form $newsDetailPageSelector */
            $newsDetailPageSelector = $factory->createForm($appConfigForm);

            $valueOptions = [];
            foreach ($detailPages as $page) {
                $valueOptions[$page['page_id']] = $page['page_id'] . " - " . $page['page_name'];
            }
            $newsDetailPageSelector->get('page-id')->setValueOptions($valueOptions);

            /** Set news id, & namespace attributes for the selector */
            $pageData = current($detailPages);
            $namespace = empty($pageData['tpl_zf2_website_folder']) ? $namespace : $pageData['tpl_zf2_website_folder'];
            if (!empty($newsId) && !empty($namespace)) {
                $newsDetailPageSelector->get('page-id')->setAttribute('data-news-id', $newsId);
                $newsDetailPageSelector->get('page-id')->setAttribute('data-name-space', $namespace);
            }
        } elseif ($detailPagesCount === 1) {
            /**
             * Case: Only 1 'Page - News details' found, && Page ID is available
             * - Hide "News details page" selector, & load the page in iFrame
             */
            $pageData = current($detailPages);
            $pageId = empty($pageData['page_id']) ? null : $pageData['page_id'];
            $namespace = empty($pageData['tpl_zf2_website_folder']) ? $namespace : $pageData['tpl_zf2_website_folder'];
        }

        if (!empty($pageId) && !empty($namespace) && !empty($newsId)) {
            $newsURI = "/id/$pageId/preview?melisSite=$namespace&newsId=$newsId&renderMode=previewtab";
        }


        $view->labels = $labels;
        $view->newsId = $newsId;
        $view->newsURI = $newsURI;
        $view->melisKey = $melisKey;
        $view->detailPagesCount = $detailPagesCount;
        $view->newsDetailPageSelector = $newsDetailPageSelector;

        return $view;
    }

    /**
     * Provides zone reloading functionality inside the Preview Tab
     * @return ViewModel
     */
    public function previewTabIframeAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $newsURI = $this->params()->fromQuery('newsUri', '');

        $noPageDetails = false;
        $tool = $this->getTool();
        $labels = [
            'noPageNewsDetails' => $tool->getTranslation('tr_meliscmsnews_preview_no_pages_found'),
        ];

        if (empty($newsURI)) {

            /** @var \MelisCmsNews\Service\MelisCmsNewsService $newsSvc */
            $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');
            $detailPages = [];
            $namespace = '';

            if (!empty($newsId)) {
                /** Get pages of 'news detail' type via site id */
                $newsDetails = $newsSvc->getNewsById($newsId);

                if (!empty($newsDetails) && is_array($newsDetails)) {
                    $newsDetails = $newsDetails[0];
                }

                $siteId = empty($newsDetails->cnews_site_id) ? null : $newsDetails->cnews_site_id;
                if (!empty($siteId)) {
                    $detailPages = $newsSvc->getNewsDetailsPagesBySite($siteId);
                }
            }

            if (empty($detailPages)) {
                /** Case: "No Page - News details found." */
                $noPageDetails = true;
            } elseif (count($detailPages) === 1) {
                /**
                 * Case: Only 1 'Page - News details' found, && Page ID is available
                 * - Hide "News details page" selector, & load the page in iFrame
                 */
                $pageData = current($detailPages);
                $pageId = empty($pageData['page_id']) ? null : $pageData['page_id'];
                $namespace = empty($pageData['tpl_zf2_website_folder']) ? $namespace : $pageData['tpl_zf2_website_folder'];
                if (!empty($pageId) && !empty($namespace) && !empty($newsId)) {
                    $newsURI = "/id/$pageId/preview?melisSite=$namespace&newsId=$newsId&renderMode=previewtab";
                }
            }
        }


        $view = new ViewModel();
        $view->labels = $labels;
        $view->newsId = $newsId;
        $view->melisKey = $melisKey;
        $view->newsURI = $newsURI;
        $view->noPageDetails = $noPageDetails;

        return $view;
    }
    /**
     * renders the tab left content container
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsContentSeoDetailsMainAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }
    /**
     * renders the tab right content paragraphs
     * @return \Laminas\View\Model\ViewModel
     */
    public function renderNewsTabsSeoDetailsAction()
    {       
        $newsId = (int)$this->params()->fromQuery('newsId', '');  
        $seoForm = $this->getNewsSeoForm();
        $newsSeoTable = $this->getServiceManager()->get('MelisCmsNewsSeoTable');
        $data = ($newsId) ? (array)$newsSeoTable->getEntryByField('cnews_id', $newsId)->toArray() : '';
        $lang_id = $this->getLangId();        
        $languages = $this->getOrderedLanguagesByCurrentLocale();
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');  
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;  
        $view->languages = $languages;
        $view->lang_id = $lang_id;
        $view->data = $data;
        $view->seoForm = $seoForm;   
        return $view;
    }
    /**
    * This retrieves the list of languages available in the platform where the current locale used is the first in the list
    * @return array
    */
    private function getOrderedLanguagesByCurrentLocale()
    {
        //get all languages available in the plaftform
        $coreLang = $this->getServiceManager()->get('MelisEngineTableCmsLang');
        $languages = $coreLang->fetchAll()->toArray();      
        $curLangId = $this->getLangId();
        //set the current locale as the first value in the array
        foreach ($languages as $key => $langData) {
            if ($langData["lang_cms_id"] == $curLangId) {
                unset($languages[$key]);
                array_unshift($languages,$langData);
            }
        }
        return $languages;
    }
    private function getNewsSeoForm() 
    {
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $seoFormConfig = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_seo_form', 'meliscmsnews_seo_form');
        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $seoForm = $factory->createForm($seoFormConfig);
        return $seoForm;
    }
    /**
     * This function saves the seo of the news
     * @return \Laminas\View\Model\JsonModel
     */
    private function saveNewsSeo($cnews_id)
    {       
        $translator = $this->getServiceManager()->get('translator');        
        // Get the form properly loaded               
        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');  
        $seoFormConfig = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_seo_form', 'meliscmsnews_seo_form');
        $melisNewsSeoTable = $this->getServiceManager()->get('MelisCmsNewsSeoTable');
        $melisNewsTextTable = $this->getServiceManager()->get('MelisCmsNewsTextsTable');
        // Check if post
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postValues = $request->getPost()->toArray();
            $languages = $this->getOrderedLanguagesByCurrentLocale();
            $languageCount = count($languages);
            if ($languages) {
                foreach ($languages as $lang) {
                    // Generating form for each language
                    $seoFormtmp = $factory->createForm($seoFormConfig);
                    //loop through the language form data
                    $ctr = 1;
                    foreach ($postValues['cnews_seo'] as $val) {  
                        if ($val['cnews_seo_lang_id'] && $val['cnews_seo_lang_id'] == $lang['lang_cms_id']) {   
                            if (!empty($val['cnews_seo_url'])) {
                                $val['cnews_seo_url'] = $this->cleanURL($val['cnews_seo_url']);
                            }
                            if (!empty($val['cnews_seo_canonical'])) {
                                $val['cnews_seo_canonical'] = $this->cleanURL($val['cnews_seo_canonical']);
                            }
                            if (!empty($val['cnews_seo_url_redirect'])) {
                                $val['cnews_seo_url_redirect'] = $this->cleanURL($val['cnews_seo_url_redirect']);
                            }
                            if (!empty($val['cnews_seo_url_301'])) {
                                $val['cnews_seo_url_301'] = $this->cleanURL($val['cnews_seo_url_301']);
                            }
                            $seoFormtmp->setData($val);    
                            break;                
                        }             
                    }   
                    if ($seoFormtmp->isValid()) {                       
                        $seoData = $seoFormtmp->getData();
                        $allEmpty = true;
                        if (!empty($seoData['cnews_seo_meta_title']) || !empty($seoData['cnews_seo_meta_description']) || !empty($seoData['cnews_seo_url']) || !empty($seoData['cnews_seo_canonical']) || !empty($seoData['cnews_seo_url_redirect']) || !empty($seoData['cnews_seo_url_301'])) {
                            $allEmpty = false;
                        }
                        $success = 1;
                        $seoData['cnews_id'] = $cnews_id;//set the news id
                        //if at least one seo property is filled up, continue
                        if (!$allEmpty)  {
                            // Check for unicity of the URL declared
                            if (!empty($seoData['cnews_seo_url'])) {
                                $newsSeo = $melisNewsSeoTable->checkSeoUrlDuplicates($seoData['cnews_seo_url'], $postValues['cnews_site_id'])->current();
                                if (!empty($newsSeo)) {
                                    // Not this page of course
                                    if (empty($seoData['cnews_seo_id']) || (!empty($seoData['cnews_seo_id']) && $seoData['cnews_seo_id'] != $newsSeo->cnews_seo_id)) {
                                        $newsTitleDuplicate = '';                                      
                                        $newsText = $melisNewsTextTable->getEntryByField('cnews_id', $newsSeo->cnews_id)->current();
                                        //get all languages available in the plaftform
                                        $cmsLangTable = $this->getServiceManager()->get('MelisEngineTableCmsLang');
                                        $languages = $cmsLangTable->getEntryByField('lang_cms_id', $newsSeo->cnews_seo_lang_id)->current();
                                        if (!empty($newsText))
                                            $newsTitleDuplicate = $newsText->cnews_title;                                      
                                        // This URL is already used somewhere else
                                        return array(
                                                'success' => 0,                                               
                                                'errors' => array( 'cnews_seo_url' => 
                                                                array(
                                                                        'cnews_seo_url' => $translator->translate('tr_meliscmsnews_page_tab_seo_error_duplicate_url') . ' '. $newsTitleDuplicate,
                                                                        'label' => $translator->translate('tr_meliscmsnews_page_tab_seo_error_label_seo_url'). '('.$lang["lang_cms_name"].')'
                                                                    )
                                                                )
                                        );
                                    }
                                }
                                //check also from page seo table                                
                                $melisTablePageSeo = $this->getServiceManager()->get('MelisEngineTablePageSeo');
                                $datasPageSeo = $melisTablePageSeo->getEntryByField('pseo_url', $seoData['cnews_seo_url']);
                                if (!empty($datasPageSeo))
                                {
                                    $datasPageSeo = $datasPageSeo->current();
                                    if (!empty($datasPageSeo))
                                    {                                       
                                        $pageNameDuplicate = '';
                                        $melisPage = $this->getServiceManager()->get('MelisEnginePage');
                                        $datasPage = $melisPage->getDatasPage($datasPageSeo->pseo_id, 'saved');
                                        $pageTree = $datasPage->getMelisPageTree();
                                        if (!empty($pageTree))
                                            $pageNameDuplicate = ' ' . $pageTree->page_name . ' (' . $datasPageSeo->pseo_id . ')';
                                        // This URL is already used somewhere else
                                        return array(
                                                'success' => 0,                                              
                                                'errors' => array( 'cnews_seo_url' =>
                                                                array(
                                                                        'cnews_seo_url' => $translator->translate('tr_meliscmsnews_page_tab_seo_error_duplicate_url') . $pageNameDuplicate,
                                                                        'label' => $translator->translate('tr_meliscmsnews_page_tab_seo_error_label_seo_url'). '('.$lang["lang_cms_name"].')'
                                                                    )
                                                                )
                                        );
                                    }
                                }                              
                            }
                            // Cleaning special char and white spaces on SEO Url
                            $enginePage = $this->getServiceManager()->get('MelisEngineTree');
                            $seoData['cnews_seo_url'] = $enginePage->cleanString(mb_strtolower($seoData['cnews_seo_url']));
                            // Checking for spaces
                            if (preg_match('/\s/', $seoData['cnews_seo_url'])) {
                                $seoData['cnews_seo_url'] = str_replace(" ", "", $seoData['cnews_seo_url']);
                            }
                            $res = $melisNewsSeoTable->save($seoData, $seoData['cnews_seo_id']);
                            if (!$res) {
                                $success = 0;
                            }
                        } else {
                            if (!empty($seoData['cnews_seo_id'])) {
                                 // All fields are empty, let's delete the entry
                                $res = $melisNewsSeoTable->deleteById($seoData['cnews_seo_id']);
                                if (!$res) {
                                    $success = 0;
                                }
                            }
                        }
                        $result = array(
                            'success' => $success,
                            'errors' => array(),
                        );
                    } else {
                        // Add labels of errors
                        $errors = $seoFormtmp->getMessages();
                        $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');                       
                        $seoFormConfig = $seoFormConfig['elements'];
                        foreach ($errors as $keyError => $valueError) {
                            foreach ($seoFormConfig as $keyForm => $valueForm) {                          
                                if ($valueForm['spec']['name'] == $keyError &&
                                        !empty($valueForm['spec']['options']['label'])){
                                            $errors[$keyError]['label'] = $valueForm['spec']['options']['label']. '('.$lang["lang_cms_name"].')';
                                            //$errors[$keyError][key($valueError)] = sprintf(current($valueError), $lang['lang_cms_name']);
                                        }
                            }
                        }
                        return array(
                                'success' => 0,                                               
                                'errors' => $errors
                        );
                    }   
                }//end foreach language
            } // end if languages       
        } else {
            $result = array(
                    'success' => 0,
                    'errors' => array(array('empty' => $translator->translate('tr_meliscms_form_common_errors_Empty datas'))),
            );
        }
        return $result;
    }
    /**
     * Rids the URL from special characters -> reference: MelisCms/PageSeoController
     * @param string $url
     * @return mixed
     */
    private function cleanURL(string $url = '')
    {
        $url = str_replace(' ', '-', $url);                 // Replaces all spaces with hyphens
        $url = preg_replace('/[^A-Za-z0-9\/\-]+/', '-', $url);  // Replaces special characters with hyphens
        // remove "/" prefix on generated URL
        if (substr($url, 0, 1) == '/') {
            return preg_replace('/\//', '', $url, 1);
        }
        return $url;
    }
}