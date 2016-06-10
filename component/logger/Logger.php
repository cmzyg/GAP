<?php

/**
 * Description of Logger
 *
 * @author rafal
 */

namespace component\logger;

use application\BaseComponent;

class Logger extends BaseComponent {

    public function __construct($registry = null)
    {
        //prevent Logger to create a Logger in a Logger in a Logger... :)
    }

    /**
     * Loads a component based on current version
     * @return $this|\component\configurationmanager\v1\ConfigurationManager
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * Selects current version of component
     * @param $version
     * @return $this|\component\configurationmanager\v1\ConfigurationManager
     */
    private function selectVersion($version)
    {
        switch ($version)
        {
            case "1.0":
                return new \component\logger\v1\Logger();
            default:
                return $this;
        }
    }

    public function __destruct()
    {
        //prevent Logger to destruct a Logger in a Logger in a Logger... :)
    }

}
