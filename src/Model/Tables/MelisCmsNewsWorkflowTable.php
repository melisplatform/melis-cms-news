<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Model\Tables;

use MelisEngine\Model\Tables\MelisGenericTable;

class MelisCmsNewsWorkflowTable extends MelisGenericTable
{
	/**
	 * Model table
	 */
	const TABLE = 'melis_cms_news_workflow';

	/**
	 * Table primary key
	 */
	const PRIMARY_KEY = 'cnews_wf_id';

	public function __construct()
	{
		$this->idField = self::PRIMARY_KEY;
	}
	
	public function getWorkflowDemandsToByUserIdAndRoleId($userId, $roleId = null)
	{
		$select = $this->getTableGateway()->getSql()->select();

		$select->columns(array('*'));
		
		$select->join('melis_cms_news_workflow_events', 'melis_cms_news_workflow_events.cnews_wfe_wf_id = melis_cms_news_workflow.cnews_wf_id',
				array('*'), $select::JOIN_RIGHT);

		if (!empty($userId) && !empty($roleId))
		{
			$where = new \Laminas\Db\Sql\Where();
			$where->nest()
				->equalTo('cnews_wfe_to_user_id', $userId)
				->or
				->equalTo('cnews_wfe_to_role_id', $roleId)
				->unnest()
				->and
				->equalTo('cnews_wfe_action', 'VALIDATION');
			$select->where($where);
		}
		else
			if (!empty($userId))
			{
				$where = new \Laminas\Db\Sql\Where();
				$where->nest()
					->equalTo('cnews_wfe_to_user_id', $userId)
					->unnest()
					->and
					->equalTo('cnews_wfe_action', 'VALIDATION');
				$select->where($where);
			}

		$select->order(array('cnews_wf_process_finished', 'cnews_wf_id DESC'));

		$select->limit(50);

		$resultSet = $this->getTableGateway()->selectWith($select);

		return $resultSet;
	}

	public function getWorkflowDemandsFromByUserIdAndRoleId($userId)
	{
		$select = $this->getTableGateway()->getSql()->select();

		$select->columns(array('*'));

		$select->join('melis_cms_news_workflow_events', 'melis_cms_news_workflow_events.cnews_wfe_wf_id = melis_cms_news_workflow.cnews_wf_id',
			array('*'), $select::JOIN_RIGHT);

		if (!empty($userId))
		{
			$where = new \Laminas\Db\Sql\Where();
			$where->nest()
				->equalTo('cnews_wfe_from_user_id', $userId)
				->unnest()
				->and
				->equalTo('cnews_wfe_action', 'VALIDATION');
			$select->where($where);
		}

		$select->order(array('cnews_wfe_date DESC', 'cnews_wf_process_finished DESC', 'cnews_wf_id DESC', 'cnews_wfe_id DESC'));

		$select->limit(50);

		$resultSet = $this->getTableGateway()->selectWith($select);


		return $resultSet;
	}

	public function getWorkflowByTypeAndId($type, $itemId, $limit = 100, $order = 'DESC')
	{
		$select = $this->getTableGateway()->getSql()->select();

		$select->columns(array('*'));

		$select->join('melis_cms_news_workflow_events', 'melis_cms_news_workflow_events.cnews_wfe_wf_id = melis_cms_news_workflow.cnews_wf_id',
			array('*'), $select::JOIN_RIGHT);

		$where = new \Laminas\Db\Sql\Where();
		$where->nest()
			->equalTo('cnews_wf_item_key_id', $itemId)
			->and
			->equalTo('cnews_wf_type', $type)
			->unnest();
		$select->where($where);

		$select->order(array('cnews_wf_id ' . $order, 'cnews_wfe_id ' . $order));
		$select->limit($limit);
		$resultSet = $this->getTableGateway()->selectWith($select);

		return $resultSet;
	}

	public function getUnvalidatedWorkflowByTypeAndId($type, $itemId, $limit = 100)
	{
		$select = $this->getTableGateway()->getSql()->select();

		$select->columns(array('*'));

		$select->join('melis_cms_news_workflow_events', 'melis_cms_news_workflow_events.cnews_wfe_wf_id = melis_cms_news_workflow.cnews_wf_id',
			array('*'), $select::JOIN_RIGHT);

		$where = new \Laminas\Db\Sql\Where();
		$where->nest()
			->equalTo('cnews_wf_item_key_id', $itemId)
			->and
			->equalTo('cnews_wf_type', $type)
			->and
			->equalTo('cnews_wfe_action', 'VALIDATION')
			->unnest();
		$select->where($where);

		$select->order(array('cnews_wf_id DESC', 'cnews_wfe_id DESC'));
		$select->limit($limit);
		$resultSet = $this->getTableGateway()->selectWith($select);

		return $resultSet;
	}
}
