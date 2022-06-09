<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Model\Tables;

use MelisCore\Model\Tables\MelisGenericTable;

class MelisCmsNewsUserRoleTable extends MelisGenericTable
{
	/**
	 * Model table
	 */
	const TABLE = 'melis_core_user_role';

	/**
	 * Table primary key
	 */
	const PRIMARY_KEY = 'urole_id';

	public function __construct()
	{
		$this->idField = self::PRIMARY_KEY;
	}
}
