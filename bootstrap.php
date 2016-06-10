<?php

/**
 *
 * Simple MVC Framework aka Baseframework
 * @version 2.0
 *
 * Created By Zygimantas Simkus
 * Copyright 2014 Plati Tech Limited, All Rights Reserved
 */
/**
 * Site Path Settings
 * For unix servers check the forward slash character  http://fsl.fmrib.ox.ac.uk/fslcourse/unix_intro/files.html
 * For Windows servers check the backslash character http://msdn.microsoft.com/en-gb/library/windows/desktop/aa365247%28v=vs.85%29.aspx#paths
 */
$sitepath = realpath(dirname(__FILE__));
$sitepath = $sitepath . DIRECTORY_SEPARATOR;
define('_SITE_PATH', $sitepath);

/**
 * Site Bootstrapping
 * Here we register all our autoloaders
 * So they all can fall through in case one fails
 */
$bootstrap = _SITE_PATH . "autoloader" . DIRECTORY_SEPARATOR . 'autoloader.php';

require_once $bootstrap;

spl_autoload_register("\autoloader\Autoloader::load");

//Composer autoloader
$composerAutoloader = _SITE_PATH . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

require_once $composerAutoloader;

/**
 *
 * Environment Settings
 *
 */
define('APPLICATION_ENV', "development"); // Could be development or production

//header('Content-Type: application/json');

date_default_timezone_set('GMT');

if (APPLICATION_ENV == 'development')
{
    ini_set('error_reporting', E_ALL ^ E_STRICT);
    ini_set('display_errors', true);
}


set_error_handler('\component\logger\v1\Logger::errorHandler', E_ALL);
register_shutdown_function('\component\logger\v1\Logger::handleFatal');
