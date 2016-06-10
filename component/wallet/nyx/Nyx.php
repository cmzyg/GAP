<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 13/10/14
 * Time: 15:08
 */

namespace component\wallet\nyx;


use component\wallet\Wallet;

class Nyx extends Wallet{

    protected $currentVersion = 1;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Loads a component based on current version
     * @return mixed|\component\wallet\nyx\v1\Nyx
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * Selects current version of component
     * @param $version
     * @return $this|\component\wallet\nyx\v1\Nyx
     */
    private function selectVersion($version)
    {
        switch($version)
        {
            case 1:
                return new \component\wallet\nyx\v1\Nyx();
            case 2:
                return new \component\wallet\nyx\v2\Nyx();
            default:
                return $this;
        }

    }


} 