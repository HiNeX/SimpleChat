<?php
/**
 * Simple router.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 0:24
 */

namespace Core\System;

use Core\Base\Singleton;
use Core\System\Configuration;

class Route extends Singleton
{
	protected static $_controller;

	/**
	 * @return array
	 */
	public function getController()
	{
		if (is_null(self::$_controller))
		{
			$request = Request::main();
			// Set default main controller.
			$routeArray = ['class' => 'index', 'action' => 'index', 'route' => NULL];
			if ($route = $request->getQuery('path'))
			{
				$routeArray['route'] = $route; // Set path.
				if (!empty($route))
				{
					$explode = explode('/', $route);

					if (!empty($explode[1]))
						$routeArray['action'] = strtolower($explode[1]); // Setting action.
					if (!empty($explode[0]))
						$routeArray['class'] = strtolower($explode[0]); // Setting controller.
				}
			}

			return self::$_controller = $routeArray;
		}

		return self::$_controller;
	}

	public function url($input) {
		$rewrite = Configuration::main()->get('general', 'rewrite');

		$routeArray = ['class' => 'index', 'action' => 'index', 'param' => ''];
		if (is_array($input)) {
			if (!empty($input[2]))
				$routeArray['param'] = http_build_query($input[2]); // Params.
			if (!empty($input[1]))
				$routeArray['action'] = strtolower($input[1]); // Setting action.
			if (!empty($input[0]))
				$routeArray['class'] = strtolower($input[0]); // Setting controller.

			$url = $routeArray['class'].'/'.$routeArray['action'];

			if ($rewrite) {
				$param = !empty($routeArray['param'])?'?'.$routeArray['param']:NULL;
				$url = '/'.$url.$param;
			} else {
				$param = !empty($routeArray['param'])?'&'.$routeArray['param']:NULL;
				$url = '/index.php?path='.$url.$param;
			}
		} else {
			$url = $input;
		}

		return $url;
	}
}