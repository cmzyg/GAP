<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 30/10/14
 * Time: 16:57
 */

namespace component\wallet\openbet;


use component\wallet\Wallet;

class Openbet extends Wallet{

    protected $currentVersion = "1.0";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Loads a component based on current version
     * @return mixed|$this
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * Selects current version of component
     * @param $version
     * @return $this
     */
    private function selectVersion($version)
    {
        switch($version)
        {
            case "1.0":
            default:
                return $this;
        }

    }
} 