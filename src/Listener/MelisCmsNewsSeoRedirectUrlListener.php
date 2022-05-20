<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\Event;

use MelisFront\Listener\MelisFrontSEODispatchRouterAbstractListener;
/**
 * This listener will react if page 404 is happen and this will try to redirect using new url
 */
class MelisCmsNewsSeoRedirectUrlListener extends MelisFrontSEODispatchRouterAbstractListener
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->attachEventListener(
            $events,
        	'*', 
            'melisfront_site_dispatch_ready',
        	function($e){

        	    $sm = $e->getTarget()->serviceManager;
        	    $params = $e->getParams();
                $idPage = $params['idpage'] ?? null;

                //check first if news id is set as route param, else we'll get it in the query param
                $newsId = $params['newsId'] ?? null;
                if (empty($newsId)) {
                    $request = $sm->get('request');                 
                    $newsId = $request->getQuery('newsId');
                }                 

                if (empty($newsId)) {
                    return;
                } else {

                    //get first the redirect url if there are any            
                    $newsSeoService = $sm->get('MelisCmsNewsSeoService');
                    $newsSeoData = $newsSeoService->getSeoData($newsId, $idPage)->current();
                      
                    if (!empty($newsSeoData)) {
                        if (!empty($newsSeoData->cnews_seo_url_redirect)) {
                            $params['301_type'] = 'seoURL';
                            $params['301'] = $newsSeoData->cnews_seo_url_redirect;
                            $params['404'] = null;
                            $params['404_type'] = '';
                        } else {
                            //if news url not found, check for the defined 301 url
                            if (!empty($params['404'])) {                              

                                if (!empty($newsSeoData->cnews_seo_url_301)) {
                                    $params['301_type'] = 'seoURL';
                                    $params['301'] = $newsSeoData->cnews_seo_url_301;
                                    $params['404'] = null;
                                    $params['404_type'] = '';
                                }                          
                            }
                        }
                    }
                                      
                }           
        	},
        120
        );
    }
}