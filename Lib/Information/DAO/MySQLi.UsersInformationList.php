<?php
class MySQLi_UsersInformationList extends DatabaseDAO implements Singleton,DAO_UsersInformationList {
	private static $instance = null;
	
	/**
	 *	@return MySQLi_UsersInformationList
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersInformationList();
		}
		return self::$instance;	
	}
	
	public function getList(DatabaseListHelperFilter $filter){
		
		if($filter->count() > 0){
			$where = 'WHERE 1 ';
			if($userid = $filter->get(UsersInformation::FIELD_USER_ID)){
				$where .= 'AND '.UsersInformation::FIELD_USER_ID.'=\''.($userid).'\' ';
			}
			if($fields = $filter->get(UsersInformation::FIELD_INFOMATION_ID)){
				$where .= 'AND '.UsersInformation::FIELD_INFOMATION_ID.' IN('.implode(',', $fields).') ';
			}
		} else {
			$where = '';
		}
		
		$query = 'SELECT '.MySQLi_UsersInformation::SELECT_COLUMNS.'
		          FROM tbl_users_has_information
		          '.$where;
		return $this->query(new MySQLiQuery($query));
	}
}
?>