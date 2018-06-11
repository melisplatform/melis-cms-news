<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller\Plugin;

use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
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

    public function __construct($updatesPluginConfig = array())
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
        $active             = true;
        $filterPublish      = true;
        $filterUnpublish    = true;
        $newsId             = !empty($data['newsId']) ? $data['newsId']   : null;
        
        $newsData = array();
        // Retreiving News using MelisCmsNewsService
        $newsSrv = $this->getServiceLocator()->get('MelisCmsNewsService');
        
        $container = new Container('melisplugins');
        $langId = $container['melis-plugins-lang-id'];
        
        if(is_null($newsId)){
            /**
             * Getting the current page id
             */
            $pageId = (!empty($data['pageId'])) ? $data['pageId'] : $this->getController()->params()->fromRoute('idpage');
            
            /**
             * Retrieving the page Site id to get the 
             * list of the current site news
             */
            $pageTreeService = $this->getServiceLocator()->get('MelisEngineTree');
            $site = $pageTreeService->getSiteByPageId($pageId);
            
            if (!empty($site))
            {
                // Retrieve most recent news as default
                $newsData = $newsSrv->getNewsList(1, $langId, null, null, null, date('Y-m-d H:i:s'), true, null, 1, 'cnews_id', 'DESC', $site->site_id);
                if (!empty($newsData[0]))
                {
                    $newsData = $newsData[0];
                }
            }
        }else{
            
            $newsDataRes = $newsSrv->getNewsById($newsId, $langId);
            
            if (!empty($newsDataRes))
            {
                // publish date is not greater than today
                if (!empty($newsDataRes->cnews_publish_date) && strtotime($newsDataRes->cnews_publish_date) <= strtotime('now'))
                {
                    // unpublish date is not greater than today
                    if (empty($newsDataRes->cnews_unpublish_date) || strtotime($newsDataRes->cnews_unpublish_date) > strtotime('now'))
                    {
                        // check if active
                        if ($newsDataRes->cnews_status)
                        {
                            $newsData = $newsDataRes;
                        }
                    }
                }
            }
        }
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'pluginId' => $data['id'],
            'news' => $newsData
        );

        // return the variable array and let the view be created
        return $viewVariables;
    }

    /**
     * This function generates the form displayed when editing the parameters of the plugin
     * @return array
     */
    public function createOptionsForms()
    {
        // construct form
        $factory = new \Zend\Form\Factory();
        $formElements = $this->getServiceLocator()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $formConfig = $this->pluginBackConfig['modal_form'];

        $response = [];
        $render   = [];
        if (!empty($formConfig)) {
            foreach ($formConfig as $formKey => $config) {
                $form = $factory->createForm($config);
                $request = $this->getServiceLocator()->get('request');
                $parameters = $request->getQuery()->toArray();

                if (!isset($parameters['validate'])) {

                    $form->setData($this->getFormData());
                    $viewModelTab = new ViewModel();
                    $viewModelTab->setTemplate($config['tab_form_layout']);
                    $viewModelTab->modalForm = $form;
                    $viewModelTab->formData   = $this->getFormData();
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModelTab);
                    array_push($render, [
                            'name' => $config['tab_title'],
                            'icon' => $config['tab_icon'],
                            'html' => $html
                        ]
                    );
                }
                else {

                    // validate the forms and send back an array with errors by tabs
                    $post = $request->getPost()->toArray();
                    $success = false;
                    $form->setData($post);

                    $errors = array();
                    if ($form->isValid()) {
                        $data = $form->getData();
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
        }
        else {
            return $response;
        }

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
     * This method will decode the XML in DB to make it in the form of the plugin config file
     * so it can overide it. Only front key is needed to update.
     * The part of the XML corresponding to this plugin can be found in $this->pluginXmlDbValue
     */
    public function loadDbXmlToPluginConfig()
    {
        $configValues = array();
        
        $xml = simplexml_load_string($this->pluginXmlDbValue);
        if ($xml)
        {
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
        if(!empty($parameters['newsId']))
            $xmlValueFormatted .= "\t\t" . '<newsId><![CDATA['   . $parameters['newsId'] . ']]></newsId>';
        
        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
                            $xmlValueFormatted .
                        "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";
        
        return $xmlValueFormatted;
    }
}
