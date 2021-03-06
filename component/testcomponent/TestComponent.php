<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 27/10/14
 * Time: 14:40
 */

namespace component\testcomponent;


use application\BaseComponent;

class TestComponent extends BaseComponent{

    protected $currentVersion = 'v1.0.0';
    protected $em;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Loads a component based on current version
     * @return $this| \component\testcomponent\v1\TestComponent
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * @param $version
     * @return $this| \component\testcomponent\v1\TestComponent
     */
    protected function selectVersion($version)
    {
        switch($version)
        {
            case 'v1.0.0':
                return new \component\testcomponent\v1\TestComponent();
            default:
                return $this;
        }
    }

} 