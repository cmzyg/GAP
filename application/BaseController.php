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

/**
 * Class BaseController
 * @package application
 */
Abstract class BaseController {

    protected $registry;

    /**
     * @param $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * The default action for all controllers
     * @return mixed
     */
    abstract public function index();




}