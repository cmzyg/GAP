<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 23/09/14
 * Time: 14:06
 */

namespace component\communication;


use application\BaseComponent;
use component\request\Request as incomingRequest;

/**
 * Class Communication
 * @package component\communication
 * @author Samuel I. Amaziro
 */
class Communication extends BaseComponent{
    /**
     * The request coming from a client
     * @var \component\request\Request
     */
    protected $inComingRequest;
    /**
     * Current version of the system
     * @var string
     */
    protected $systemVersion = "4.0";


    public function __construct()
    {

        parent::__construct();
        $re = new incomingRequest();
        $this->inComingRequest = $re->loadComponent();
    }

    /**
     * Loads a component based on current version
     * @return mixed|$this|\component\communication\v1\Communication()
     */
    public function loadComponent()
    {
      return $this->selectVersion($this->currentVersion);
    }

    /**
     * Selects current version of component
     * @param $version
     * @return $this|\component\communication\v1\Communication()
     */
    private function selectVersion($version)
    {
        switch($version)
        {
            case "1.0":
                return new \component\communication\v1\Communication();
            default:
                return $this;
        }

    }


} 