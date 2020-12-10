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

class MelisCmsNewsTable extends MelisGenericTable 
{

    /**
     * Table name
     */
    const TABLE = 'melis_cms_news';
    /**
     * Primary key
     */
    const PRIMARY_KEY = 'cnews_id';

    protected $cnews_text_cols = [
        'cnews_text_id',
        'cnews_title',
        'cnews_subtitle',
        'cnews_paragraph1',
        'cnews_paragraph2',
        'cnews_paragraph3',
        'cnews_paragraph4',
        'cnews_id',
        'cnews_lang_id',
    ];

    /**
     * MelisCmsNewsTable constructor.
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
    public function getNews($newsId, $langId = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $clause = array();
        
        $select->join('melis_cms_site', 'melis_cms_site.site_id = melis_cms_news.cnews_site_id', array('site_name'), $select::JOIN_LEFT);
        $select->join('melis_cms_news_texts', 'melis_cms_news_texts.cnews_id = melis_cms_news.cnews_id','*', $select::JOIN_LEFT);
        
        $select->where('melis_cms_news.cnews_id ='.$newsId);
        
        if (!is_null($langId)) {
            $select->where('melis_cms_news_texts.cnews_lang_id ='.$langId);
        }
        
        $resultData = $this->tableGateway->selectWith($select);
        return $resultData;
    }

    public function getNewsList(
        $status          = null,
        $langId          = null,
        $dateMin         = null,
        $dateMax         = null,
        $publishDateMin  = null,
        $publishDateMax  = null,
        $unpublishFilter = false,
        $start           = null,
        $limit           = null,
        $orderColumn     = null,
        $order           = null,
        $siteId          = null,
        $search          = null,
        $publishedOnly   = null 
    ) {
        $select = $this->tableGateway->getSql()->select();

        $select->join('melis_cms_site', 'melis_cms_site.site_id = melis_cms_news.cnews_site_id', array('site_name','site_label'), $select::JOIN_LEFT);
        $select->join('melis_cms_news_texts', 'melis_cms_news_texts.cnews_id = melis_cms_news.cnews_id', '*', $select::JOIN_LEFT);

        if (!is_null($search)) {
            $search = '%'.$search.'%';
            $select->where->NEST->like('melis_cms_news.cnews_id', $search)
            ->or->like('melis_cms_news_texts.cnews_title', $search);
        }

        if (!is_null($siteId)) {
            $select->where->equalTo('cnews_site_id', $siteId);
        }
        
        if (!is_null($status)) {
            $select->where('cnews_status ='.$status);
        }
        
        if (!is_null($langId)) {
            $select->where('melis_cms_news_texts.cnews_lang_id ='.$langId);
        }
        
        if (!is_null($dateMin)) {
            $select->where('cnews_creation_date >= "'.$dateMin.'"');
        }
        
        if (!is_null($dateMax)) {
            $select->where('cnews_creation_date <= "'.$dateMax.'"');
        }

        if (!is_null($publishDateMin)) {
            $select->where('DATE(cnews_publish_date)>= "'.$publishDateMin.'"');
        }

        if (!is_null($publishDateMax)) {
            $select->where('DATE(cnews_publish_date) <= "'.$publishDateMax.'"');
        }

        if (!is_null($limit)) {
            $select->limit( (int) $limit);
        }

        if ($unpublishFilter) {
            $select->where->nest->greaterThan('cnews_unpublish_date', date("Y-m-d H:i:s"))->or->isNull('cnews_unpublish_date')->unnest;
        }
        
        if (!is_null($start)) {
            $select->offset($start);
        }

        if (!is_null($orderColumn) && !is_null($order)) {
            if ($orderColumn == 'site_label') {
                $select->order('melis_cms_site.' . $orderColumn . ' ' . $order);
            } elseif (in_array($orderColumn, $this->cnews_text_cols)) {
                $select->order('melis_cms_news_texts.' . $orderColumn . ' ' . $order);
            } else {
                $select->order('melis_cms_news.' . $orderColumn . ' ' . $order);
            }
        }

        /**
         * include news if date today is equal or greather than published date
         */
        if ($publishedOnly) {
            $select->where('NOW() >= cnews_publish_date');
        }

        $select->where('melis_cms_news_texts.cnews_title !=""');

        $resultData = $this->tableGateway->selectWith($select);

        return $resultData;
    }
    
    public function checkForDuplicates($search)
    {
        $search = '%'.$search.'%';
        $sql = "select Number From 
        (select cnews_documents1 as Number from melis_cms_news WHERE cnews_documents1 LIKE '". $search ."'
        union all
        select cnews_documents2 as Number from melis_cms_news WHERE cnews_documents2 LIKE '". $search ."'
        union all
        select cnews_documents3 as Number from melis_cms_news WHERE cnews_documents3 LIKE '". $search ."'
        union all
        select cnews_image1  as Number from melis_cms_news WHERE cnews_image1 LIKE '". $search ."'
        union all
        select cnews_image2  as Number from melis_cms_news WHERE cnews_image2 LIKE '". $search ."'
        union all
        select cnews_image3 as Number from melis_cms_news WHERE cnews_image2 LIKE '". $search ."'
        ) myTab";
        
        $resultData = $this->tableGateway->getAdapter()->driver->getConnection()->execute($sql);
        return $resultData;
    }
    
    public function getNewsListByMonths($limit = null, $siteId = null)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->columns(array(
            new Expression('MONTH(cnews_publish_date) AS month'),
            new Expression('YEAR(cnews_publish_date) AS year'),
        ));
        $select->where(array('cnews_status' => '1'));

        if(!is_null($siteId)){
            $select->where->equalTo('cnews_site_id', $siteId);
        }

        $select->where->lessThan('cnews_publish_date', date('Y-m-d H:i:s', strtotime("now")));

        $select->where->nest->greaterThan('cnews_unpublish_date', date('Y-m-d H:i:s', strtotime("now")))->or->isNull('cnews_unpublish_date')->unnest;

        $select->group(array('month', 'year'));
        $select->order(new Expression('month + " " + year'), 'DESC');


        if (!is_null($limit))
        {
            $select->limit($limit);
        }

    
        $resultData = $this->tableGateway->selectWith($select);
        return $resultData;
    }
    
    public function getNewsByMonthYear($month, $year, $limit = null, $siteId = null)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->join('melis_cms_news_texts', 'melis_cms_news_texts.cnews_id = melis_cms_news.cnews_id', array('cnews_id', 'cnews_title'), $select::JOIN_LEFT);

        $select->where(array('cnews_status' => '1'));
        
        if (!is_null($siteId)) {
            $select->where->equalTo('cnews_site_id', $siteId);
        }
        
        $select->where('MONTH(cnews_publish_date) = '.$month);
        $select->where('YEAR(cnews_publish_date) ='.$year);
        $select->group("melis_cms_news.cnews_id");
        $select->where->nest->greaterThan('cnews_unpublish_date', date('Y-m-d H:i:s', strtotime("now")))->or->isNull('cnews_unpublish_date')->unnest;
        
        $select->order(array('cnews_publish_date' => 'DESC'));
        if (!is_null($limit)) {
            $select->limit($limit);
        }

        $resultData = $this->tableGateway->selectWith($select);

        return $resultData;
    }

    /**
     * get last news record
     * @return Object
     */
    public function getLastNews()
    {
        $select = $this->tableGateway->getSql()->select();

        $select->order(array('cnews_id' => 'DESC'));

        $select->limit(1);

        return $this->tableGateway->selectWith($select);
    }

     /**
     * get last news record
     * @return Object
     */
    public function getNewsData($where)
    {
        $select = $this->tableGateway->getSql()->select();
        
        $select->join('melis_cms_news_texts', 'melis_cms_news_texts.cnews_id = melis_cms_news.cnews_id', array('cnews_title', 'cnews_lang_id'), $select::JOIN_LEFT);
        
        $select->where($where);

        return $this->tableGateway->selectWith($select);
    }

}
