<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Service;

use MelisCore\Service\MelisGeneralService;

class MelisCmsNewsSeoService extends MelisGeneralService 
{
			
	/**
	 * Returns the link of a page, MelisUrl or specific SEO
	 * @param int $idPage, the page id of the news detail page
	 * @param int $newsId, the news id
	 * @param boolean $absolute If true, returns link with domain
	 * 
	 */ 
	public function getPageLink($idPage, $newsId, $absolute = false)
	{
		if (empty($newsId))
			return null;

		// Retrieve cache version if front mode to avoid multiple calls
		$cacheKey = 'getNewsLink_id_' . $idPage . '_newsId_'.$newsId.'_' . $absolute;
		$cacheConfig = 'engine_page_services';
		$melisEngineCacheSystem = $this->getServiceManager()->get('MelisEngineCacheSystem');
		$results = $melisEngineCacheSystem->getCacheByKey($cacheKey, $cacheConfig);
		
		if (!is_null($results)) return $results;

		// Get the already generated link from the DB if possible    
		$link = '';		
		$pageDefaultUrlsSrv = $this->getServiceManager()->get('MelisEnginePageDefaultUrlsService');
		if ($this->getRenderMode() == 'front') {
			$defaultUrls = $pageDefaultUrlsSrv->getPageDefaultUrl($idPage);
			if (!empty($defaultUrls)) {
				if (count($defaultUrls) > 0) {
					$link = $defaultUrls[0]['purl_page_url'];
				}
			}
		}

		// if nothing found in DB, then let's generate
		if ($link == '') {
			// Generate real one
			$seoUrl = '';
	            
	        //Check for Seo URL first of the idpage   
        	$melisPage = $this->getServiceManager()->get('MelisEnginePage');
			$datasPageRes = $melisPage->getDatasPage($idPage);
			$datasPageTreeRes = $datasPageRes->getMelisPageTree();
			
			if ($datasPageTreeRes && !empty($datasPageTreeRes->pseo_url)) {
				$seoUrl = $datasPageTreeRes->pseo_url;
				if (substr($seoUrl, 0, 1) != '/')
					$seoUrl = '/' . $seoUrl;
			}
         
			$melisEngineTreeService = $this->getServiceManager()->get('MelisTreeService');
			if ($seoUrl == '') {
				/**
				 * SITE V2 UPDATES
				 *
				 * This will check the site_opt_lang_url of the site
				 * to determine whether the url will be modified to
				 * add the lang locale on the url
				 */				
				$siteLangOpt = $melisEngineTreeService->getSiteLangUrlOptByPageId($idPage);
				$seoUrl = $siteLangOpt['siteLangOptVal'];

				/**
				 * END V2 UPDATES
				 */
				// First let's see if page is the homepage one ( / no id following for url)
				$datasSite = $melisEngineTreeService->getSiteByPageId($idPage);
				if (!empty($datasSite) && $datasSite->site_main_page_id == $idPage) {
					$seoUrl = (!empty($seoUrl)) ? $seoUrl : '/';
				} else {
					// if not, construct a classic Melis URL /..../..../id/xx
					$datasPage = $melisEngineTreeService->getPageBreadcrumb($idPage);

					$seoUrl .= '/';
					foreach ($datasPage as $page) {
						if (!empty($datasSite) && $datasSite->site_main_page_id == $page->page_id)
							continue;

							$namePage = $page->page_name;
	
						$seoUrl .= $namePage . '/';
					}
					$seoUrl .= 'id/' . $idPage;
				}
			}

			$link = $melisEngineTreeService->cleanLink($seoUrl);	
				   
			//add to DB
			$tablePageDefaultUrls = $this->getServiceManager()->get('MelisEngineTablePageDefaultUrls');
			$tablePageDefaultUrls->save(
				array(
					'purl_page_id' => $idPage,
					'purl_page_url' => $link
				),
				$idPage
			);	
		}
		//Check for the News SEO URL
            $newsDetailSeoLink = $this->getSeoData($newsId, $idPage)->current();
            //add the newsId param if no news seo url is given		
			if (empty($newsDetailSeoLink->cnews_seo_url)) {			
				$link = $link . '?newsId=' . $newsId;
			} else {
				//if the news has a seo url set, use it, else, get the seo url of the page	           
            	if (substr($newsDetailSeoLink->cnews_seo_url, 0, 1) != '/'){
					$link = $link . '/' . $newsDetailSeoLink->cnews_seo_url;           
            	} else {
            		$link = $link .  $newsDetailSeoLink->cnews_seo_url; 
			}
		}
			
		$router = $this->getServiceManager()->get('router');
		$request = $this->getServiceManager()->get('request');
		$routeMatch = $router->match($request);

		$idversion = null;
		if (!empty($routeMatch)){
			$idversion = $routeMatch->getParam('idversion');
		}

		if ($absolute || !empty($idversion)) {
			$host = $melisEngineTreeService->getDomainByPageId($idPage);
			$link = $host . $link;
		}

		// Save cache key
		$melisEngineCacheSystem->setCacheByKey($cacheKey, $cacheConfig, $link);

		return $link;
	}


	/**
	* Gets seo data for the given news id
	* @param int $newsId
	* @param int $idPage, the news detail page id
	*/
	public function getSeoData($newsId, $idPage)
	{
		// Retrieve cache version if front mode to avoid multiple calls
		$cacheKey = 'getSeoDataNews_page_'.$idPage.'_newsID_'. $newsId;
		$cacheConfig = 'engine_memory_cache';
		$melisEngineCacheSystem = $this->getServiceManager()->get('MelisEngineCacheSystem');
		$results = $melisEngineCacheSystem->getCacheByKey($cacheKey, $cacheConfig);

		if (!is_null($results)) return $results;

		//get language of the given page
		$pageLangId = null;
		if (!empty($idPage)) {
			$cmsPageLang = $this->getServiceManager()->get('MelisEngineTablePageLang');
			$pageLang = $cmsPageLang->getEntryByField('plang_page_id', $idPage)->current();

			if (!empty($pageLang)) {
				$pageLangId = $pageLang->plang_lang_id;
			}
		}

		$tableNewsSeo = $this->getServiceManager()->get('MelisCmsNewsSeoTable');
		$seoData = $tableNewsSeo->getNewsSeo($newsId, $pageLangId);

		// Save cache key
		$melisEngineCacheSystem->setCacheByKey($cacheKey, $cacheConfig, $seoData);
		
		return $seoData;
	}

	/**
	 * Updates the title, description and canonical of the news detail page
	 * 
	 * @param int $news Id 
	 * @param int $idPage Id of page asked
	 * @param string $contentGenerated Content to be changed
	 */
	public function updateTitleAndDescription($newsId, $idPage, $contentGenerated)
	{
		$newContent = $contentGenerated;

		// Check for SEO URL first from the news seo table
	    $newsSeoService = $this->getServiceManager()->get('MelisCmsNewsSeoService');
	    $newsTextTable = $this->getServiceManager()->get('MelisCmsNewsTextsTable');
        $newsDetailSeo = $this->getSeoData($newsId, $idPage)->current();
			
		if (!empty($newsDetailSeo))	{				
			/**
			 * Description tag
			 */				
			if (!empty($newsDetailSeo->cnews_seo_meta_description)) {
				$metaDescription = addslashes($newsDetailSeo->cnews_seo_meta_description);
				$metaDescription = str_replace("\'", "'", $metaDescription);

				$descriptionTag = "\n\t<meta name=\"description\" content=\"$metaDescription\" />\n";				
				$descriptionRegex = '/(<meta[^>]*name=[\"\']description[\"\'][^>]*content=[\"\'](.*?)[\"\'][^>]*>)/i';				
				preg_match($descriptionRegex, $contentGenerated, $descriptions);				

				if (!empty($descriptions)) {
					// Replace existing description in source with the defined meta description
					$newContent = preg_replace($descriptionRegex, $descriptionTag, $contentGenerated,1);
				} else {
					// meta desc tag doesn't exist, look for head tag to add
					// if no head tag, then nothing will happen
					$headRegex = '/(<head[^>]*>)/im';
					$newContent = preg_replace($headRegex, "$1$descriptionTag", $contentGenerated,1);
				}	
				$contentGenerated = $newContent;		
			}

			/**
			 * Title tag
			 */			
			$metaTitle = '';
			if (!empty($newsDetailSeo->cnews_seo_meta_title)) {
				$metaTitle = $newsDetailSeo->cnews_seo_meta_title;
					
			} else {
				//use the news title instead if no meta title is given
				$newsTitle = $newsTextTable->getEntryByField('cnews_id', $newsId)->current();
				if (!empty($newsTitle)) {
					$metaTitle = $newsTitle->cnews_title;
				}				
			}
		
			if ($metaTitle != '') {
				$metaTitle = addslashes($metaTitle);
				$metaTitle = str_replace("\'", "'", $metaTitle);

				$titleTag = "<title>$metaTitle</title>";
				$titleReg = '/\<title\>+(.*?)+\<\/title>/';
				preg_match($titleReg, $contentGenerated, $titles);

				if (!empty($titles)) {
					// Replace existing title in source with the defined meta page title
					$newContent = preg_replace($titleReg, $titleTag, $newContent);
				} else {
					// Meta Title tag doesn't exist, look for head tag to add
					// if no head tag, then nothing will happen
					$headRegex = '/(<head[^>]*>)/im';
					$newContent = preg_replace($headRegex, "$1$titleTag", $newContent,1);
				}

				$contentGenerated = $newContent;
			}	
				

			/**
			 * Canonical Tag
			 */
			if (!empty($newsDetailSeo->cnews_seo_canonical)) {
				$canonicalUrl = addslashes($newsDetailSeo->cnews_seo_canonical);
				$canonicalUrl = str_replace("\'", "'", $canonicalUrl);

				$canonicalUrlTag = "\n\t<link rel=\"canonical\" href=\"$canonicalUrl\" />\n";
				$canonicalRegex = '/(<link[^>]*rel=[\"\']canonical[\"\'][^>]*href=[\"\'](.*?)[\"\'][^>]*>)/i';
				preg_match($canonicalRegex, $contentGenerated, $canonicalFound);
				if(!empty($canonicalFound)){
					$newContent = preg_replace($canonicalRegex, $canonicalUrlTag, $contentGenerated, 1);
				}else {
					$headRegex = '/(<head[^>]*>)/im';
					$newContent = preg_replace($headRegex, "$1$canonicalUrlTag", $contentGenerated, 1);
				}
				$contentGenerated = $newContent;
			}	
		}
       
		return $contentGenerated;
	}

}
