<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Model\Tables;

use MelisCore\Model\Tables\MelisGenericTable;

class MelisCmsNewsWorkflowCommentTypeTable extends MelisGenericTable
{
	/**
	 * Model table
	 */
	const TABLE = 'melis_cms_news_workflow_comment_type';

	/**
	 * Table primary key
	 */
	const PRIMARY_KEY = 'cnews_comt_id';

	public function __construct()
	{
		$this->idField = self::PRIMARY_KEY;
	}
}
