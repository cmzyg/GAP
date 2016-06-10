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

use component\logger\Logger;

/**
 * Class BaseComponent
 * @package application
 */
Abstract class BaseComponent {

    protected $registry;
    protected $currentVersion = "1.0";

    /**
     * @var \component\logger\v1\Logger|\component\logger\Logger|null
     */
    protected static $logger = null;

    /**
     * @param $registry
     */
    public function __construct($registry = null)
    {
        $this->registry = $registry;
        $this->getLogger();
        self::$logger->login($this);
    }

    /**
     * Loads a component based on current version
     * @return mixed
     */
    abstract public function loadComponent();

    /**
     * Gets the current version of the component
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    /**
     * Returns an array of available versions for a component
     * @return array
     */
    public function getAvailableVersions()
    {
        return array($this->getCurrentVersion());
    }

    /**
     * Sets the version to use if called before loadComponent to the provided version if on the list of available versions
     * @param $version
     */
    public function setCurrentVersion($version)
    {
        if (in_array($version, $this->getAvailableVersions()))
        {
            $this->currentVersion = $version;
        }
    }

    /**
     * @return \component\logger\v1\Logger|\component\logger\Logger|null
     */
    public static function getLogger()
    {
        if (self::$logger == null)
        {
            $logger = new Logger();
            self::$logger = $logger->loadComponent();
        }
        return self::$logger;
    }

    public function __destruct()
    {
        if (isset(self::$logger) && self::$logger !== null)
        {
            self::$logger->logout($this);
        }
    }

}
