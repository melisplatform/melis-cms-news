<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller\Plugin;


use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use Laminas\Form\Factory;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

/**
 * This plugin implements the business logic of the
 * "ShowNews" plugin.
 *
 * Please look inside app.plugins.php for possible awaited parameters
 * in front and back function calls.
 *
 * front() and back() are the only functions to create / update.
 * front() generates the website view
 *
 * Configuration can be found in $pluginConfig / $pluginFrontConfig / $pluginBackConfig
 * Configuration is automatically merged with the parameters provided when calling the plugin.
 * Merge detects automatically from the route if rendering must be done for front or back.
 *
 * How to call this plugin without parameters:
 * $plugin = $this->MelisCmsNewsShowNewsPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisCmsNewsShowNewsPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/news/shownews'
 * );
 * $pluginView = $plugin->render($parameters);
 *
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'shownews');
 *
 * How to display in your controller's view:
 * echo $this->shownews;
 *
 *
 */
class MelisCmsNewsShowNewsPlugin extends MelisTemplatingPlugin
{

    public function __construct($updatesPluginConfig = [])
    {
        $this->configPluginKey = 'meliscmsnews';
        $this->pluginXmlDbKey = 'MelisCmsNewsShowNews';
        parent::__construct($updatesPluginConfig);
    }

    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        $data = $this->getFormData();

        // Properties
        $newsId = !empty($data['newsId']) ? $data['newsId'] : null;

        $newsData = [];
        /** @var \MelisCmsNews\Service\MelisCmsNewsService $newsSrv */
        $newsSrv = $this->getServiceManager()->get('MelisCmsNewsService');

        $container = new Container('melisplugins');
        $langId = $container['melis-plugins-lang-id'];

        if (is_null($newsId)) {
            /**
             * Getting the current page id
             */
            $pageId = (!empty($data['pageId'])) ? $data['pageId'] : $this->getController()->params()->fromRoute('idpage');

            /**
             * Retrieving the page Site id to get the
             * list of the current site news
             */
            $pageTreeService = $this->getServiceManager()->get('MelisEngineTree');
            $site = $pageTreeService->getSiteByPageId($pageId);

            if (!empty($site)) {
                // Retrieve most recent news as default
                $newsData = $newsSrv->getNewsList(1, $langId, null, null, null, date('Y-m-d H:i:s'), true, null, 1, 'cnews_id', 'DESC', $site->site_id);
                if (!empty($newsData[0])) {
                    $newsData = $newsData[0];
                }
            }
        } else {
            $newsDataRes = $newsSrv->getNewsById($newsId, $langId);

            if (!empty($newsDataRes)) {
                // publish date is not greater than today
                if (!empty($newsDataRes->cnews_publish_date) && strtotime($newsDataRes->cnews_publish_date) <= strtotime('now')) {
                    // unpublish date is not greater than today
                    if (empty($newsDataRes->cnews_unpublish_date) || strtotime($newsDataRes->cnews_unpublish_date) > strtotime('now')) {
                        // check if active
                        if ($newsDataRes->cnews_status) {
                            $newsData = $newsDataRes;
                        }
                    }
                }
            }

            /** Return news data for Preview Tab */
            if (!empty($data['renderMode']) && $data['renderMode'] === "previewtab") {
                $newsData = $newsDataRes;
            }
        }
        # add user account
        $activeModules = $this->getServiceManager()->get('MelisAssetManagerModulesService')->getActiveModules();
        $userAccountModuleIsLoaded = in_array('MelisCmsUserAccount', $activeModules);
        if ($userAccountModuleIsLoaded) {
            //get Author data
            $authorId = $newsData->cnews_author_account ?? null;
            $userSiteService = $this->getServiceManager()->get('FrontUserAccountService');
            $authorInfo = $userSiteService->getUserById($authorId);
            if (!empty($authorInfo)) {
                # remove password
                unset($authorInfo['uac_password']);
                $newsData->cnews_author_account = $authorInfo;
            }
        }
        //sort paragraphs based on the defined order
        $newsData = (array) $newsData;
        $paragraphOrder = $newsData['cnews_paragraph_order'] ?? null; //the paragraph order defined in db
        $paragraphFields = array('cnews_paragraph1', 'cnews_paragraph2', 'cnews_paragraph3', 'cnews_paragraph4','cnews_paragraph5','cnews_paragraph6','cnews_paragraph7','cnews_paragraph8','cnews_paragraph9','cnews_paragraph10');
        $paragraphOrder = !empty($paragraphOrder) ? explode('-', $paragraphOrder) : $paragraphFields;

        $ctr = 0;
        $sortedNewsData = array();
        foreach ($paragraphOrder as $field) {
            $sortedNewsData[$paragraphFields[$ctr]] = $newsData[$field] ?? null;
            $ctr++;
        }        
        $finalData = array_merge($newsData, $sortedNewsData);
        // Create an array with the variables that will be available in the view
        $viewVariables = [
            'pluginId' => $data['id'],
            'news' => $finalData
        ];

        // return the variable array and let the view be created
        return $viewVariables;
    }

    /**
     * Returns the data to populate the form inside the modals when invoked
     * @return array|bool|null
     */
    public function getFormData()
    {
        $data = parent::getFormData();

        return $data;
    }

    /**
     * This function generates the form displayed when editing the parameters of the plugin
     * @return array
     */
    public function createOptionsForms()
    {
        // construct form
        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $formConfig = $this->pluginBackConfig['modal_form'];

        $response = [];
        $render = [];
        if (!empty($formConfig)) {
            foreach ($formConfig as $formKey => $config) {
                $form = $factory->createForm($config);

                /** Properties Tab: Set the value options for default news */
                if ($formKey === 'melis_cms_news_list_plugin_template_form') {
                    $pluginData = $this->getFormData();
                    $container = new Container('melisplugins');
                    $langId = $container['melis-plugins-lang-id'];

                    $siteId = $this->getSiteIdByPageId($pluginData['pageId']);

                    /** @var \MelisCmsNews\Service\MelisCmsNewsService $newsSvc */
                    $newsSvc = $this->getServiceManager()->get('MelisCmsNewsService');
                    $posts = $newsSvc->getNewsList(
                        1,
                        null,
                        null,
                        null,
                        null,
                        null,
                        1,
                        null,
                        null,
                        'cnews_title',
                        'ASC',
                        $siteId,
                        null
                    );

                    $posts = $this->processNewsLists($posts, $langId);

                    $valueOptions = [];
                    foreach ($posts as $post) {
                        $valueOptions[$post['cnews_id']] = $post['site_label'] . ' - ' . $post['cnews_title'];
                    }
                    $form->get('newsId')->setValueOptions($valueOptions);
                }

                $request = $this->getServiceManager()->get('request');
                $parameters = $request->getQuery()->toArray();

                if (!isset($parameters['validate'])) {
                    $form->setData($this->getFormData());
                    $viewModelTab = new ViewModel();
                    $viewModelTab->setTemplate($config['tab_form_layout']);
                    $viewModelTab->modalForm = $form;
                    $viewModelTab->formData = $this->getFormData();
                    $viewRender = $this->getServiceManager()->get('ViewRenderer');
                    $html = $viewRender->render($viewModelTab);
                    array_push($render, [
                            'name' => $config['tab_title'],
                            'icon' => $config['tab_icon'],
                            'html' => $html
                        ]
                    );
                } else {

                    // validate the forms and send back an array with errors by tabs
                    $post = $request->getPost()->toArray();
                    $success = false;
                    $form->setData($post);

                    if ($form->isValid()) {
                        $success = true;
                        array_push($response, [
                            'name' => $this->pluginBackConfig['modal_form'][$formKey]['tab_title'],
                            'success' => $success,
                        ]);
                    } else {
                        $errors = $form->getMessages();

                        foreach ($errors as $keyError => $valueError) {
                            foreach ($config['elements'] as $keyForm => $valueForm) {
                                if ($valueForm['spec']['name'] == $keyError &&
                                    !empty($valueForm['spec']['options']['label'])
                                )
                                    $errors[$keyError]['label'] = $valueForm['spec']['options']['label'];
                            }
                        }

                        array_push($response, [
                            'name' => $this->pluginBackConfig['modal_form'][$formKey]['tab_title'],
                            'success' => $success,
                            'errors' => $errors,
                            'message' => '',
                        ]);
                    }

                }
            }
        }

        if (!isset($parameters['validate'])) {
            return $render;
        } else {
            return $response;
        }

    }

    /**
     * This method will decode the XML in DB to make it in the form of the plugin config file
     * so it can overide it. Only front key is needed to update.
     * The part of the XML corresponding to this plugin can be found in $this->pluginXmlDbValue
     */
    public function loadDbXmlToPluginConfig()
    {
        $configValues = [];

        $xml = simplexml_load_string($this->pluginXmlDbValue);
        if ($xml) {
            if (!empty($xml->template_path))
                $configValues['template_path'] = (string)$xml->template_path;
            if (!empty($xml->newsId))
                $configValues['newsId'] = (string)$xml->newsId;
        }

        return $configValues;
    }

    /**
     * This method saves the XML version of this plugin in DB, for this pageId
     * Automatically called from savePageSession listenner in PageEdition
     */
    public function savePluginConfigToXml($parameters)
    {
        $xmlValueFormatted = '';

        // template_path is mendatory for all plugins
        if (!empty($parameters['template_path']))
            $xmlValueFormatted .= "\t\t" . '<template_path><![CDATA[' . $parameters['template_path'] . ']]></template_path>';
        if (!empty($parameters['newsId']))
            $xmlValueFormatted .= "\t\t" . '<newsId><![CDATA[' . $parameters['newsId'] . ']]></newsId>';

        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
    }

    /**
     * Function to reprocess the news lists
     * according to correct language
     *
     * @param $lists
     * @param $langId
     * @return array
     */
    private function processNewsLists($lists, $langId){
        $newsCorrectLang = [];
        $newsDiffLang = [];

        /**
         * separate the correct news (language)
         */
        foreach($lists as $key => $news){
            if($langId == $news['cnews_lang_id']){
                array_push($newsCorrectLang, $news);
            }else{
                array_push($newsDiffLang, $news);
            }
        }

        /**
         * remove other news from other language
         */
        foreach($newsDiffLang as $key => $diffNews){
            foreach($newsCorrectLang as $k => $corrNews){
                if($diffNews['cnews_id'] == $corrNews['cnews_id']){
                    unset($newsDiffLang[$key]);
                }
            }
        }

        /**
         * merge and sort the final lists
         */
        $finalNewsLists = array_merge($newsCorrectLang, $newsDiffLang);
        usort($finalNewsLists, function($a, $b) {
            return $a['cnews_title'] <=> $b['cnews_title'];
        });

        return $finalNewsLists;
    }

    /**
     * Get site id
     *
     * @param $pageId
     * @return int
     */
    private function getSiteIdByPageId($pageId)
    {
        $siteId = 0;

        $pageSaved = $this->getServiceManager()->get('MelisEngineTablePageSaved');
        $pagePublished = $this->getServiceManager()->get('MelisEngineTablePagePublished');
        $template = $this->getServiceManager()->get('MelisEngineTableTemplate');

        if(!empty($pageId)){
            /**
             * check first if there is data on page saved
             */
            $pageSavedData = $pageSaved->getEntryById($pageId)->current();
            if(!empty($pageSavedData)){
                $tplId = $pageSavedData->page_tpl_id;
            }else{
                //try to get the data from the page published
                $pagePublishedData = $pagePublished->getEntryById($pageId)->current();
                $tplId = $pagePublishedData->page_tpl_id;
            }

            if(!empty($tplId)){
                $tplData = $template->getEntryById($tplId)->current();
                if(!empty($tplData)){
                    $siteId = $tplData->tpl_site_id;
                }
            }
        }

        return $siteId;
    }
}
