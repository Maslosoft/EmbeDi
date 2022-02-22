<?php

use Maslosoft\EmbeDi\EmbeDi;

error_reporting(E_ALL);
date_default_timezone_set('Europe/Paris');

const VENDOR_DIR = __DIR__ . '/../vendor';
require VENDOR_DIR . '/autoload.php';

// Invoker stub for windows
if (defined('PHP_WINDOWS_VERSION_MAJOR'))
{
	require __DIR__ . '/../misc/Invoker.php';
}

$version = (new EmbeDi)->getVersion();
echo "EmbeDi " . $version . PHP_EOL;