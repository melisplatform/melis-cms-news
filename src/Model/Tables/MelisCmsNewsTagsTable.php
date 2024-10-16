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
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Sql;

class MelisCmsNewsTagsTable extends MelisGenericTable 
{
    /**
     * Table name
     */
    const TABLE = 'melis_cms_tag_entity';
    /**
     * Primary key
     */
    const PRIMARY_KEY = 'id';

    const ENTITY_TYPE = 'NEWS';

    /**
     * MelisCmsNewsSeoTable constructor.
     */
    public function __construct()
    {
        $this->idField = self::PRIMARY_KEY;
    }

    public function syncNewsTags($newsId, array $newValues = []): bool
    {
        $newsId = (int) $newsId;

        return $this->syncPivotTable(
            $this->getTableGateway()->getAdapter(), 
            self::TABLE,
            'entity_id',
            'tag_id',
            $newsId,
            $newValues,
            ['entity_type' => self::ENTITY_TYPE],
            ['entity_type' => self::ENTITY_TYPE]
        );
    }



    /**
	 * Syncs the values in a pivot table<br/>
	 * Sample Usage: syncPivotTable($this->getTableGateway()->getAdapter(), 'melis_cms_tag_entity', 'entity_id', 'tag_id', $newsId, $newValues);<br/>
	 */
    private function syncPivotTable(
        Adapter $adapter,
        string $pivotTable,
        string $pivotKey1,
        string $pivotKey2,
        $key1Value,
        array $newKey2Values,
        $where = null,
        array $otherValues = null
    ) {
        $sql = new Sql($adapter);
    
        // Fetch existing pivot table entries for the given key1Value
        $select = $sql->select();
        $select->from($pivotTable)->where([$pivotKey1 => $key1Value]);
        if(!empty($where)) {
            $select->where($where);
        }
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
    
        $existingKey2Values = [];
        foreach ($result as $row) {
            $existingKey2Values[] = $row[$pivotKey2];
        }
    
        // Determine which relations to add and which to remove
        $valuesToInsert = array_diff($newKey2Values, $existingKey2Values);
        $valuesToDelete = array_diff($existingKey2Values, $newKey2Values);
    
        // Wrap in a transaction
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            // Insert new relationships
            foreach ($valuesToInsert as $value) {
                $insert = $sql->insert($pivotTable);
                $vals = [
                    $pivotKey1 => $key1Value,
                    $pivotKey2 => $value,
                ];
                if(!empty($otherValues)) {
                    $vals = [...$vals, ...$otherValues];
                }
                $insert->values($vals);
    
                $statement = $sql->prepareStatementForSqlObject($insert);
                $statement->execute();
            }
    
            // Delete outdated relationships
            foreach ($valuesToDelete as $value) {
                $delete = $sql->delete($pivotTable);
                $delete->where([
                    $pivotKey1 => $key1Value,
                    $pivotKey2 => $value,
                ]);
    
                $statement = $sql->prepareStatementForSqlObject($delete);
                $statement->execute();
            }
    
            // Commit transaction
            $adapter->getDriver()->getConnection()->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            $adapter->getDriver()->getConnection()->rollback();
            throw $e;
            return false;
        }
    }
}