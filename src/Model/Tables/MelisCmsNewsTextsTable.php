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

class MelisCmsNewsTextsTable extends MelisGenericTable 
{
    protected $tableGateway;
    protected $idField;
    
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
        $this->idField = 'cnews_text_id';
    }

    public function updateNewsText($set, $where)
    {
    	//die(var_dump($set));
        $update = $this->tableGateway->getSql()->update();
        $update->set($set);
        $update->where($where);

        // Execute the query
        return $this->tableGateway->updateWith($update);
    }

    /**
     * get last news record
     * @return Object
     */
    public function getNewsDataByCnd($where, $title = null)
    {
        $select = $this->tableGateway->getSql()->select();
        if ($title) {
             $select->where('cnews_title !=""');
        }
       
        $select->where($where);

        //$select->where->nest->greaterThan('cnews_unpublish_date', date('Y-m-d H:i:s', strtotime("now")))->or->isNull('cnews_unpublish_date')->unnest;

        return $this->tableGateway->selectWith($select);
    }


}