<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 30/10/14
 * Time: 16:57
 */

namespace component\wallet\purse;

use component\wallet\Wallet;

class Purse extends Wallet {

    protected $currentVersion = "1.0";

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
        switch ($version)
        {
            case "1.0":
                return new \component\wallet\purse\v1\Purse();
            default:
                return $this;
        }
    }

}
