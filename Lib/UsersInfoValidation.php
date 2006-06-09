<?php

Class UserInfoInputValidator implements InputValidator {
	private $dao;
	private $expr;
	private $ref_check;
	private $validator;
	private $depth;
	
	const LIBRARY = 'UsersInfo';
	
	public function __construct($expr, $ref_check=true, $validator=null, $depth=null){
		$this->expr = $expr;
		$this->ref_check = $ref_check;
		$this->validator = $validator;
		$this->depth = $depth;
	}
	
	public function validate($content){
		$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
		if($this->ref_check){
			if(is_null($this->depth)){
				if($this->dao->ifInfoItemExcists($this->expr, $content)){
					return $this->expr;	
				} else {
					return false;	
				}
			} else {
				return $this->_recursiveCheck($this->expr, $content, $this->depth);	
			}
		} else if($validator instanceof InputValidator){
			if($this->dao->ifInfoItemExcists($this->expr) && $validator->validate($content)){
				return $this->expr;
			} else {
				return false;
			}	
		} else {
			return $this->dao->ifInfoItemExcists($item);
		}
	}
	
	private function _recursiveCheck($expr, $content, $maxdepth, $depth=0){
		if($this->dao->ifInfoItemExcists($expr, $content)){
			return $expr;
		} else {
			if($depth > $maxdepth){
				return false;	
			} else {
				$depth++;
				$query = $this->dao->getInfoItemsFromParent($expr);
				while($out = $query->fetchArray()){
					$checkQ  = $this->dao->getInfoFromParent($out['pk_users_info_items']);
					while($cout = $checkQ->fetchArray()){
						if($return = $this->_recursiveCheck($cout['pk_users_info'], $content, $maxdepth, $depth)){
							return $return;
						}
					}
				}
			}
		}
		return false;
	}
}
?>