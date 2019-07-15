<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use MelisCore\Listener\MelisCoreGeneralListener;
use Zend\Session\Container;

class MelisCmsNewsTableColumnDisplayListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents      = $events->getSharedManager();

        $this->listeners[] = $sharedEvents->attach(
            '*',
            'melis_toolcreator_col_display_options',
            function ($e) {

                $sm = $e->getTarget()->getServiceLocator();
                $params = $e->getParams();
                $params['valueOptions']['news_title'] = $sm->get('translator')->translate('tr_meliscmsnews_news_title');
            }
        );

        $this->listeners[] = $sharedEvents->attach(
            '*',
            'melis_tool_column_display_news_title',
            function($e){

                $sm = $e->getTarget()->getServiceLocator();
                $params = $e->getParams();

                $newsTitle = $params['data'];

                $container = new Container('meliscore');
                $coreLangId = $container['melis-lang-id'];
                $langId = $coreLangId;

                $melisEngineLangTable = $sm->get('MelisEngineTableCmsLang');
                $locale = $container['melis-lang-locale'];
                $currentLangData = $melisEngineLangTable->getEntryByField('lang_cms_locale', $locale);
                $currentLang = $currentLangData->current();
                if (!empty($currentLang))
                    $langId = $currentLang->lang_cms_id;

                $newsSrv = $sm->get('MelisCmsNewsTextsTable');
                $newsData = $newsSrv->getEntryByField('cnews_id', $params['data'])->toArray();

                $newsTitleTmp = '';
                if (!empty($newsData)){
                    foreach ($newsData As $data)
                        if ($data['cnews_lang_id'] == $langId && !empty($data['cnews_title'])){
                            $newsTitleTmp = $data['cnews_title'];
                        }
                }

                if (empty($newsTitleTmp)){
                    foreach ($newsData As $data)
                        if (!empty($data['cnews_title'])){
                            $newsTitleTmp = $data['cnews_title'];
                            break;
                        }
                }

                $params['data'] = $newsTitleTmp;
            }
        );
    }
}