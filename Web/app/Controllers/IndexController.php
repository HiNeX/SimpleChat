<?php
/**
 * Main Page.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 5:39
 */

namespace Controllers;


use Core\Base\Controller;

class IndexController extends Controller {
	public function indexAction() {
		if ($this->request->hasPost('nickname') and $this->request->hasPost('password')) {
			$nickname = $this->request->getPost('nickname');
			$password = $this->request->getPost('password');

			if ($this->request->hasPost('signin')) {
				$this->user->signin($nickname, $password);
				$this->display->set('message', ['type' => 'error', 'text' => $this->translation->get('Login failed')]);
			} else {
				$this->user->signup($nickname, $password);
				$this->display->set('message', ['type' => 'error', 'text' => $this->translation->get('Nickname exist')]);
			}
		}

		$auth = $this->user->auth();
		if ($auth['success']) {
			$this->request->redirect(['chat', 'index']);
		}

		$this->display->setTitle('Online Chat!');
		$this->display->render();
	}

	public function languageAction() {
		$language = $this->translation->getLanguage();
		$language = ($language === 'english')?'russian':'english';
		$this->translation->setLanguage($language);
		if ($url = $this->request->getQuery('return')){
			$this->request->redirect($url);
		} else {
			$this->request->redirect(['index', 'index']);
		}
	}
}