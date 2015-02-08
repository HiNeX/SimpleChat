<?php
/**
 * Base controller.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 6:41
 */

namespace Core\Base;

use Core\System\Request;
use Core\System\Translation;
use Core\System\View;
use Models\Extended\User;

abstract class Controller
{
	protected $request;
	protected $user;
	protected $display;
	protected $translation;


	final function __construct()
	{
		$this->request      = Request::main();
		$this->user         = User::main();
		$this->display      = View::main();
		$this->translation  = Translation::main();
	}

	public function runBefore()
	{
	}

	public function runAfter()
	{
	}
}