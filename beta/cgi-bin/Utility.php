<?php

declare(strict_types=1);


class Utility
{
    /**
     * Is request from Ajax?
     * @return bool If request be from Ajax then TRUE.
     */
    public static function isAjax():bool
    {
        $request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';

        return $request === 'xmlhttprequest';
    }
}
