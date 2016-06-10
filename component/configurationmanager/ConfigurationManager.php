<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/10/14
 * Time: 10:14
 */

namespace component\configurationmanager;


use application\BaseComponent;

class ConfigurationManager extends BaseComponent{

    protected $entityManager;
    
    function __construct()
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
     * @return $this|\component\configurationmanager\v1\ConfigurationManager
     */
    private function selectVersion($version)
    {
        switch($version)
        {
            case "1.0":
                return new \component\configurationmanager\v1\ConfigurationManager();
            default:
                return $this;
        }

    }

} 