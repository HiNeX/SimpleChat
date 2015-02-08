<?php
/**
 * PDO: base singleton modification.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 22:20
 */

namespace Core\System;

use Core\Base\Singleton;

class Database extends Singleton
{
	/**
	 * @return mixed
	 */
	public static function main()
	{
		$class = get_called_class();

		if (!isset(self::$_instances[$class]))
		{
			$type     = Configuration::main()->get('database', 'type');
			$host     = Configuration::main()->get('database', 'host');
			$base     = Configuration::main()->get('database', 'base');
			$user     = Configuration::main()->get('database', 'user');
			$password = Configuration::main()->get('database', 'password');
			try
			{
				$database                 = new \PDO($type . ':host=' . $host . ';dbname=' . $base, $user, $password, [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
				self::$_instances[$class] = $database;
			} catch (\PDOException $e)
			{
				die($e);
			}
		}

		return self::$_instances[$class];
	}
}