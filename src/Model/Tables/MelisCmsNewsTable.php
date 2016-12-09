<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Model\Tables;

use Zend\Db\TableGateway\TableGateway;

class MelisCmsNewsTable extends MelisEcomGenericTable 
{
    protected $tableGateway;
    protected $idField;
    
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
        $this->idField = 'cnews_id';
    }
    
    public function getNewsList($status = null, $start = null, $limit = null, $order = null, $search = null )
    {
        $select = $this->tableGateway->getSql()->select();
        $clause = array();
        
        if(!is_null($search)){
            $search = '%'.$search.'%';
            $select->where->NEST->like('cnews_id', $search)
            ->or->like('cnews_title', $search);
        }
        
        if (!is_null($start))
        {
            $select->offset($start);
        }
        
        if (!is_null($limit)&&$limit!=-1)
        {
            $select->limit($limit);
        }
        
        if (!is_null($order))
        {
            $select->order($order);
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
    
}