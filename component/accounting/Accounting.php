<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 06/11/14
 * Time: 09:46
 */

namespace component\accounting;
use application\BaseComponent;

/**
 * Class Accounting
 * @package component\accounting
 */
class Accounting extends BaseComponent {

    protected $processResponse;
    protected $processStatus;
    protected $errorMessage;

    /**
     * Loads a component based on current version
     * @return null|$this|\component\accounting\v1\Accounting
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * Selects current version of component
     * @param $version
     * @return null|$this|\component\accounting\v1\Accounting
     */
    private function selectVersion($version)
    {
        switch ($version)
        {
            case "1.0":
                return new \component\accounting\v1\Accounting();
            default:
                return $this;
        }
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
