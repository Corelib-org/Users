<?php

interface DAO_UsersCategories {
	public function deleteCategory($uid,$id);
	public function setCategory($uid, $id);
	public function getCategories($uid);
}

class UsersCategory extends UserDecorator {
	/**
	 * 	@var category
	 */
	private $category = null;
	
	const LIBRARY = 'UsersCategories';
	
	public function __construct($id=null,$category=null,$count=null){
		$this->set($id,$category,$count);
	}
	
	public function getXML(DOMDocument $xml){
		return $this->buildXML($xml, $this->category->getXML($xml));
	}
	
	public function getID(){
		return $this->decorator->getUID();	
	}
	
	public function set($id=null,$category=null,$count=null){
		$this->category = new Category($id,$category,$count);
	}
	
	public function save(){
		if(!is_null($this->category)){
			if(is_null($this->dao)){
				$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
			}
			$id = $this->category->commit();
			$this->dao->setCategory($this->getID(), $id);
			return $id;	
		}
	}
	public function delete() {
		if(!is_null($this->category)){
			if(is_null($this->dao)){
				$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
			}
			$this->dao->deleteCategory($this->getID(),$this->category->getCategoryID());
			$this->category->deleteCategory();
		}
	}
}


class UsersCategories extends UserDecorator  {
	private $categories = array();
	private $count;
	
	const LIBRARY = 'UsersCategories';
	
	public function __construct($count=false){
		$this->count = $count;
	}
	public function setCategory($id,$category=null){
		$usercat = new UsersCategory($id,$category);
		$usercat->decorate($this->decorator);
		$this->categories[] = $usercat;
	}
	
	public function save(){
	 	while(list(,$val) = each($this->categories)){
			$val->save();
		}
	}
	public function getXML(DOMDocument $xml){
		$DOMCategories = $xml->createElement('categories');
		$res = $this->_getCategories();
		while($out = $res->fetchArray()){
			if($this->count == true) {
				$cat = new UsersCategory($out['pk_categories'],$out['category'],$out['count']);
			} else {
				$cat = new UsersCategory($out['pk_categories'],$out['category']);
			}
			$DOMCategories->appendChild($cat->getXML($xml));
		}
		return $this->buildXML($xml, $DOMCategories);
	}	
	private function _getCategories(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
		}
		if($this->count == true) {
			return $this->dao->getCategoriesCount($this->getID());
		} else {
			return $this->dao->getCategories($this->getID());
		}
	}
	public function getID(){
		return $this->decorator->getUID();
	}
}
?>