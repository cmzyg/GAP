<?php
/**
 * Created by PhpStorm.
 * User: Pawel
 * Date: 19/09/14
 * Time: 11:36
 */

namespace component\validation;


use application\BaseComponent;
use component\EntityManager;
use component\validation\v1\Validation as ValidationV1;

class Validation extends BaseComponent {

    protected $currentVersion = 'v1.0.0';
    protected $em;

    public function __construct()
    {
        parent::__construct();
        $this->em = EntityManager::createEntityManager();
    }

    /**
     * Loads a component based on current version
     * @return $this|\component\validation\v1\Validation
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * @param $version
     * @return $this|\component\validation\v1\Validation
     */
    protected function selectVersion($version)
    {
        switch($version)
        {
            case 'v1.0.0':
                return new ValidationV1();
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
} 