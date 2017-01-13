<?php

/**
*
*/
class Utility
{
	static function isAjax()
	{
		$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
		return $request === 'xmlhttprequest';
	}
}
