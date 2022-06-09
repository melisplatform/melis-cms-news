<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Model\Tables;

use MelisEngine\Model\Tables\MelisGenericTable;

class MelisCmsNewsWorkflowEventsTable extends MelisGenericTable
{
	/**
	 * Model table
	 */
	const TABLE = 'melis_cms_news_workflow_events';

	/**
	 * Table primary key
	 */
	const PRIMARY_KEY = 'cnews_wfe_id';

	public function __construct()
	{
		$this->idField = self::PRIMARY_KEY;
	}
	
	public function getWorkflowEventsById($wfId)
	{
		$select = $this->getTableGateway()->getSql()->select();
	
		$select->columns(array('*'));

		$where = new \Laminas\Db\Sql\Where();
		$where->equalTo('cnews_wfe_wf_id', $wfId);
		$select->where($where);

		$resultSet = $this->getTableGateway()->selectWith($select);
	
		return $resultSet;
	}
}
