<?php
/**
 * Simple session control class.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 21:08
 */

namespace Core\System;

use Core\Base\Singleton;

class Session extends Singleton
{
	private $_start = FALSE;
	private $_session_id;

	/**
	 * @return bool
	 */
	public function start()
	{
		if (!$this->_start)
		{
			session_start();

			$this->_session_id = session_id();
			$this->_start      = TRUE;
		}

		return TRUE;
	}

	/**
	 * @param $index
	 *
	 * @return bool|mixed
	 */
	public function get($index)
	{
		$this->start();

		return isset($_SESSION[$index]) ? $_SESSION[$index] : FALSE;
	}

	/**
	 * @param $index
	 * @param $value
	 *
	 * @return mixed
	 */
	public function set($index, $value)
	{
		$this->start();

		return $_SESSION[$index] = $value;
	}

	public function remove($index)
	{
		$this->start();

		if (isset($_SESSION[$index])) { unset($_SESSION[$index]); return true; }

		return false;
	}


	/**
	 * @return mixed
	 */
	public function getSessionId()
	{
		return $this->_session_id;
	}

	/**
	 * @return bool
	 */
	public function destroy()
	{
		if ($this->_start)
		{
			if (ini_get("session.use_cookies")) {
				$params = session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000,
					$params["path"], $params["domain"],
					$params["secure"], $params["httponly"]
				);
			}

			session_destroy();
			$this->_start = FALSE;
		}

		return $this->_start;
	}
}