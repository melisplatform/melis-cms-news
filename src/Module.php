<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews;

use MelisCmsNews\Form\Factory\MelisCmsNewsBOSelectFactory;
use MelisCmsNews\Listener\MelisCmsNewsGdprAutoDeleteActionDeleteListener;
use MelisCmsNews\Listener\MelisCmsNewsTableColumnDisplayListener;
use MelisCmsNews\Listener\MelisCmsNewsToolCreatorEditionTypeListener;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\ModuleManager\ModuleManager;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Session\Container;

use MelisCmsNews\Listener\MelisCmsNewsSliderDeletedListener;
use MelisCmsNews\Listener\MelisCmsNewsFlashMessengerListener;
use MelisCmsNews\Listener\MelisCmsNewsPreviewTypeListener;

/*news seo*/
use Laminas\ModuleManager\ModuleEvent;
use MelisCmsNews\Listener\MelisCmsNewsSEORouteListener;
use MelisCmsNews\Listener\MelisCmsNewsRenderPageListener;
use MelisCmsNews\Listener\MelisCmsNewsMetaPageListener;
use MelisCmsNews\Listener\MelisCmsNewsSeoRedirectUrlListener;
class Module
{
    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager();
        /**
         *  - Catching PAGE SEO URLs to update Router
         *    > create SEO route first so the modules can have a route match in creating translations
         */
        $events->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, [new MelisCmsNewsSEORouteListener(), 'onLoadModulesPost']);
    }
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
            (new MelisCmsNewsSliderDeletedListener())->attach($eventManager);
            (new MelisCmsNewsFlashMessengerListener())->attach($eventManager);
            (new MelisCmsNewsPreviewTypeListener())->attach($eventManager);
            (new MelisCmsNewsTableColumnDisplayListener())->attach($eventManager);
            (new MelisCmsNewsToolCreatorEditionTypeListener())->attach($eventManager);
            (new MelisCmsNewsGdprAutoDeleteActionDeleteListener())->attach($eventManager);
        } else {
            (new MelisCmsNewsRenderPageListener())->attach($eventManager);
            (new MelisCmsNewsMetaPageListener())->attach($eventManager);
            (new MelisCmsNewsSeoRedirectUrlListener())->attach($eventManager);
        }
    }
    
    public function getConfig()
    {
        $config = [];
        $configFiles = [
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

            // Extending with MelisCmsComments module
            include __DIR__ . '/../config/comments.config.php',
            include __DIR__ . '/../config/plugins/dashboard/dashboard.latest.comments.php',
        ];
        
        foreach ($configFiles as $file) {
            $config = ArrayUtils::merge($config, $file);
        } 
        
        return $config;
    }

    public function getAutoloaderConfig()
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
    
    public function createTranslations($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $translator = $sm->get('translator');
    
        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];

        if (!empty($locale)){
            
            $translationType = [
                'interface',
            ];
            
            $translationList = [];
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
