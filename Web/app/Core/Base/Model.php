<?php
/**
 * Base model realisation.
 * Author: Roman Hinex
 * Date: 11.12.14
 * Time: 1:38
 */

namespace Core\Base;


use Core\System\Database;

abstract class Model extends Singleton {
	protected static $db;
	protected static $id = 'id';
	protected static $table;
	protected static $class;
	protected static $exec;
	protected static $single = null;
	protected static $order = ['by' => 'id', 'sort' => 'ASC'];

	public static function main() { return false; }

	public function __set($name, $value)
	{ return self::$single[$name] = $value; }

	public function __get($name)
	{ return isset(self::$single[$name])?self::$single[$name]:false; }

	protected static function load($flag = false) {
		self::$class = get_called_class();

		if (!isset(self::$_instances[self::$class]) OR $flag) {
			self::$_instances[self::$class] = new self::$class();
			$stdClass = new \ReflectionClass(self::$_instances[self::$class]);
			self::$table = $stdClass->getShortName();
			self::$db = Database::main();
		}

		static::init();

		return self::$_instances[self::$class];
	}

	private static function getWhere($data) {
		if (!empty($data) AND is_array($data)) {
			$where_items = [];
			foreach ($data as $compare) {
				if (count($compare) !== 3) { return false; }
				$f_array = [];

				foreach ($compare as $i => $f){
					$f_array[0] = '`'.$compare[0].'`';
					$f_array[1] = $compare[1];
					$f_array[2] = '\''.$compare[2].'\'';
				}

				$where_items[] = implode(' ', $f_array);
			}

			return ' WHERE '.implode(' AND ', $where_items);
		} else if (is_numeric($data)) {
			return ' WHERE '.self::$id.' = '.$data;
		}

		return false;
	}

	private static function getOne($data) {
		$where = self::getWhere($data);

		return static::$db->prepare('SELECT * FROM '.self::$table.$where.' ORDER BY '.self::$order['by'].' '.self::$order['sort'].' LIMIT 1');
	}

	private static function getMany($data, $from, $to) {
		$where = self::getWhere($data);
		$limit = $to > 0?' LIMIT '.$from.', '.$to:'';
		return static::$db->prepare('SELECT * FROM '.self::$table.$where.' ORDER BY '.self::$order['by'].' '.self::$order['sort'].' '.$limit);
	}


	protected static function init() {} // Initialize function

	protected static function useId($id) { return self::$id = $id; }
	protected static function useTable($table) { return self::$table = $table; }
	protected static function useOrder($by, $sort) { return self::$order = ['by' => $by, 'sort' => $sort]; }

	public final static function find($data = []) {
		self::load(true);
		self::$single = NULL;
		self::$exec = self::getOne($data);

		if (self::$exec->execute()) {
			self::$single = self::$exec->fetch(\PDO::FETCH_ASSOC);
		} else {
			return false;
		}

		return static::$_instances[self::$class];
	}

	public final static function findAll($data = [], $from = 0, $to = false) {
		self::load(true);
		self::$single = NULL;
		self::$exec = self::getMany($data, $from, $to);
		self::$exec->execute();

		return static::$_instances[self::$class];
	}

	public final static function create() {
		self::load(true);
		self::$single = NULL;
		return static::$_instances[self::$class];
	}

	public final static function like($column, $text) {
		self::load(true);
		self::$single = NULL;

		self::$exec = self::$db->prepare('SELECT * FROM '.self::$table.' WHERE '.$column.' LIKE \'%'.$text.'%\'');
		self::$exec->execute();
		return static::$_instances[self::$class];
	}

	public function fetch() {
		return self::$single;
	}

	public function fetchAll() {
		return self::$exec->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function count() {
		return self::$exec->rowCount();
	}

	public function save() {

		if (!empty(self::$single)) {
			if (isset(self::$single['id'])) {
				$id = self::$single['id'];
				unset(self::$single['id']);

				$arraySetters = [];

				foreach (self::$single as $key => $value) {
					$arraySetters[] = '`'.$key.'` = \''.$value.'\'';
				}

				self::$single['id'] = $id;

				self::$exec = static::$db->prepare('UPDATE '.self::$table.' SET '.implode(', ', $arraySetters).' WHERE `id` = '.$id.' LIMIT 1');

				return self::$exec->execute();
			} else {
				$arrayKeys = [];
				$arrayValues = [];


				foreach (self::$single as $key => $value) {
					$arrayKeys[] = '`'.$key.'`';
					$arrayValues[] = '\''.$value.'\'';
				}

				self::$exec = static::$db->prepare('INSERT INTO '.self::$table.' ('.implode(', ', $arrayKeys).') VALUES ('.implode(', ', $arrayValues).')');

				if ($status = self::$exec->execute()) {
					self::$single['id'] = static::$db->lastInsertId();
				}

				return $status;
			}
		}

		return false;
	}

}