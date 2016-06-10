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
 * Bootstrap
 */
require_once "bootstrap.php";



/**
 *
 * Registry Settings
 * Here we call necessary configurations
 * such as routing, database e.t.c
 * And we register them so they can be accessible
 * Application Wide.
 */


$registry = new \application\Registry();
$registry->router = new \application\Router($registry);
$registry->template = new \application\Template($registry);
$registry->template->setViewPath(_SITE_PATH."view".DIRECTORY_SEPARATOR);
$route = "";
$route = @strtolower($_REQUEST['rt']);

/**
 * Store every input
 */


//echo $_REQUEST['rt'];
@$request = ((strpos($_REQUEST['rt'], ".php") !== false) ? str_replace(".php", "", $_REQUEST['rt']) : $_REQUEST['rt']);
$controllerName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $request);
$controllerName = preg_replace('/[^a-z0-9 ]/i', '', $controllerName);
$controllerName = @strtolower($controllerName);
$request = strtolower($request);

$registry->router->loadController(((strpos($route, "/") === false) ? $controllerName : $request), _SITE_PATH."controller".DIRECTORY_SEPARATOR);

//include "SoapService.wsdl.xml";

//return;

// Stop App

//$registry->router->loadController($route, _SITE_PATH."controller".DIRECTORY_SEPARATOR);


//End
