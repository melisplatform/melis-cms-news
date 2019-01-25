<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller;


use Zend\File\Transfer\Adapter\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

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
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        return $view;
    }

    /**
     * sets the news letter data to the layout
     * @param unknown $couponId
     */
    private function setTableVariables($newsId)
    {
        $layoutVar = array();
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');
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
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsPageContentAction()
    {
        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', 0);
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
        $newsId = (int)$this->params()->fromQuery('newsId', '');
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
        $newsId = (int)$this->params()->fromQuery('newsId', '');
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
        $newsId = (int)$this->params()->fromQuery('newsId', '');
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
        if (!empty($this->layout()->news)) {
            if ($this->layout()->news->cnews_status) {
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
     * renders the tabs content header title
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftAction()
    {
        /**
         * True when MelisCmsComments module is DISABLED
         */
        $commentsModuleIsDisabled = !in_array('MelisCmsComments', $this->getServiceLocator()->get('MelisAssetManagerModulesService')->getActiveModules());

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
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftPropertiesAction()
    {
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $tool = $this->getServiceLocator()->get('MelisCoreTool');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_properties_form', 'meliscmsnews_properties_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        if (!empty($this->layout()->news)) {
            $newsData = (array)$this->layout()->news;
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
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftDocumentsAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);

        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $parConf = $melisCoreConfig->getItem('meliscmsnews/conf/documents_conf/');
        $limit = $parConf['max'];
        $name = $parConf['name'];
        $documents = array();

        if (!empty($this->layout()->news)) {
            for ($c = 1; $c <= $limit; $c++) {
                $tmp = $name . $c;
                if ($this->layout()->news->$tmp) {
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
     * renders the tab left content site select
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftSitesAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);

        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');

        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_site_select_form', 'meliscmsnews_site_select_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        if (!empty($this->layout()->news)) {
            $form->setData((array)$this->layout()->news);
        }

        $view = new ViewModel();
        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->form = $form;
        return $view;
    }

    /**
     * renders the tab left content documents
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsLeftImagesAction()
    {
        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $this->setTableVariables($newsId);

        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $parConf = $melisCoreConfig->getItem('meliscmsnews/conf/images_conf/');
        $limit = $parConf['max'];
        $name = $parConf['name'];
        $images = array();

        if (!empty($this->layout()->news)) {
            for ($c = 1; $c <= $limit; $c++) {
                $tmp = $name . $c;
                if ($this->layout()->news->$tmp) {
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
        $view->limit = $limit;
        return $view;
    }

    /**
     * renders the tab right content paragraphs
     * @return \Zend\View\Model\ViewModel
     */
    public function renderNewsTabsPropertiesDetailsRightParagraphsAction()
    {
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $forms = array();

        //$formsTitleSubtitle = $this->getFormData($melisCoreConfig, 'MelisCmsNews/forms/meliscmsnews_site_title_subtitle_form', 'meliscmsnews_site_title_subtitle_form');
        /**
         * Overriding the Title element's label: Added asterisk to emphasize as mandatory field
         */
        $titleFormConfig = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_site_title_subtitle_form', 'meliscmsnews_site_title_subtitle_form');
        $titleLabel = $titleFormConfig['elements'][0]['spec']['options']['label'];
        $titleFormConfig['elements'][0]['spec']['options']['label'] = $titleLabel . ' *';
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $formsTitleSubtitle = $factory->createForm($titleFormConfig);

        $newsTextsTable = $this->getServiceLocator()->get('MelisCmsNewsTextsTable');

        $data = ($newsId) ? (array)$newsTextsTable->getNewsDataByCnd(['cnews_id' => $newsId])->toArray() : '';
        $lang_id = $this->getLangId();
        $melisEngineLangTable = $this->getServiceLocator()->get('MelisEngineTableCmsLang');
        $melisEngineLang = $melisEngineLangTable->fetchAll();
        $languages = $melisEngineLang->toArray();

        $view = new ViewModel();
        $melisKey = $this->params()->fromRoute('melisKey', '');

        $view->melisKey = $melisKey;
        $view->newsId = $newsId;
        $view->forms = $forms;
        $view->languages = $languages;
        $view->lang_id = $lang_id;
        $view->data = $data;
        $view->formsTitleSubtitle = $formsTitleSubtitle;

        return $view;
    }

    private function getFormData($config, $formPath, $formName)
    {
        $appConfigForm = $config->getFormMergedAndOrdered($formPath, $formName);

        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);

        return $factory->createForm($appConfigForm);
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
            $melisEngineLangTable = $this->getServiceLocator()->get('MelisEngineTableCmsLang');
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
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $response = [];
        $id = null;
        $success = 0;
        $errors = [];
        $dateErrors = [];
        $data = [];
        $textMessage = 'tr_meliscmsnews_save_fail';
        $textTitle = 'tr_meliscmsnews_list_header_title';
        $logTypeCode = '';
        $postValues = [];
        /** @var \MelisCmsNews\Service\MelisCmsNewsService $newsSvc */
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');
        $tool = $this->getServiceLocator()->get('MelisCoreTool');

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

            $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
            $form = $this->getFormData($melisCoreConfig, 'MelisCmsNews/forms/meliscmsnews_properties_form', 'meliscmsnews_properties_form');

            if (!empty($postValues['cnews_publish_date']) && !empty($postValues['cnews_unpublish_date'])) {
                // convert date to generic for date comparison
                $pubDate = $tool->localeDateToSql($postValues['cnews_publish_date'], '', 'en_EN');
                $unpubDate = $tool->localeDateToSql($postValues['cnews_unpublish_date'], '', 'en_EN');

                if (strtotime($pubDate) > strtotime($unpubDate)) {
                    $dateErrors['cnews_unpublish_date'] = [
                        'isGreaterThan' => $this->getTool()->getTranslation('tr_meliscmsnews_form_unpublish_error'),
                        'label' => $this->getTool()->getTranslation('tr_meliscmsnews_form_unpublish'),
                    ];
                }
            }

            if (empty($postValues['cnews_id'])) {
                $postValues['cnews_creation_date'] = date("Y-m-d H:i:s");
            }

            if (empty($postValues['cnews_publish_date'])) {
                $postValues['cnews_publish_date'] = date("Y-m-d H:i:s");

                $pubDate = $tool->localeDateToSql($postValues['cnews_publish_date'], '', 'en_EN');
                $unpubDate = $tool->localeDateToSql($postValues['cnews_unpublish_date'], '', 'en_EN');

                if (!empty($postValues['cnews_publish_date']) && !empty($postValues['cnews_unpublish_date'])) {
                    if (strtotime($pubDate) > strtotime($unpubDate)) {
                        $dateErrors['cnews_unpublish_date'] = [
                            'isGreaterThan' => $this->getTool()->getTranslation('tr_meliscmsnews_form_unpublish_error_date_today'),
                            'label' => $this->getTool()->getTranslation('tr_meliscmsnews_form_unpublish'),
                        ];
                    }
                }
            }

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

            if ($form->isValid() && $titleExist) {
                $newsTbl = $form->getData();

                $cnews_id = $newsTbl['cnews_id'];
                $newsTbl['cnews_status'] = $postValues['cnews_status'];
                $newsTbl['cnews_slider_id'] = empty($postValues['cnews_slider_id']) ? NULL : $postValues['cnews_slider_id'];
                $newsTbl['cnews_site_id'] = empty($postValues['cnews_site_id']) ? NULL : $postValues['cnews_site_id'];
                unset($newsTbl['cnews_id']);
                $newsTbl['cnews_publish_date'] = $tool->localeDateToSql($newsTbl['cnews_publish_date']);
                $newsTbl['cnews_unpublish_date'] = $tool->localeDateToSql($newsTbl['cnews_unpublish_date']);

                $data['cnews_id'] = $newsSvc->saveNews($newsTbl, $cnews_id);

                $lang_id = $this->getLangId();
                $data['cnews_title'] = '';

                if ($data['cnews_id']) {
                    for ($i = 0; $i < (int)$postValues['formCount']; $i++) {
                        if ($postValues['cnews_lang_id'][$i] == $lang_id) {
                            $data['cnews_title'] = $postValues['cnews_title'][$i];
                        }

                        $newsTxt['cnews_title'] = $postValues['cnews_title'][$i];
                        $newsTxt['cnews_subtitle'] = $postValues['cnews_subtitle'][$i];
                        $newsTxt['cnews_lang_id'] = $postValues['cnews_lang_id'][$i];

                        // get all paragraph values
                        for ($c = 1; $c <= 4; $c++) {
                            $newsTxt['cnews_paragraph' . $c] = $postValues['cnews_paragraph' . $c][$i];
                        }

                        // get news title to be displayed on new tab
                        if (empty($data['cnews_title'])) {
                            $data['cnews_title'] = !empty($postValues['cnews_title'][0]) ? $postValues['cnews_title'][0] : $postValues['cnews_title'][1];
                        }

                        /** @var \MelisCmsNews\Model\Tables\MelisCmsNewsTable $newsTable */
                        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');
                        /** @var \MelisCmsNews\Model\Tables\MelisCmsNewsTextsTable $newsTextsTable */
                        $newsTextsTable = $this->getServiceLocator()->get('MelisCmsNewsTextsTable');

                        $lastNews = $newsTable->getLastNews()->current();

                        if (empty($postValues['cnews_id'])) {
                            $lastNews = (array)$lastNews;
                            $newsTxt['cnews_id'] = $lastNews['cnews_id'];

                            $newsTextsTable->save($newsTxt);
                        } else {
                            $newsTextsTable->updateNewsText(
                                $newsTxt, [
                                    'cnews_id' => $postValues['cnews_id'],
                                    'cnews_lang_id' => $newsTxt['cnews_lang_id']
                                ]
                            );
                        }
                    }
                }

                if ($data['cnews_id']) {
                    $id = $data['cnews_id'];
                    $textMessage = 'tr_meliscmsnews_save_success';
                    $success = 1;
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

                $errors = array_merge($errors, $dateErrors);
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
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');

        return $melisTool;
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
        $view->setTerminal(true);
        return $view;
    }

    /**
     * renders the order list modal for updating order status
     * @return \Zend\View\Model\ViewModel
     */
    public function renderModalFormAction()
    {
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_file_form', 'meliscmsnews_file_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        $file = '';
        $default = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAAPAAA/+EDLWh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS41LWMwMTQgNzkuMTUxNDgxLCAyMDEzLzAzLzEzLTEyOjA5OjE1ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozRkNFMzU3RDg2QUYxMUU1OEM4OENCQkI2QTc0MTkwRSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozRkNFMzU3Qzg2QUYxMUU1OEM4OENCQkI2QTc0MTkwRSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChNYWNpbnRvc2gpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDEwNzlDODNCQThDMTFFMjg5NTlFMDAzODgzMjZDMkIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDEwNzlDODRCQThDMTFFMjg5NTlFMDAzODgzMjZDMkIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAAGBAQEBQQGBQUGCQYFBgkLCAYGCAsMCgoL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mhNky5fTkBYwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/9k=';

        $melisKey = $this->params()->fromRoute('melisKey', '');
        $newsId = (int)$this->params()->fromQuery('newsId', '');
        $isNew = $this->params()->fromQuery('isNew', '');
        $type = $this->params()->fromQuery('type', '');

        $column = $this->params()->fromQuery('column');

        if (!$isNew) {
            $this->setTableVariables($newsId);
            $file = $this->layout()->news->$column;
        }

        $data = array(
            'cnews_id' => $newsId,
            'type' => $type,
            'cnews_document' => $file,
            'column' => $column,

        );

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
        $this->getEventManager()->trigger('meliscmsnews_save_news_file_start', $this, array());
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');

        $id = null;
        $response = array();
        $success = 0;
        $errors = array();
        $data = array();
        $textMessage = 'tr_meliscmsnews_save_file_fail';
        $textTitle = 'tr_meliscmsnews_list_header_title';
        $logTypeCode = '';
        $postValues = [];
        if ($this->getRequest()->isPost()) {
            $postValues = get_object_vars($this->getRequest()->getPost());
            $file = $this->attachmentValidator();

            if ($file['type'] == 'image') {
                $logTypeCode = 'CMS_NEWS_UPLOAD_IMG';
            } else {
                $logTypeCode = 'CMS_NEWS_UPLOAD_FILE';
            }

            if (empty($file['errors'])) {
                $id = $postValues['cnews_id'];
                $news = $newsSvc->getNewsById($postValues['cnews_id']);


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
                            $data = array(
                                $tmp => $file['fileName']
                            );
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

        $response = array(
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors,
            'chunk' => $postValues,
        );

        $this->getEventManager()->trigger('meliscmsnews_save_news_file_end',
            $this, array_merge($response, array('typeCode' => $logTypeCode, 'itemId' => $id)));

        return new JsonModel($response);
    }

    private function attachmentValidator()
    {
        $melisCoreConfig = $this->getServiceLocator()->get('MelisCoreConfig');
        $confUpload = $melisCoreConfig->getItem('meliscmsnews/conf/files');
        $minSize = $confUpload['minUploadSize'];
        $maxSize = $confUpload['maxUploadSize'];
        $conPath = $confUpload['imagesPath'];
        $upload = false;
        $textMessage = '';
        $newFileName = '';
        $type = 'file';

        //prepare validators
        $size = new Size(array(
            'min' => $minSize,
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
        if ($postValues['type'] == 'image') {
            $type = 'image';
            $validator = array($size, $imageValidator);
        } else {
            $validator = array($size);
        }

        if (!emptY($uploadedFile['name'])) {
            //format name
            $fileName = $uploadedFile['name'];

            //create folder based on news id
            if ($this->createFolder($postValues['cnews_id'])) {
                $adapter = new Http();

                // do saving
                $adapter->setValidators($validator, $fileName);

                if ($adapter->isValid()) {

                    $adapter->setDestination('public' . $conPath . $postValues['cnews_id'] . '/');
                    $newFileName = $this->renameIfDuplicateFile($conPath . $postValues['cnews_id'] . '/' . $fileName);
                    $savedDocFileName = 'public' . $newFileName;
                    $adapter->addFilter('File\Rename', array(
                        'target' => $savedDocFileName,
                        'overwrite' => true,
                    ));

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
                $textMessage = $this->getTool()->getTranslation('tr_meliscmsnews_save_upload_file_path_rights_error');
            }
        } else {
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

    private function formatBytes($bytes)
    {
        $size = $bytes;
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return round(number_format($size / pow(1024, $power), 2, '.', ',')) . ' ' . $units[$power];
    }

    /**
     * Creates a folder inside "public/media/commerce" with full permission (for now)
     * @param String $folderType , enum: category, product, variant
     * @param int $folderId
     * @return bool
     */
    private function createFolder($id)
    {
        $status = false;
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
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');
        $docData = $newsTable->checkForDuplicates(pathinfo($filePath, PATHINFO_DIRNAME) . '/' . pathinfo($filePath, PATHINFO_FILENAME));
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
        $this->getEventManager()->trigger('meliscmsnews_delete_news_file_start', $this, array());
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $response = array();
        $id = null;
        $success = 0;
        $errors = array();
        $data = array();
        $textTitle = 'tr_meliscmsnews_common_label_remove_file';
        $textMessage = 'tr_meliscore_error_message';
        $logTypeCode = '';
        $type = null;

        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');

        if ($this->getRequest()->isPost()) {
            $postValues = get_object_vars($this->getRequest()->getPost());
            $id = $postValues['newsId'];
            $type = $postValues['type'];

            if (in_array($type, array('file', 'image'))) {
                $logTypeCode = 'CMS_NEWS_' . strtoupper($type) . '_DELETE';
                $textTitle = 'tr_meliscmsnews_delete_' . $type . '_title';
            }

            $tmp = $newsSvc->getNewsById($postValues['newsId']);
            $data = array(
                $postValues['column'] => ''
            );

            if ($newsSvc->saveNews($data, $postValues['newsId'])) {
                $success = 1;

                if (in_array($type, array('file', 'image'))) {
                    $textMessage = 'tr_meliscmsnews_delete_' . $type . '_success';
                }

                $col = $postValues['column'];
                $fileUploadPath = 'public' . $tmp->$col;
                $this->deleteFileFromDirectory($fileUploadPath);
            }
        }

        if ($success == 0) {
            if (in_array($type, array('file', 'image'))) {
                $textMessage = 'tr_meliscmsnews_delete_' . $type . '_unable';
            }
        }

        $response = array(
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors,
            'chunk' => $data,
        );

        $this->getEventManager()->trigger('meliscmsnews_delete_news_file_end',
            $this, array_merge($response, array('typeCode' => $logTypeCode, 'itemId' => $id)));

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
        $errors = array();
        $data = array();
        $newsTable = $this->getServiceLocator()->get('MelisCmsNewsTable');
        $newsSvc = $this->getServiceLocator()->get('MelisCmsNewsService');

        if ($this->getRequest()->isPost()) {
            $postValues = get_object_vars($this->getRequest()->getPost());
            foreach ($newsTable->getEntryByField('cnews_slider_id', $postValues['sliderId']) as $news) {
                $tmp = array(
                    'cnews_slider_id' => NULL
                );
                $newsSvc->saveNews($tmp, $news->cnews_id);
                $success = 1;
            }

        }

        $results = array(
            'success' => $success,
            'errors' => $errors,
            'datas' => $data,
        );
        return new JsonModel($results);
    }

    public function getFormattedFileName($fileName)
    {
        $file = pathinfo($fileName, PATHINFO_FILENAME);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = $file;

        return $newFileName;
    }
}
