<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 30/10/14
 * Time: 16:57
 */

namespace component\wallet\seamless;


use component\wallet\Wallet;

class Seamless extends Wallet{

    protected $currentVersion = "1.0";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Loads a component based on current version
     * @return mixed|$this|\component\wallet\seamless\v1\Seamless
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * Selects current version of component
     * @param $version
     * @return $this|\component\wallet\seamless\v1\Seamless
     */
    private function selectVersion($version)
    {
        switch($version)
        {
            case "1.0":
                return new \component\wallet\seamless\v1\Seamless();
            default:
                return $this;
        }

    }
} 