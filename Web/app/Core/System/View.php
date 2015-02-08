<?php
/**
 * Native template engine realisation.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 22:59
 */

namespace Core\System;

use Core\Base\Singleton;

class View extends Singleton
{
	private $_array = [];
	private $_title = 'Default';
	private $_keywords;
	private $_description;
	private $_media = ['css' => [], 'js' => []];
	private $t;
	private $route;
	private static $_action;

	/**
	 * @param $controller
	 * @param $action
	 *
	 * @return string
	 */
	private function generateContent($controller, $action)
	{
		$path = ROOT . '/app/Templates/' . $controller . '/' . $action . '.phtml';

		if (isset($path))
		{
			ob_start();
			include_once $path;
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		return 'Template "' . $action . '" not found';
	}

	/**
	 *
	 */
	private function initTranslation()
	{
		$this->t = Translation::main();
	}

	/**
	 *
	 */
	private function initRoute()
	{
		$this->route = Route::main();
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function setTitle($value)
	{
		return $this->_title = $value;
	}

	/**
	 * @return string
	 */
	protected function getTitle()
	{
		return $this->_title;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function setKeywords($value)
	{
		return $this->_keywords = $value;
	}

	/**
	 * @return mixed
	 */
	protected function getKeywords()
	{
		return $this->_keywords;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function setDescription($value)
	{
		return $this->_description = $value;
	}

	/**
	 * @return mixed
	 */
	protected function getDescription()
	{
		return $this->_description;
	}

	/**
	 * @param $index
	 * @param $value
	 *
	 * @return mixed
	 */
	public function set($index, $value)
	{
		return $this->_array[$index] = $value;
	}

	/**
	 * @param $index
	 *
	 * @return bool
	 */
	protected function get($index)
	{
		return isset($this->_array[$index]) ? $this->_array[$index] : FALSE;
	}

	/**
	 * @return mixed
	 */
	protected function getContent()
	{
		return static::$_action;
	}

	/**
	 * @return bool|string
	 */
	protected function getCSS()
	{
		if (empty($this->_media['css']))
			return FALSE;

		$css = [];

		foreach ($this->_media['css'] as $value)
		{
			$css[] = '<link rel="stylesheet" href="' . $value . '">';
		}

		return implode("\n", $css) . "\n";
	}

	/**
	 * @return bool|string
	 */
	protected function getJS()
	{
		if (empty($this->_media['js']))
			return FALSE;

		$js = [];

		foreach ($this->_media['js'] as $value)
		{
			$js[] = '<script src="' . $value . '" type="text/javascript"></script>';
		}

		return implode("\n", $js) . "\n";
	}

	/**
	 * @param $media
	 * @param $file
	 *
	 * @return bool
	 */
	public function addMedia($media, $file)
	{
		switch ($media)
		{
			case 'css':
				$this->_media['css'][] = $file;
				break;

			case 'js':
			case 'javascript':
				$this->_media['js'][] = $file;
				break;

			default:
				return FALSE;
				break;
		}

		return TRUE;
	}

	/**
	 * @param bool   $template
	 * @param string $base
	 */
	public function render($template = FALSE, $base = 'base')
	{
		$controller_data = Route::main()->getController();

		$controller = $controller_data['class'];
		$action     = $template ? $template : $controller_data['action'];

		self::initTranslation();
		self::initRoute();
		self::$_action = self::generateContent($controller, $action);

		include ROOT . '/app/Templates/' . $base . '.phtml';
	}
}