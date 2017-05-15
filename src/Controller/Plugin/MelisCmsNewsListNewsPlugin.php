<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller\Plugin;

use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

/**
 * This plugin implements the business logic of the
 * "ListNews" plugin.
 * 
 * Please look inside app.plugins.php for possible awaited parameters
 * in front and back function calls.
 * 
 * front() and back() are the only functions to create / update.
 * front() generates the website view
 * back() generates the plugin view in template edition mode (TODO)
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
    // the key of the configuration in the app.plugins.php
    public $configPluginKey = 'meliscmsnews';
    
    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        
        // Pagination
        $current = (!empty($this->pluginFrontConfig['pagination']['current'])) ? $this->pluginFrontConfig['pagination']['current'] : 1;
        $nbPerPage = (!empty($this->pluginFrontConfig['pagination']['nbPerPage'])) ? $this->pluginFrontConfig['pagination']['nbPerPage'] : 1;
        $nbPageBeforeAfter = (!empty($this->pluginFrontConfig['pagination']['nbPageBeforeAfter'])) ? $this->pluginFrontConfig['pagination']['nbPageBeforeAfter'] : 1;
        
        // Filters
        $status = (!empty($this->pluginFrontConfig['filter']['status'])) ? $this->pluginFrontConfig['filter']['status'] : 1;
        $orderColumn = (!empty($this->pluginFrontConfig['filter']['column'])) ? $this->pluginFrontConfig['filter']['column'] : null;
        $order = (!empty($this->pluginFrontConfig['filter']['order'])) ? $this->pluginFrontConfig['filter']['order'] : null;
        $dateMin = (!empty($this->pluginFrontConfig['filter']['date_min'])) ? $this->pluginFrontConfig['filter']['date_min'] : null;
        $dateMax = (!empty($this->pluginFrontConfig['filter']['date_max'])) ? $this->pluginFrontConfig['filter']['date_max'] : null;
        $unpublishFilter = (!empty($this->pluginFrontConfig['filter']['unpublish_filter'])) ? $this->pluginFrontConfig['filter']['unpublish_filter'] : null;
        $siteId = (!empty($this->pluginFrontConfig['filter']['site_id'])) ? $this->pluginFrontConfig['filter']['site_id'] : null;
        $search = (!empty($this->pluginFrontConfig['filter']['search'])) ? $this->pluginFrontConfig['filter']['search'] : null;
        
        // Retreiving News list using MelisCmsNewsService
        $newsSrv = $this->getServiceLocator()->get('MelisCmsNewsService');
        $newsList = $newsSrv->getNewsList($status, null, null, $dateMin, $dateMax, $unpublishFilter, null, null, $orderColumn, $order, $siteId ,$search);
        
        $listNews = array();
        foreach ($newsList As $key => $val)
        {
            // Adding the News Data to result variable
            array_push($listNews, $val);
        }
        
        $paginator = new Paginator(new ArrayAdapter($listNews));
        $paginator->setCurrentPageNumber($current)
                    ->setItemCountPerPage($nbPerPage);
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'listNews' => $paginator,
            'nbPageBeforeAfter' => $nbPageBeforeAfter
        );
        
        // return the variable array and let the view be created
        return $viewVariables;
    }
}
