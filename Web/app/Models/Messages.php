<?php
/**
 * User model.
 * Author: Roman Hinex
 * Date: 11.12.14
 * Time: 1:40
 */

namespace Models;


use Core\Base\Model;

class Messages extends Model {

	protected static function init() {
//		self::useTable('app_users');
	}

	public function getId() {
		return $this->id;
	}

	public function getDialogId() {
		return $this->dialog_id;
	}

	public function setDialogId($dialog_id) {
		return $this->dialog_id = $dialog_id;
	}

	public function getUserId() {
		return $this->user_id;
	}

	public function setUserId($user_id) {
		return $this->user_id = $user_id;
	}

	public function getTime() {
		return $this->time;
	}

	public function setTime($time) {
		return $this->time = $time;
	}

	public function getMessage() {
		return $this->message;
	}

	public function setMessage($message) {
		return $this->message = $message;
	}

}