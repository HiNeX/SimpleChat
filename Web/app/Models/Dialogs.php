<?php
/**
 * User model.
 * Author: Roman Hinex
 * Date: 11.12.14
 * Time: 1:40
 */

namespace Models;


use Core\Base\Model;

class Dialogs extends Model {

	protected static function init() {
//		self::useTable('app_users');
	}

	public function getId() {
		return $this->id;
	}

	public function getFrom() {
		return $this->from;
	}

	public function setFrom($from) {
		return $this->from = $from;
	}

	public function getTo() {
		return $this->to;
	}

	public function setTo($to) {
		return $this->to = $to;
	}

	public function getNew() {
		return $this->new;
	}

	public function setNew($new) {
		return $this->new = $new;
	}

}