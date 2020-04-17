<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use MelisCore\Listener\MelisGeneralListener;

class MelisCmsNewsServiceMicroServiceListener extends MelisGeneralListener implements ListenerAggregateInterface
{

    public function attach(EventManagerInterface $events)
    {
        $sharedEvents      = $events->getSharedManager();

        $callBackHandler = $sharedEvents->attach(
            '*',
            array(
                'melis_core_microservice_amend_data',
            ),
            function($e){

                $sm = $e->getTarget()->getServiceLocator();
                $params = $e->getParams();
                $tool = $sm->get('MelisCoreTool');

                $module  = isset($params['module'])  ? $params['module']  : null;
                $service = isset($params['service']) ? $params['service'] : null;
                $method  = isset($params['method'])  ? $params['method']  : null;
                $post    = isset($params['post'])  ? $params['post']  : null;
                $results = isset($params['results'])  ? $params['results']  : null;

                if($module == 'MelisCmsNews' && $service == 'MelisCmsNewsService' && $method == 'getNewsList') {


                    $request = $sm->get('Request');
                    $uri     = $request->getUri();
                    $scheme  = $uri->getScheme();
                    $host    = $uri->getHost();
                    $url     = $scheme . '://' . $host;

                    foreach ($results as $item)
                    {
                        $tmpData1 = $item->cnews_image1;
                        $tmpData2 = $item->cnews_image2;
                        $tmpData3 = $item->cnews_image3;

                        $item->cnews_image1 = $url . $tmpData1;
                        if($tmpData1)
                        {
                            $item->cnews_image1 = $url . $tmpData1;
                        }
                        if($tmpData2)
                        {
                            $item->cnews_image2 = $url . $tmpData2;
                        }
                        if($tmpData3)
                        {
                            $item->cnews_image3 = $url . $tmpData3;
                        }


                    }
                    $results = $tool->convertObjectToArray($results);

                }
                if($module == 'MelisCmsSlider' && $service == 'MelisCmsSliderService' && $method == 'getSliderList') {
                    $results = $tool->convertObjectToArray($results);

                }

                if($module == 'MelisCmsSlider' && $service == 'MelisCmsSliderService' && $method == 'getSliderByPageId') {
                    $results = $tool->convertObjectToArray($results);

                }
                return array(
                    'module'  => $module,
                    'service' => $service,
                    'method'  => $method,
                    'post'    => $post,
                    'results' => $results
                );
            },
            -1000);

        $this->listeners[] = $callBackHandler;
    }
}