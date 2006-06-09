<?php
class MySQLi_UsersInfo extends DatabaseDAO implements Singleton,DAO_UsersInfo {
	private static $instance = null;
	
	/**
	 *	@return Database
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersInfo();
		}
		return self::$instance;	
	}
	
	public function getUsersInfoIN($in, $uid=null, $selected=false){
		$query = 'SELECT pk_users_info, fk_users_info_items, info_title, ignore_title, has_items
		          FROM tbl_users_info
		          WHERE pk_users_info IN('.$in.')';
		if(!is_null($uid)){
			$query = 'SELECT pk_users_info, tbl_users_info.fk_users_info_items, info_title, ignore_title, value, has_items
			          FROM tbl_users_info
			          LEFT JOIN tbl_users_has_info ON pk_users_info=fk_users_info AND fk_users=\''.$uid.'\' AND tbl_users_has_info.fk_users_info_items IS NULL 
			          WHERE pk_users_info IN('.$in.')';
		}
		return $this->slaveQuery(new MySQLiQuery($query));
	}

	public function getInfoFromParent($id){
		$query = 'SELECT pk_users_info, fk_users_info_items, info_title, ignore_title, has_items
		          FROM tbl_users_info
		          WHERE fk_users_info_items=\''.$id.'\'';
		return $this->slaveQuery(new MySQLiQuery($query));
	}
	
	public function getInfoItemsFromParent($id, $uid=null, $selected=false){
		if(is_null($uid)){
			$query = 'SELECT pk_users_info_items, fk_users_info, item_title, has_items
			          FROM tbl_users_info_items
			          WHERE fk_users_info=\''.$id.'\'';
		} else if($selected){
			$query = 'SELECT pk_users_info_items, tbl_users_info_items.fk_users_info, item_title, fk_users AS pk_users, has_items
			          FROM tbl_users_info_items
			          INNER JOIN tbl_users_has_info ON pk_users_info_items=fk_users_info_items AND fk_users=\''.$uid.'\'
			          WHERE tbl_users_info_items.fk_users_info=\''.$id.'\'';
		} else {
			$query = 'SELECT pk_users_info_items, tbl_users_info_items.fk_users_info, item_title, fk_users AS pk_users, has_items
			          FROM tbl_users_info_items
			          LEFT JOIN tbl_users_has_info ON pk_users_info_items=fk_users_info_items AND fk_users=\''.$uid.'\'
			          WHERE tbl_users_info_items.fk_users_info=\''.$id.'\'';
		}
		return $this->slaveQuery(new MySQLiQuery($query));
	}
	
	public function ifInfoItemExcists($item, $value=null){
		$query = 'SELECT pk_users_info
		          FROM tbl_users_info ';
		if(!is_null($value)){
			$query .= 'INNER JOIN tbl_users_info_items ON pk_users_info=fk_users_info AND pk_users_info_items=\''.$value.'\'';	
		}
		$query .= ' WHERE pk_users_info = \''.$item.'\'';	
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return true;
		} else {
			return false;	
		}
	}
	
	public function setUserInfoItem($uid, $id, $item = null, $value=null){
		if(is_null($item)){
			$item = 'NULL';	
		} else {
			$item = '\''.$item.'\'';	
		}
		if(is_null($value)){
			$value = 'NULL';	
		} else {
			$value = '\''.$value.'\'';	
		}
		$query = 'DELETE FROM tbl_users_has_info
		          WHERE fk_users=\''.$uid.'\' AND fk_users_info=\''.$id.'\'';
		$this->masterQuery(new MySQLiQuery($query));
		$query = 'INSERT INTO tbl_users_has_info(fk_users, fk_users_info, fk_users_info_items, value)
		          VALUES(\''.$uid.'\', \''.$id.'\', '.$item.', '.$value.')';
		$query = $this->masterQuery(new MySQLiQuery($query));
		return true;
	}
}
?>