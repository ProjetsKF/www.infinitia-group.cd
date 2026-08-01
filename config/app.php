<?php
if (!function_exists('infinitia_is_local_host')) {
    function infinitia_is_local_host($host)
    {
        $host = strtolower(trim($host));
        $parsedHost = parse_url('http://' . $host, PHP_URL_HOST);

        if ($parsedHost === false || $parsedHost === null || $parsedHost === '') {
            $parsedHost = preg_replace('/:\d+$/', '', $host);
        }

        return $parsedHost === 'localhost' || $parsedHost === '127.0.0.1';
    }
}

if (!function_exists('infinitia_base_url')) {
    function infinitia_base_url()
    {
        static $baseUrl = null;

        if ($baseUrl !== null) {
            return $baseUrl;
        }

        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

        if (infinitia_is_local_host($host)) {
            $path = '';
            $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
            $scriptName = str_replace('\\', '/', $scriptName);
            $segments = explode('/', trim($scriptName, '/'));

            if (count($segments) > 1) {
                $path = '/' . $segments[0];
            }

            $baseUrl = 'http://' . $host . $path;
        } else {
            $baseUrl = 'https://www.infinitia-group.com';
        }

        return rtrim($baseUrl, '/');
    }
}

if (!function_exists('infinitia_url')) {
    function infinitia_url($path)
    {
        $path = trim($path);

        if ($path === '') {
            return infinitia_base_url() . '/';
        }

        return infinitia_base_url() . '/' . ltrim($path, '/');
    }
}

if (!function_exists('infinitia_url_html')) {
    function infinitia_url_html($path)
    {
        return htmlspecialchars(infinitia_url($path), ENT_QUOTES, 'UTF-8');
    }
}
