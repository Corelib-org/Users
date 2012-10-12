<?php
class WebPage extends ZhostingPageGet {

	public function login(){
		$this->xsl->addTemplate('pages/login.xsl');
	}

}
?>