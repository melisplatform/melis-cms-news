<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Model\Tables;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use MelisEngine\Model\Tables\MelisGenericTable;

class MelisCmsNewsTable extends MelisGenericTable 
{
    protected $tableGateway;
    protected $idField;
    
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
        $this->idField = 'cnews_id';
    }
    
    public function getNewsList(
        $status = null, $dateMin = null, $dateMax = null, $publishDateMin = null, 
        $publishDateMax = null, $unpublishFilter = false, $start = null, $limit = null, 
        $orderColumn = 'cnews_id', $order = null, $siteId = null, $search = null 
    )
    {
        $select = $this->tableGateway->getSql()->select();
        $clause = array();
        
        $select->join('melis_cms_site', 'melis_cms_site.site_id = melis_cms_news.cnews_site_id', array('site_name'), $select::JOIN_LEFT);
        
        if(!is_null($search)){
            $search = '%'.$search.'%';
            $select->where->NEST->like('cnews_id', $search)
            ->or->like('cnews_title', $search);
        }
        
        if(!is_null($siteId)){
            $select->where->equalTo('cnews_site_id', $siteId);
        }
        
        if (!is_null($status))
        {
            $select->where('cnews_status ='.$status);
        }
        
        if (!is_null($dateMin))
        {
            $select->where('cnews_creation_date >= "'.$dateMin.'"');
        }
        
        if (!is_null($dateMax))
        {
            $select->where('cnews_creation_date <= "'.$dateMax.'"');
        }
        
        if (!is_null($publishDateMin))
        {
            $select->where('cnews_publish_date >= "'.$publishDateMin.'"');
        }
        
        if (!is_null($publishDateMax))
        {
            $select->where('cnews_publish_date <= "'.$publishDateMax.'"');
        }
        
        if($unpublishFilter){
            $select->where->nest->greaterThan('cnews_unpublish_date', date("Y-m-d H:i:s", strtotime("now")))->or->isNull('cnews_unpublish_date')->unnest;
        }
        
        if (!is_null($start))
        {
            $select->offset($start);
        }
        
        if (!is_null($limit)&&$limit!=-1)
        {
            $select->limit($limit);
        }
        
        if (!is_null($orderColumn) && !is_null($order))
        {
            $select->order(array($orderColumn => $order));
        }
        
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
        
        $select->group(array(new Expression('MONTH(cnews_publish_date)'), new Expression('YEAR(cnews_publish_date)')));
        $select->order(array('cnews_publish_date' => 'DESC'));
        
        
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
        $select->columns(array('cnews_id', 'cnews_title'));
        $select->where(array('cnews_status' => '1'));
        
        if(!is_null($siteId)){
            $select->where->equalTo('cnews_site_id', $siteId);
        }
        
        $select->where('MONTH(cnews_publish_date) = '.$month);
        $select->where('YEAR(cnews_publish_date) ='.$year);
        
        $select->where->nest->greaterThan('cnews_unpublish_date', date('Y-m-d H:i:s', strtotime("now")))->or->isNull('cnews_unpublish_date')->unnest;
        
        $select->order(array('cnews_publish_date' => 'DESC'));
        if (!is_null($limit))
        {
            $select->limit($limit);
        }
        
        
        $resultData = $this->tableGateway->selectWith($select);
        return $resultData;
    }
}