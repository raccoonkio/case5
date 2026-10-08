<?php

$publicPath = __DIR__.'/public';
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$file = realpath($publicPath.$uri);

$isPublicFile = $file && (str_starts_with($file, realpath($publicPath).DIRECTORY_SEPARATOR) || str_starts_with($file, realpath(__DIR__.'/storage/app/public').DIRECTORY_SEPARATOR));

if ($uri !== '/' && $isPublicFile && is_file($file)) {
    return false;
}

require_once $publicPath.'/index.php';
