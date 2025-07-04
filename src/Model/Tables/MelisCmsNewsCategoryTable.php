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

class MelisCmsNewsCategoryTable extends MelisGenericTable 
{
    /**
     * Table name
     */
    const TABLE = 'melis_cms_news_category';
    /**
     * Primary key
     */
    const PRIMARY_KEY = 'cnc_id';

    /**
     * MelisCmsNewsSeoTable constructor.
     */
    public function __construct()
    {
        $this->idField = self::PRIMARY_KEY;
    }
}