<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Laminas\ModuleManager\ModuleEvent;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Mvc\MvcEvent;

class MelisCmsNewsSEORouteListener
{
    public function onLoadModulesPost(ModuleEvent $e)
    {
        /** @var ServiceManager $sm */
        $sm = $e->getParam('ServiceManager');
        if(!empty($_SERVER['REQUEST_URI'])){
            $uri = $_SERVER['REQUEST_URI'];

            //we don't want listener to be executed if it's not a php code
            preg_match('/.*\.((?!php).)+(?:\?.*|)$/i', $uri, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches) > 1)
                return;

            //check if we are in front
            if (!str_starts_with($uri, '/melis')) {
                // get request
                $request = $sm->get('request');
                // get uri
                $uri = $request->getUri();

                $url = $uri->getPath();
                // remove slash on first
                $url = preg_replace('/\//i','',$url,1);
                // get the url parameters
                $urlParams = $request->getQuery()->toString();
                if (! empty($urlParams)) {
                    $urlParams = '?' . $urlParams;
                }

                //check first if there is news id the query,                                               
                $newsId = $request->getQuery('newsId');


                //if no news id in the query, check if the url contains news seo url
                if (empty($newsId)) {
                
                    $uri_segments = explode('/', $url);
                    $uriSegmentCount = count($uri_segments);     
                    
                    if ($uriSegmentCount > 1) {
                        $melisNewsSeoTable = $sm->get('MelisCmsNewsSeoTable');
                        $idPage = null;
                        $seoUrl = "";

                        $idKey = array_search('id', $uri_segments); 
             
                        //if no 'id' in the segment, we assume that the page uses a pseo_url             
                        if(!$idKey) {
                            $siteDomainTable = $sm->get('MelisEngineTableSiteDomain');
                            $siteDomain  = $siteDomainTable->getEntryByField('sdom_domain', $uri->getHost())->current();

                            //get all page seo url of the current published news detail pages and compare it to the given url if matches
                            $publishedPages = $sm->get('MelisEngineTablePagePublished');      
                            $publishedPages = $publishedPages->getPagesByType('NEWS_DETAIL', $siteDomain->sdom_site_id);
                            $cmsPageSeo = $sm->get('MelisEngineTablePageSeo');

                            if ($publishedPages->count()) {
                                foreach ($publishedPages->toArray() as $index => $page) {                     
                                    $pageSeo = $cmsPageSeo->getEntryByField('pseo_id', $page['page_id'])->current();

                                    if (!empty($pageSeo)) {
                                        $pageSeoUrl = ltrim($pageSeo->pseo_url, '/');
                                        $pageSeoUrl = rtrim($pageSeo->pseo_url, '/');

                                        //check if the url contains the page seo url, then check if the remaining of the url is the news seo url 
                                        if (str_contains($url, $pageSeoUrl) ) {                                            
                                            $seoUrl = substr($url, strlen($pageSeoUrl));
                                            $seoUrl = ltrim($seoUrl,'/');
                                            $seoUrl = rtrim($seoUrl,'/');
                                           
                                            $newsSeo = $melisNewsSeoTable->getEntryByField('cnews_seo_url', $seoUrl)->current();                                          
                                            if (!empty($newsSeo)) {
                                                $idPage = $page['page_id'];                                                
                                                break;
                                            }
                                        }
                                    }
                                }
                            }

                        } else {                            
                            //new detail page not uses pseo_url, get the news seo url after news/id/{idpage} segment
                            $idPage = $uri_segments[$idKey+1] ?? null;//the news detail page id
                        
                            //get the seo url from the given uri path                            
                            //seo url starts after the id/{idpage} key
                            for ($i = $idKey+2; $i < count($uri_segments); $i++) {
                                $seoUrl .= !empty($seoUrl) ? '/'. $uri_segments[$i] : $uri_segments[$i];
                            }

                            $newsSeo = $melisNewsSeoTable->getEntryByField('cnews_seo_url', $seoUrl)->current();
                        }                                               

                        if (!empty($newsSeo) && !empty($idPage)) { 
                            //set the url param
                            $urlParams = '?newsId='.$newsSeo->cnews_id;

                            // get router
                            $router = $sm->get('router');
                            // Creating dynamicaly the route and the params that are needed in the regular melis routing
                            $route = \Laminas\Router\Http\Segment::factory(array(
                                'route' => '/' . $url,
                                'defaults' => array(
                                    'controller' => 'MelisFront\Controller\Index',
                                    'action' => 'index',
                                    'idpage' => $idPage,
                                    'renderType' => 'melis_zf2_mvc',
                                    'renderMode' => 'front',
                                    'preview' => false,
                                    'urlparams' => $urlParams,
                                    'norewrite' => true,
                                    'newsId' => $newsSeo->cnews_id                           
                                )                        
                            ));

                            // add the route to the router
                            $router->addRoute('melis-front-page-seo', $route);  
                        }                        
                    } 
                }         
            }
        }
    }
}