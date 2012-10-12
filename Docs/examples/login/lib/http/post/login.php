<?php
class WebPage extends ZhostingPagePost {

	public function login(){
		$input = InputHandler::getInstance();
		$input->validatePost('login-username', new InputValidatorRegex('/^([a-zA-Z0-9\-\_]+)$/'));
		$input->validatePost('login-password', new InputValidatorNotEmpty());
		$input->validatePost('r', new InputValidatorNotEmpty());

		$input->addStripSerializePostVariable('login-password');
		$input->addStripSerializePostVariable('login-username');

		$auth = UserAuthorization::getInstance();
		$auth->logout();

		if($input->isValidPostVariables('login-username', 'login-password')){
			$user = new User();
			if($user->getByUsername($input->getPost('login-username')) && $user->checkPassword($input->getPost('login-password'))){
				$auth->authorize($user);
			}
		}

		$input->addStripSerializePostVariable('login-password');
		$input->addStripSerializePostVariable('login-username');
		$input->unsetPost('login-password');
		$input->unsetPost('login-username');

		if($auth->isAuthorized()){
			if($input->isValidPost('r')){
				$this->post->setLocation($input->getPost('r'));
			} else {
				$this->post->setLocation('/');
			}
		} else {
			$this->post->setLocation('login/', $input->serializePost());
		}
	}

}
?>