<?php

/**
*
*/
class Utility
{
	/**
	 * Is request from Ajax?
	 * @return boolean If request be from Ajax then TRUE.
	 */
	static function isAjax()
	{
		$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
		return $request === 'xmlhttprequest';
	}
}
