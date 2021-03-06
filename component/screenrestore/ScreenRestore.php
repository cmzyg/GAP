<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 30/10/14
 * Time: 15:23
 */

namespace component\screenrestore;


use application\BaseComponent;

class ScreenRestore extends BaseComponent {

    protected $processResponse;
    protected $processStatus;
    /**
     * @var \component\request\Request
     */
    protected $request;
    protected $currentVersion = 'v1.0.0';
    protected $em;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Loads a component based on current version
     * @return $this| \component\screenrestore\v1\ScreenRestore
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * @param $version
     * @return $this| \component\screenrestore\v1\ScreenRestore
     */
    protected function selectVersion($version)
    {
        switch($version)
        {
            case 'v1.0.0':
                return new \component\screenrestore\v1\ScreenRestore();
            default:
                return $this;
        }
    }

    public function NotSupported()
    {
        return "Action is not Supported";
    }

    /**
     * @return bool
     */
    public function isProcessSuccessful()
    {
        return $this->processStatus;
    }

    /**
     * @return mixed
     */
    public function getProcessResponse()
    {
        return $this->processResponse;
    }



} 