<?php
/**
 * Simple request realisation.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 0:58
 */

namespace Core\System;

use Core\Base\Singleton;
use Core\System\Route;

class Request extends Singleton
{
	/**
	 * @param $query
	 *
	 * @return bool|mixed
	 */
	public function getQuery($query)
	{
		return isset($_GET[$query]) ? $_GET[$query] : FALSE;
	}

	/**
	 * @param $query
	 *
	 * @return bool
	 */
	public function hasQuery($query)
	{
		return isset($_GET[$query]);
	}

	/**
	 * @param $post
	 *
	 * @return bool|mixed
	 */
	public function getPost($post)
	{
		return isset($_POST[$post]) ? $_POST[$post] : FALSE;
	}

	/**
	 * @param $post
	 *
	 * @return bool
	 */
	public function hasPost($post)
	{
		return isset($_POST[$post]);
	}

	/**
	 * @param $request
	 *
	 * @return bool|mixed
	 */
	public function getRequest($request)
	{
		return isset($_REQUEST[$request]) ? $_REQUEST[$request] : FALSE;
	}

	/**
	 * @param $request
	 *
	 * @return bool
	 */
	public function hasRequest($request)
	{
		return isset($_REQUEST[$request]);
	}

	/**
	 * @param $cookie
	 *
	 * @return bool|mixed
	 */
	public function getCookie($cookie)
	{
		return isset($_COOKIE[$cookie]) ? $_COOKIE[$cookie] : FALSE;
	}

	/**
	 * @param $cookie
	 * @param $value
	 *
	 * @return bool
	 */
	public function setCookie($cookie, $value)
	{
		return setcookie($cookie, $value);
	}

	/**
	 * @param $cookie
	 *
	 * @return bool
	 */
	public function hasCookie($cookie)
	{
		return isset($_COOKIE[$cookie]);
	}

	/**
	 * @return mixed
	 */
	public function getMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * @param      $header
	 * @param bool $replace
	 * @param bool $code
	 */
	public function sendHeader($header, $replace = FALSE, $code = FALSE)
	{
		header($header, $replace, $code);
	}

	/**
	 * @param      $path
	 * @param bool $replace
	 * @param bool $code
	 */
	public function redirect($path, $replace = FALSE, $code = FALSE)
	{
		header('Location: '.Route::main()->url($path), $replace, $code);
		exit;
	}

	/**
	 * @param array $array
	 */
	public function sendJson($array = ['success' => FALSE])
	{
		header('Content-Type: application/json');
		echo json_encode($array);
	}
}