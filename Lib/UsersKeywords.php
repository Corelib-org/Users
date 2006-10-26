<?php

interface DAO_UsersKeywords {
	public function flushKeywords($uid);
	public function setKeyword($uid, $keyword);
}

class UsersKeyword extends UserDecorator {
	/**
	 * 	@var Keyword
	 */
	private $keyword = null;
	
	const LIBRARY = 'UsersKeywords';
	
	public function __construct($keyword,$id=null){
		$this->set($keyword,$id);
	}
	
	public function getXML(DOMDocument $xml){
		return $this->buildXML($xml, $this->keyword->getXML($xml));
	}
	
	public function getUID(){
		return $this->decorator->getUID();	
	}
	
	public function set($keyword,$id){
		$this->keyword = new Keyword($keyword,$id);
	}
	
	public function commit(){
		if(!is_null($this->keyword)){
			if(is_null($this->dao)){
				$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
			}
			$this->dao->setKeyword($this->getUID(), $this->keyword->getKeywordID());	
		}
	}
}

class UsersKeywords extends UserDecorator {
	private $keywords = array();
	
	const LIBRARY = 'UsersKeywords';
	
	public function setKeywordsFromString($string, $seperator=','){
		$keywords = KeywordTools::parseKeywordList($string, $seperator);
		while(list($key,$val) = each($keywords)){
			$this->keywords[$key] = new UsersKeyword($val);
			$this->keywords[$key]->decorate($this->decorator);
		}
	}
	public function flush(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
		}
		$this->dao->flushKeywords($this->getUID());	
	}
	public function commit(){
	 	while(list(,$val) = each($this->keywords)){
			$val->commit();
		}
	}
	public function getXML(DOMDocument $xml){
		$DOMKeywords = $xml->createElement('keywords');
		$res = $this->_getKeywords();
		while($out = $res->fetchArray()){
			$keyword = new UsersKeyword($out['keyword'],$out['pk_keywords']);
			$DOMKeywords->appendChild($keyword->getXML($xml));
		}
		return $this->buildXML($xml, $DOMKeywords);
	}
	
	private function _getKeywords(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
		}
		return $this->dao->getKeywords($this->getUID());
	}
	
	public function getUID(){
		return $this->decorator->getUID();	
	}
}
?>