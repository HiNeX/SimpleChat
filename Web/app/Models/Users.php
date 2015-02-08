<?php
/**
 * User model.
 * Author: Roman Hinex
 * Date: 11.12.14
 * Time: 1:40
 */

namespace Models;


use Core\Base\Model;

class Users extends Model {

	protected static function init() {
//		self::useTable('app_users');
	}

	public function getId() {
		return $this->id;
	}

	public function getNickname() {
		return $this->nickname;
	}

	public function setNickname($nickname) {
		return $this->nickname = $nickname;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		return $this->password = $password;
	}

	public function getOnline() {
		return $this->online;
	}

	public function setOnline($online) {
		return $this->online = $online;
	}

	public function getToken() {
		return $this->token;
	}

	public function setToken($token) {
		return $this->token = $token;
	}
}