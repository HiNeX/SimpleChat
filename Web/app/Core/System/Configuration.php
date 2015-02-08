<?php
/**
 * Configuration class.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 8:18
 */

namespace Core\System;

use Core\Base\Singleton;

class Configuration extends Singleton
{
	protected $_configurations = []; // Array of multiple configurations.
	protected $_is_edit = TRUE; // Check changes before save.

	/**
	 * @param $configuration
	 *
	 * @return bool|mixed Return loaded configuration
	 */
	protected function load($configuration)
	{
		if (!isset($this->_configurations[$configuration]))
		{
			$configuration_path = ROOT . '/app/Configurations/' . $configuration . '.json';
			if (!is_file($configuration_path))
				return FALSE; // Check file before load.

			$data                                  = file_get_contents($configuration_path);
			$this->_configurations[$configuration] = json_decode($data, TRUE); // Write configuration in array.
		}

		return $this->_configurations[$configuration];
	}

	/**
	 * Save configuration after changes
	 * @return bool
	 */
	protected function save()
	{
		if (empty($this->_configurations))
			return FALSE; // Check array before save.

		// Loop for saving all configurations.
		foreach ($this->_configurations as $configuration => $array)
		{
			$configuration_path = ROOT . '/app/Configurations/' . $configuration . '.json';
			if (!is_file($configuration_path))
				continue; // If file not found, goto next step.

			file_put_contents($configuration_path, json_encode($array));
		}

		return TRUE;
	}

	/**
	 * @param $configuration
	 * @param $key
	 *
	 * @return bool|mixed or configuration value
	 */
	public function get($configuration, $key)
	{
		$configuration = $this->load($configuration); // Load called configuration.
		return isset($configuration[$key]) ? $configuration[$key] : FALSE;
	}

	/**
	 * @param $configuration
	 * @param $key
	 * @param $value
	 *
	 * @return bool|mixed or configuration value
	 */
	public function set($configuration, $key, $value)
	{
		if ($this->load($configuration))
		{ // Check configuration before set.
			$this->_is_edit = TRUE;

			return $this->_configurations[$configuration][$key] = $value;
		}

		return FALSE;
	}

	final public function finality()
	{
		if ($this->_is_edit)
			return $this->save();

		return FALSE;
	}
}