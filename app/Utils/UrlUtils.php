<?php

namespace App\Utils;

class UrlUtils
{
    public static function getFrontendUrl()
    {
        static $url;
        if (!$url) {
            $url = app()->environment('local') ? env('FRONTEND_URL_LOCAL') : env('FRONTEND_URL_PROD');
        }
        return $url;
    }
}
