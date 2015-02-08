<?php
/**
 * Simple translation class with JSON files.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 21:06
 */

namespace Core\System;

use Core\Base\Singleton;

class Translation extends Singleton
{
	private $_language = ['language' => '', 'container' => ''];

	/**
	 * @return bool
	 */
	private function load()
	{
		$language = Session::main()->get('language');
		$language = ($language !== FALSE) ? $language : Configuration::main()->get('general', 'language');

		if ($language !== $this->_language['language'])
		{
			;
			$translation = ROOT . '/app/Translations/' . $language . '.json';

			if (is_file($translation))
			{
				$this->_language['language']  = $language;
				$this->_language['container'] = json_decode(file_get_contents($translation), TRUE);
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param $language
	 */
	public function setLanguage($language)
	{
		if (preg_match('/^[a-zA-Z]+$/', $language))
		{
			Session::main()->set('language', $language);
		}

		$this->load();
	}

	public function getLanguage() {
		return Session::main()->get('language');
	}

	/**
	 * @param null $name
	 *
	 * @return mixed|null
	 */
	public function get($name = NULL)
	{
		$this->load();

		$prepare   = [];
		$arguments = func_get_args();

		if (isset($this->_language['container'][$name]))
		{
			$prepare[] = $this->_language['container'][$name];

			for ($i = 1; $i < func_num_args(); $i ++)
			{
				array_push($prepare, $arguments[$i]);
			}

			return call_user_func_array('sprintf', $prepare);
		}

		return $name;
	}
}