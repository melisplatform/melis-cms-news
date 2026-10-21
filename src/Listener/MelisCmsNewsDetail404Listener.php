<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Listener;

use Laminas\EventManager\EventManagerInterface;
use MelisFront\Listener\MelisFrontSEODispatchRouterAbstractListener;

/**
 * A news detail page asked for a news that does not exist or is not online (draft, not yet published,
 * expired) goes to the site 404 page, like an unknown news SEO URL, instead of an HTTP 500 or a blank
 * page (Mantis #0011271). A detail page is a page of type NEWS_DETAIL or holding the « Détails actualité »
 * plugin. The back-office preview (renderMode=previewtab, logged-in user) still shows drafts.
 */
class MelisCmsNewsDetail404Listener extends MelisFrontSEODispatchRouterAbstractListener
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->attachEventListener(
            $events,
            '*',
            'melisfront_site_dispatch_ready',
            function ($e) {
                $params = $e->getParams();
                if (($params['renderMode'] ?? 'front') !== 'front' || !empty($params['301']) || !empty($params['404'])) {
                    return;
                }

                $sm = $e->getTarget()->serviceManager;
                $newsId = $params['newsId'] ?? $sm->get('request')->getQuery('newsId');
                $idPage = $params['idpage'] ?? null;
                if (empty($newsId) || empty($idPage) || empty($params['datasPage'])) {
                    return;
                }

                $pageTree = $params['datasPage']->getMelisPageTree();
                $isDetailPage = !empty($pageTree)
                    && (($pageTree->page_type ?? '') === 'NEWS_DETAIL'
                        || str_contains((string) ($pageTree->page_content ?? ''), 'MelisCmsNewsShowNewsPlugin'));
                if (!$isDetailPage) {
                    return;
                }

                $newsService = $sm->get('MelisCmsNewsService');
                if ($newsService->isBackOfficePreview()) {
                    return;
                }

                if (!ctype_digit((string) $newsId) || !$newsService->isNewsPublished((int) $newsId)) {
                    $params['404'] = $e->getTarget()->redirect404($sm->get('Application')->getMvcEvent(), $idPage);
                }
            },
            130
        );
    }
}
