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
}