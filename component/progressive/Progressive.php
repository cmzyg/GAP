<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 03/11/14
 * Time: 16:52
 */

namespace component\progressive;


use application\BaseComponent;
use component\EntityManager;

class Progressive extends BaseComponent{

    protected $processResponse;
    protected $processStatus;
    protected $errorMessage;
    /**
     * @var \component\request\Request
     */
    protected $request;
    protected $currentVersion = 'v1.0.0';
    protected $em;

    public function __construct()
    {
        parent::__construct();
        $this->em = EntityManager::createEntityManager();
    }

    /**
     * Loads a component based on current version
     * @return Progressive|\component\progressive\v1\Progressive
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * @param $version
     * @return $this|\component\progressive\v1\Progressive
     */
    protected function selectVersion($version)
    {
        switch($version)
        {
            case 'v1.0.0':
                return new \component\progressive\v1\Progressive();
            default:
                return $this;
        }
    }


    protected function save($entity)
    {
        if(is_object($entity))
        {
            try
            {
                $this->em->persist($entity);
                $this->em->flush();
            }catch (\Exception $ex)
            {
                return false;
            }

            return true;
        }

        return false;
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