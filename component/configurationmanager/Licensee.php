<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/10/14
 * Time: 10:18
 */

namespace component\configurationmanager;


class Licensee {

    private $licensee;
    private $operatorName;
    private $operatorId;
    private $specialFlag = null;

    /**
     * @param mixed $licensee
     */
    public function setLicensee($licensee)
    {
        $this->licensee = $licensee;
    }

    /**
     * @return mixed
     */
    public function getLicensee()
    {
        return $this->licensee;
    }

    /**
     * @param mixed $operatorId
     */
    public function setOperatorId($operatorId)
    {
        if($operatorId < 1)
        {
            $this->operatorId = null;
        }
        else
        {
            $this->operatorId = intval($operatorId);
        }

    }

    /**
     * @return mixed
     */
    public function getOperatorId()
    {
        return $this->operatorId;
    }

    /**
     * @param mixed $operatorName
     */
    public function setOperatorName($operatorName)
    {
        $this->operatorName = $operatorName;
    }

    /**
     * @return mixed
     */
    public function getOperatorName()
    {
        return $this->operatorName;
    }

    /**
     * @param null $specialFlag
     */
    public function setSpecialFlag($specialFlag)
    {
        $this->specialFlag = $specialFlag;
    }

    /**
     * @return null
     */
    public function getSpecialFlag()
    {
        return $this->specialFlag;
    }




} 