<?php
/**
 * Register class.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 0:53
 */

namespace Core\System;

use Core\Base\Singleton;

class Register extends Singleton
{
	private $_register = [];

	/**
	 * @param $get
	 *
	 * @return bool|mixed
	 */
	public function get($get)
	{
		return isset($this->_register[$get]) ? $this->_register[$get] : FALSE;
	}

	/**
	 * @param $set
	 * @param $value
	 *
	 * @return mixed
	 */
	public function set($set, $value)
	{
		return $this->_register[$set] = $value;
	}

	/**
	 * @param $has
	 *
	 * @return bool
	 */
	public function has($has)
	{
		return isset($this->_register[$has]);
	}
}