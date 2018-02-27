<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Session\Container;

use MelisCmsNews\Listener\MelisCmsNewsSliderDeletedListener;
use MelisCmsNews\Listener\MelisCmsNewsFlashMessengerListener;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $melisRoute = 0;
        $this->createTranslations($e);        
        $sm = $e->getApplication()->getServiceManager();
        $routeMatch = $sm->get('router')->match($sm->get('request'));
        
        if (!empty($routeMatch))
        {
            $routeName = $routeMatch->getMatchedRouteName();
            
            $module = explode('/', $routeName);
             
            if (!empty($module[0]))
            {
                if ($module[0] == 'melis-backoffice')
                    $melisRoute = true;
            }
        }
        
        if ($melisRoute)
        {
            // attach listeners for Melis
            $eventManager->attach(new MelisCmsNewsSliderDeletedListener());
            $eventManager->attach(new MelisCmsNewsFlashMessengerListener());         
            
        }
    }
    
    public function init(ModuleManager $manager)
    {
    }

    public function getConfig()
    {
    	$config = array();
    	$configFiles = array(
    		include __DIR__ . '/../config/module.config.php',

    	    // interface design Melis
    		include __DIR__ . '/../config/app.interface.php',
    	    include __DIR__ . '/../config/app.tools.php',
    	    include __DIR__ . '/../config/app.forms.php',
    	    include __DIR__ . '/../config/app.microservice.php',
    	    
    	    // Tests
    	    include __DIR__ . '/../config/diagnostic.config.php',

    	    // Templating plugins
    	    include __DIR__ . '/../config/plugins/MelisCmsNewsLatestNewsPlugin.config.php',
    	    include __DIR__ . '/../config/plugins/MelisCmsNewsListNewsPlugin.config.php',
    	    include __DIR__ . '/../config/plugins/MelisCmsNewsShowNewsPlugin.config.php',
    	    
    	);
    	
    	foreach ($configFiles as $file) {
    		$config = ArrayUtils::merge($config, $file);
    	} 
    	
    	return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function createTranslations($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $translator = $sm->get('translator');
    
        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];

        if (!empty($locale)){
            
            $translationType = array(
                'interface',
            );
            
            $translationList = array();
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/../module/MelisModuleConfig/config/translation.list.php')){
                $translationList = include 'module/MelisModuleConfig/config/translation.list.php';
            }

            foreach($translationType as $type){
                
                $transPath = '';
                $moduleTrans = __NAMESPACE__."/$locale.$type.php";
                
                if(in_array($moduleTrans, $translationList)){
                    $transPath = "module/MelisModuleConfig/languages/".$moduleTrans;
                }

                if(empty($transPath)){
                    
                    // if translation is not found, use melis default translations
                    $defaultLocale = (file_exists(__DIR__ . "/../language/$locale.$type.php"))? $locale : "en_EN";
                    $transPath = __DIR__ . "/../language/$defaultLocale.$type.php";
                }
                
                $translator->addTranslationFile('phparray', $transPath);
            }
        }
    }
 
}
