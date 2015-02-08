<?php
/**
 * User management.
 * Author: Roman Hinex
 * Date: 12.01.15
 * Time: 0:59
 */

namespace Models\Extended;


use Core\Base\Singleton;
use Core\System\Session;
use Models\Users;

class User extends Singleton {
	protected $_user = false;
	protected $db;

	protected function load() {}

	protected function passwordHash($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	protected function generatePassword() {
		return substr(hash('sha512',rand()),4,12);
	}

	protected function getUser ($nickname) {

		if (preg_match("/^[a-zA-Z0-9]+$/", $nickname) AND $user = Users::find([['nickname', '=', $nickname]])) {
			return $user->getId()?$user:false;
		}

		return false;
	}

	public function auth() {
		$session = Session::main();
		$user = Users::find($session->get('user_id'));

		if ($session->get('user_id') AND $user->getId()) {
			if ($user->getToken() !== $session->getSessionId()) {
				$session->destroy();
			} else {
				$this->_user = $user;
				$user->setOnline(date( 'Y-m-d H:i:s'));
				$user->save();
				return ['success' => true, 'result' => $user];
			}
		}

		return ['success' => false, 'errormessage' => 'User is not logged', 'errorcode' => 2];
	}

	public function signin ($nickname, $password) {
		$this->load();

		if ($user = $this->getUser($nickname)) {
			if (password_verify($password, $user->getPassword())) {
				$session = Session::main();
				$session->set('user_id', $user->getId());
				$token = $session->getSessionId();

				$user->setToken($token);
				$user->save();

				return ['success' => true, 'result' => ['token' => $token]];
			}
		}

		return ['success' => false];
	}

	public function signup ($nickname, $password) {
		$this->load();

		if (!$this->getUser($nickname)) {
			$session = Session::main();
			$session->start();

			$token = $session->getSessionId();

			$password = $this->passwordHash($password);

			$user = Users::create();
			$user->setNickname($nickname);
			$user->setPassword($password);
			$user->setToken($token);

			if ($save = $user->save()) {
				$session->set('user_id', $user->getId());
			}


			return ['success' => $save, 'result' => ['token' => $token]];

		}

		return ['success' => false, 'errormessage' => 'User already exists', 'errorcode' => 7];
	}

	public function change($password, $new_password) {
		$this->load();

		if ($this->_user !== false)
		{
			if (password_verify($password, $this->_user->getPassword()))
			{
				$this->_user->setPassword($this->passwordHash($new_password));
				$this->_user->save();

				return true;
			}
		}

		return false;
	}

	public function restore($nickname) {
		$this->load();

		if ($user = $this->getUser($nickname)) {
			$password = $this->generatePassword();

			$user->setPassword($this->passwordHash($password));
			$user->save();

			return $password;
		}

		return false;
	}

	public function check($token) {
		$user = Users::find(['token' => $token]);
		return $user->getId()?true:false;
	}
}