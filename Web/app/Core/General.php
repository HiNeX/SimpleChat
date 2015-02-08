<?php
/**
 * Main core class.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 0:24
 */

namespace Core;


use Core\System\Configuration;
use Core\System\Route;

final class General {
	private function initController () {
		if ($controller = Route::main()->getController()) {

			$controllerName = 'Controllers\\'.ucfirst($controller['class']).'Controller';
			$actionName = $controller['action'].'Action';

			// Check controller exists.
			if (class_exists($controllerName)) {
				$controllerClass = new $controllerName();

				// Check action exists.
				if (method_exists($controllerClass, $actionName)) {
					$controllerClass->runBefore(); // Safe constructor.
					$controllerClass->$actionName(); // Run action.
					$controllerClass->runAfter(); // Safe destructor.
					return true;
				}
			}
		}

		echo '404';

		return false;
	}

	private function completion() {
		Configuration::main()->finality();
	}

	public function init () {
		$this->initController(); // Controller and action initialization.
		$this->completion(); // Run actions after completion.
	}
}