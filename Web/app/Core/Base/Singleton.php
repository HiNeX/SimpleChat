<?php
/**
 * Singleton realisation.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 0:26
 */

namespace Core\Base;


abstract class Singleton {
	protected static $_instances = [];

	protected function __construct() {}
	protected function __clone() {}

	public static function main() {
		$class = get_called_class();

		if (!isset(self::$_instances[$class])) {
			self::$_instances[$class] = new $class();
		}

		return self::$_instances[$class];
	}
}