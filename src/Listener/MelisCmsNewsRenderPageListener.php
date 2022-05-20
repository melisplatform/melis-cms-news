<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\Stdlib\Parameters;

/**
 * This listener will set newsId in the request parameter if and only if news id is not in the uri query param
 * This will be triggered if the news has seo url defined
 */
class MelisCmsNewsRenderPageListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $callBackHandler = $events->attach(
            MvcEvent::EVENT_ROUTE, 
            function(MvcEvent $e){

                // Get route match to know if we are displaying in back or front
                $routeMatch = $e->getRouteMatch();
               // $frontBlogContainer = new Container('melisfrontblog');
                // AssetManager, we don't want listener to be executed if it's not a php code
                $uri = $_SERVER['REQUEST_URI'];
                preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
                if (count($matches) > 1)
                    return;

                // No routematch, we're not in Melis, no need this listener
                if (!$routeMatch)
                    return;
                
                $renderMode = $routeMatch->getParam('renderMode');

                if (empty($renderMode)){
                    $renderMode = 'front';
                }

                // Only for Melis Front routes
                if ($renderMode == 'front') {
                    $sm = $e->getApplication()->getServiceManager();
                
                    // Get the response generated
                    $response = $e->getResponse();
                    $request  = $e->getRequest();
                    $newsId = $request->getQuery('newsId');

                    //if news
                    if (!$newsId) {                        
                        if ($routeMatch->getParam('newsId')) {
                            $postParam = new Parameters();
                            $postParam->set('newsId', $routeMatch->getParam('newsId'));                
                            $request->setQuery($postParam);                                
                        }                                         
                    }                     
                }
            },
            1
        );
        
        $this->listeners[] = $callBackHandler;
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}