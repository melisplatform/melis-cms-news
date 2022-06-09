<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Model\Tables;

use MelisCore\Model\Tables\MelisGenericTable;

class MelisCmsNewsWorkflowCommentTable extends MelisGenericTable
{
	/**
	 * Model table
	 */
	const TABLE = 'melis_cms_news_workflow_comment';

	/**
	 * Table primary key
	 */
	const PRIMARY_KEY = 'cnews_com_id';

	public function __construct()
	{
		$this->idField = self::PRIMARY_KEY;
	}
	
	public function getComments($pageId, $workflowType = null)
	{
		$select = $this->getTableGateway()->getSql()->select();
		
		$select->columns(array('*'));
		$select->order('cnews_com_date DESC');
		$select->where(array('cnews_com_news_id' => $pageId));
		
		if (!empty($workflowType)) {
			$select->join(array('wf' => 'melis_cms_news_workflow'), 'wf.cnews_wf_item_key_id = melis_cms_news_workflow_comment.cnews_com_news_id', [], $select::JOIN_LEFT);
			$select->where(array('cnews_wf_type' => trim(strtoupper($workflowType))));
			$select->group('cnews_com_id');
		}

		$resultSet = $this->getTableGateway()->selectWith($select);
		
		return $resultSet;
	}
}
