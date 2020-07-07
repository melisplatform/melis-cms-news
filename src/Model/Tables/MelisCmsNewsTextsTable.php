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

class MelisCmsNewsTextsTable extends MelisGenericTable 
{
    /**
     * Table name
     */
    const TABLE = 'melis_cms_news_texts';
    /**
     * Primary key
     */
    const PRIMARY_KEY = 'cnews_text_id';

    /**
     * MelisCmsNewsTextsTable constructor.
     */
    public function __construct()
    {
        $this->idField = self::PRIMARY_KEY;
    }

    /**
     * @param $set
     * @param $where
     * @return mixed
     */
    public function updateNewsText($set, $where)
    {
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

        return $this->tableGateway->selectWith($select);
    }

    /**
     * @param int|null $postId
     * @return null
     */
    public function getPostTitle(int $postId = null)
    {
        if (empty($postId)) {
            return null;
        } else {
            $select = $this->tableGateway->getSql()->select();
            $select->where->equalTo('cnews_id', $postId);
            $select->where('cnews_title !=""');
            $select->limit(1);

            $resultData = $this->tableGateway->selectWith($select);

            return $resultData;
        }
    }
}