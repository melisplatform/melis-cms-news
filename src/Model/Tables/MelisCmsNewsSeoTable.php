<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Model\Tables;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Sql\Expression;
use MelisEngine\Model\Tables\MelisGenericTable;

class MelisCmsNewsSeoTable extends MelisGenericTable 
{
    /**
     * Table name
     */
    const TABLE = 'melis_cms_news_seo';
    /**
     * Primary key
     */
    const PRIMARY_KEY = 'cnews_seo_id';

    /**
     * MelisCmsNewsSeoTable constructor.
     */
    public function __construct()
    {
        $this->idField = self::PRIMARY_KEY;
    }

    /**
     * @param $newsId
     * @param null $langId
     * @return mixed
     */
    public function getNewsSeo($newsId, $langId = null)
    {
        $select = $this->tableGateway->getSql()->select();  

        $select->where('melis_cms_news_seo.cnews_id ='.$newsId);
        
        if (!is_null($langId)) {
            $select->where('melis_cms_news_seo.cnews_seo_lang_id ='.$langId);
        }
        
        $resultData = $this->tableGateway->selectWith($select);
        return $resultData;
    }
    /**
     * @param $seoUrl
     * @param null $siteId
     * @return mixed
     */
    public function checkSeoUrlDuplicates($seoUrl, $siteId)
    {
        $select = $this->tableGateway->getSql()->select();  
        if ($seoUrl) {
            $select->where->like('melis_cms_news_seo.cnews_seo_url', $seoUrl);     
        }
        if (!is_null($siteId)) {
            $select->join(array('news' => 'melis_cms_news'), 'news.cnews_id = melis_cms_news_seo.cnews_id', array(), $select::JOIN_LEFT);
            $select->join(array('site' => 'melis_cms_site'), 'site.site_id = news.cnews_site_id', array('site_name'), $select::JOIN_LEFT);
            $select->where('news.cnews_site_id ='.$siteId);
        }
        $resultData = $this->tableGateway->selectWith($select);
        return $resultData;
    }
}