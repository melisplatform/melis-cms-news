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
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;
use Laminas\Session\Container;
use Laminas\Stdlib\ArrayUtils;
use Laminas\View\Model\ViewModel;

/**
 * This plugin implements the business logic of the
 * "ListNews" plugin.
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
 * $plugin = $this->MelisCmsNewsListNewsPlugin();
 * $pluginView = $plugin->render();
 *
 * How to call this plugin with custom parameters:
 * $plugin = $this->MelisCmsNewsListNewsPlugin();
 * $parameters = array(
 *      'template_path' => 'MySiteTest/news/listnews'
 * );
 * $pluginView = $plugin->render($parameters);
 *
 * How to add to your controller's view:
 * $view->addChild($pluginView, 'listnews');
 *
 * How to display in your controller's view:
 * echo $this->listnews;
 *
 *
 */
class MelisCmsNewsListNewsPlugin extends MelisTemplatingPlugin
{
    public function __construct($updatesPluginConfig = [])
    {
        $this->configPluginKey = 'meliscmsnews';
        $this->pluginXmlDbKey = 'MelisCmsNewsListNews';
        parent::__construct($updatesPluginConfig);
    }

    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        $container = new Container('melisplugins');
        $langId = $container['melis-plugins-lang-id'];

        // Default values
        $defLangLocale = 'en_EN';

        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        $data = $this->getFormData();

        // Properties
        $pageIdDetailNews = empty($data['pageIdNews']) ? null : $data['pageIdNews'];
        $siteId = empty($data['site_id']) ? null : $data['site_id'];

        // Pagination
        $current = empty($data['current']) ? 1 : $data['current'];
        $nbPerPage = empty($data['nbPerPage']) ? 1 : $data['nbPerPage'];
        $nbPageBeforeAfter = empty($data['nbPageBeforeAfter']) ? 0 : $data['nbPageBeforeAfter'];

        // Filters
        $status = true;
        $unpublishFilter = true;
        $orderColumn = empty($data['column']) ? 'cnews_publish_date' : $data['column'];
        $order = empty($data['order']) ? 'DESC' : $data['order'];
        $dateMin = empty($data['date_min']) ? null : $data['date_min'];
        $dateMax = empty($data['date_max']) ? null : $data['date_max'];
        $search = empty($data['search']) ? null : $data['search'];

        // convert date formats
        $dateMin = is_null($dateMin) ? null : date('Y-m-d H:i:s', strtotime($dateMin));
        $dateMax = is_null($dateMax) ? null : date('Y-m-d H:i:s', strtotime($dateMax . ' 23:59:59'));

        $pageTreeService = $this->getServiceManager()->get('MelisEngineTree');

        /**
         * Getting the current page id
         */
        $pageId = (!empty($data['pageId'])) ? $data['pageId'] : $this->getController()->params()->fromRoute('idpage');

        // If news deatils page id is null
        if (is_null($pageIdDetailNews)) {
            $pageIdDetailNews = $pageId;
        }

        // Getting the current Site id from current page id
        if (is_null($siteId)) {
            $site = $pageTreeService->getSiteByPageId($pageId);

            if (!empty($site)) {
                $siteId = $site->site_id;
            }
        }

        // Retrieving News list using MelisCmsNewsService
        /** @var \MelisCmsNews\Model\Tables\MelisCmsNewsTable $newsSrv */
        $newsSrv = $this->getServiceManager()->get('MelisCmsNewsService');
        $newsList = $newsSrv->getNewsList($status, $langId, null, null, $dateMin, $dateMax, $unpublishFilter, null, null, $orderColumn, $order, $siteId, $search);

        $listNews = [];
        foreach ($newsList As $key => $val) {

            // Generate link to news
            $newsSeoService = $this->getServiceManager()->get('MelisCmsNewsSeoService');
            $link = $newsSeoService->getPageLink($pageIdDetailNews, $val['cnews_id'], false);    
            $val['newsLink'] = $link;

            // date formated of news
            $val['newsDateFormated'] = date('d M Y', strtotime(($val['cnews_publish_date']) ? $val['cnews_publish_date'] : $val['cnews_creation_date']));

            // Adding the News Data to result variable           
            array_push($listNews, $val);
        }

        $paginator = new Paginator(new ArrayAdapter($listNews));
        $paginator->setCurrentPageNumber($current)
            ->setItemCountPerPage($nbPerPage)
            ->setPageRange(($nbPageBeforeAfter * 2) + 1);

        /**
         * Getting the page data (ex. Language Locale, etc.)
         * @var \MelisEngine\Service\MelisPageService $pageService
         */
        $langLocale = $defLangLocale;  // english by default
        $pageId = (int)$this->pluginFrontConfig['pageId'];
        if ($pageId) {
            $pageService = $this->getServiceManager()->get('MelisEnginePage');
            $pageData = $pageService->getDatasPage($pageId)->getMelisPageTree();
            if (!empty($pageData)) {
                $langLocale = empty($pageData->lang_cms_locale) ? $langLocale : $pageData->lang_cms_locale;
            }
        }

        // Create an array with the variables that will be available in the view
        $viewVariables = [
            'pluginId' => $data['id'],
            'listNews' => $paginator,
            'searchKey' => $search,
            'dateMin' => $data['date_min'],
            'dateMax' => $data['date_max'],
            'nbPageBeforeAfter' => $nbPageBeforeAfter,
            'langId' => $langId,
            'locale' => $langLocale,
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
     */
    public function createOptionsForms()
    {
        // construct form
        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $formConfig = $this->pluginBackConfig['modal_form'];
        $translator = $this->getServiceManager()->get('translator');

        $response = [];
        $render = [];

        if (!empty($formConfig)) {
            foreach ($formConfig as $formKey => $config) {
                $form = $factory->createForm($config);
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
                    /**
                     * validate the forms and send back an array with errors by tabs
                     */

                    $success = false;
                    $errors = [];

                    $post = $request->getPost()->toArray();
                    $form->setData($post);


                    if ($formKey == 'melis_cms_news_list_plugin_filter_form') {
                        if (!empty($post['date_min']) && !empty($post['date_max'])) {
                            if (strtotime($post['date_min']) > strtotime($post['date_max'])) {
                                $errors['date_max'] = [
                                    'label' => $translator->translate('tr_meliscmsnews_plugin_filter_date_range_to'),
                                    'inValidDates' => $translator->translate('tr_meliscmsnews_plugin_invalid_dates'),
                                ];
                            }
                        }
                    }

                    if ($form->isValid()) {
                        if (empty($errors)) {
                            $success = true;
                            array_push($response, [
                                'name' => $this->pluginBackConfig['modal_form'][$formKey]['tab_title'],
                                'success' => $success,
                            ]);
                        }
                    } else {
                        if (!empty($errors)) {
                            $errors = ArrayUtils::merge($errors, $form->getMessages());
                        } else {
                            $errors = $form->getMessages();
                        }
                        foreach ($errors as $keyError => $valueError) {
                            foreach ($config['elements'] as $keyForm => $valueForm) {
                                if ($valueForm['spec']['name'] == $keyError &&
                                    !empty($valueForm['spec']['options']['label'])
                                )
                                    $errors[$keyError]['label'] = $valueForm['spec']['options']['label'];
                            }
                        }
                    }

                    if (!empty($errors)) {
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
            if (!empty($xml->site_id))
                $configValues['site_id'] = (string)$xml->site_id;
            if (!empty($xml->pageIdNews))
                $configValues['pageIdNews'] = (string)$xml->pageIdNews;
            if (!empty($xml->current))
                $configValues['pagination']['current'] = (string)$xml->current;
            if (!empty($xml->nbPerPage))
                $configValues['pagination']['nbPerPage'] = (string)$xml->nbPerPage;
            if (!empty($xml->nbPageBeforeAfter))
                $configValues['pagination']['nbPageBeforeAfter'] = (string)$xml->nbPageBeforeAfter;
            if (!empty($xml->column))
                $configValues['filter']['column'] = (string)$xml->column;
            if (!empty($xml->order))
                $configValues['filter']['order'] = (string)$xml->order;
            if (!empty($xml->date_min))
                $configValues['filter']['date_min'] = (string)$xml->date_min;
            if (!empty($xml->date_max))
                $configValues['filter']['date_max'] = (string)$xml->date_max;
            if (!empty($xml->search))
                $configValues['filter']['search'] = (string)$xml->search;
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
        if (!empty($parameters['site_id']))
            $xmlValueFormatted .= "\t\t" . '<site_id><![CDATA[' . $parameters['site_id'] . ']]></site_id>';
        if (!empty($parameters['pageIdNews']))
            $xmlValueFormatted .= "\t\t" . '<pageIdNews><![CDATA[' . $parameters['pageIdNews'] . ']]></pageIdNews>';
        if (!empty($parameters['current']))
            $xmlValueFormatted .= "\t\t" . '<current><![CDATA[' . $parameters['current'] . ']]></current>';
        if (!empty($parameters['nbPerPage']))
            $xmlValueFormatted .= "\t\t" . '<nbPerPage><![CDATA[' . $parameters['nbPerPage'] . ']]></nbPerPage>';
        if (!empty($parameters['nbPageBeforeAfter']))
            $xmlValueFormatted .= "\t\t" . '<nbPageBeforeAfter><![CDATA[' . $parameters['nbPageBeforeAfter'] . ']]></nbPageBeforeAfter>';
        if (!empty($parameters['column']))
            $xmlValueFormatted .= "\t\t" . '<column><![CDATA[' . $parameters['column'] . ']]></column>';
        if (!empty($parameters['order']))
            $xmlValueFormatted .= "\t\t" . '<order><![CDATA[' . $parameters['order'] . ']]></order>';
        if (!empty($parameters['date_min']))
            $xmlValueFormatted .= "\t\t" . '<date_min><![CDATA[' . $parameters['date_min'] . ']]></date_min>';
        if (!empty($parameters['date_max']))
            $xmlValueFormatted .= "\t\t" . '<date_max><![CDATA[' . $parameters['date_max'] . ']]></date_max>';
        if (!empty($parameters['search']))
            $xmlValueFormatted .= "\t\t" . '<search><![CDATA[' . $parameters['search'] . ']]></search>';

        // Something has been saved, let's generate an XML for DB
        $xmlValueFormatted = "\t" . '<' . $this->pluginXmlDbKey . ' id="' . $parameters['melisPluginId'] . '">' .
            $xmlValueFormatted .
            "\t" . '</' . $this->pluginXmlDbKey . '>' . "\n";

        return $xmlValueFormatted;
    }
}
