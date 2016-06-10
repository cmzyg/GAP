<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/11/14
 * Time: 11:28
 */

namespace component\wallet\tournament;


use component\wallet\Wallet;

class Tournament extends Wallet {

    protected $currentVersion = "1.0";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Loads a component based on current version
     * @return mixed|\component\wallet\fun\v1\Fun
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * Selects current version of component
     * @param $version
     * @return $this|\component\wallet\fun\v1\Fun
     */
    private function selectVersion($version)
    {
        switch($version)
        {
            case "1.0":
                return new \component\wallet\tournament\v1\Tournament();
            default:
                return $this;
        }

    }

} 