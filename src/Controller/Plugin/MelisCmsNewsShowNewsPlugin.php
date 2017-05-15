<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Controller\Plugin;

use MelisEngine\Controller\Plugin\MelisTemplatingPlugin;

/**
 * This plugin implements the business logic of the
 * "ShowNews" plugin.
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
    // the key of the configuration in the app.plugins.php
    public $configPluginKey = 'meliscmsnews';
    
    /**
     * This function gets the datas and create an array of variables
     * that will be associated with the child view generated.
     */
    public function front()
    {
        // Get the parameters and config from $this->pluginFrontConfig (default > hardcoded > get > post)
        $newsId = (!empty($this->pluginFrontConfig['newsId'])) ? $this->pluginFrontConfig['newsId'] : null;
        $siteId = (!empty($this->pluginFrontConfig['site_id'])) ? $this->pluginFrontConfig['site_id'] : null;
        $active = (!empty($this->pluginFrontConfig['active'])) ? $this->pluginFrontConfig['active'] : true;
        $filterPublish = (!empty($this->pluginFrontConfig['filter_publish'])) ? $this->pluginFrontConfig['filter_publish'] : true;
        $filterUnpublish = (!empty($this->pluginFrontConfig['filter_unpublish'])) ? $this->pluginFrontConfig['filter_unpublish'] : true;
        
        $newsData = array();
        if ($newsId)
        {
            // Retreiving News using MelisCmsNewsService
            $newsSrv = $this->getServiceLocator()->get('MelisCmsNewsService');
            $newsData = $newsSrv->getNewsById($newsId);
            
            if($filterPublish){
                // publish date is not greater than today
                if(empty($newsData->cnews_publish_date)){
                    $newsData =  array();
                }else{
                    $newsData = (strtotime($newsData->cnews_publish_date) < strtotime("now"))? $newsData : array();
                }
            }
          
            if($filterUnpublish && !empty($newsData->cnews_unpublish_date)){
                // unpublish date is not greater than today
                $newsData = (strtotime($newsData->cnews_unpublish_date) > strtotime("now"))? $newsData : array();
            }
            
            if($active){
                // check if active
                $newsData = ($newsData->cnews_status)? $newsData : array();
            }
            
            if(!empty($newsData) && !is_null($siteId)){
                // check site id
                $newsData = ($siteId == $newsData->cnews_site_id)? $newsData : array();
            }
        }
        
        // Create an array with the variables that will be available in the view
        $viewVariables = array(
            'news' => $newsData
        );
        
        // return the variable array and let the view be created
        return $viewVariables;
    }
}
