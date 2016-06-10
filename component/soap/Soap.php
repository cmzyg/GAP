<?php

/**
 * Description of Soap
 *
 * @author rafal
 */
namespace component\soap;

use application\BaseComponent;
class Soap extends BaseComponent  {
    

    public function __construct()
    {
        parent::__construct();
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
     * @return $this|\component\soap\v1\Soap
     */
    private function selectVersion($version)
    {
        switch ($version)
        {
            case "1.0":
                return new \component\soap\v1\Soap();
            default:
                return $this;
        }
    }
}
