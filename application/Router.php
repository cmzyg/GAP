<?php
/**
 *
 * Simple MVC Framework aka Baseframework
 * @version 2.0
 *
 * Created By Zygimantas Simkus
 * Copyright 2014 Plati Tech Limited, All Rights Reserved
 */

namespace application;


use exceptions\InvalidPathException;

/**
 * Class Router
 * @package application
 */
class Router {

    private $registry;
    private $path;
    private $controller;
    private $action;
    public $clClass;

    /**
     * @param $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Sets the path to the requested controller
     * @param $path
     * @throws \exceptions\InvalidPathException
     */
    public function setControllerPath($path)
    {

        if(!(is_dir($path)))
        {
            throw new InvalidPathException("Invalid Path Exception\nInvalid Controller Path provided: ");
        }
        $this->path = $path;

    }

    /**
     * Load the requested Controller
     * @param $route
     * @param $path
     * @throws \Exception
     * @return bool
     */
    public function loadController($route, $path)
    {
         
        try
        {
            $this->setControllerPath($path);
        }catch (\Exception $ex)
        {
            echo $ex->getMessage();
            return false;
        }

        $controllerFile = $this->getControllerReady($route);
        //print $controllerFile;
        if(!is_readable($controllerFile)){
         $this->controller = "pagenotfound";
        }

        $controllerClass = '\\controller\\'.$this->controller."Controller";
        $controllerClass = new $controllerClass($this->registry);
        $this->clClass = $controllerClass;
        
          
        if(!is_callable(array($controllerClass, $this->action)))
        {
            $controllerAction = "index";
        }
        else{
            
            $controllerAction = $this->action;
        }
        $controllerClass->$controllerAction();
    }

    /**
     * Prepares the action of the requested controller
     * @param $route
     * @return string
     */
    public function getControllerReady($route)
    {
        if(!empty($route))
        {
            $routeArray = explode("/", $route);
            $this->controller = $routeArray[0];
            if(isset($routeArray[1]))
            {
                $this->action = $routeArray[1];
            }
        }

        if(empty($this->controller))
        {
            $this->controller = "index";
        }

        if(empty($this->action))
        {
            $this->action = "index";
        }

        return $this->path.$this->controller."Controller.php";
    }


}