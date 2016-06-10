<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 05/01/15
 * Time: 10:20
 */

namespace component\soap;


use component\logger\Logger;
use exceptions\SoapException;
use model\gmapi\GmapiLicensee;

class SoapRequest {
    /**
     * @var null|\model\gmapi\GmapiLicensee
     */
    protected $gmapiLicensee;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    /**
     * @var string
     */
    protected $fault;

    protected $faultCode;



    protected function getLicenseeDetails($lId)
    {
        $licenseeId = $configurationId = $regulationId = $dbPrefix = $secretKey = null;
      
        try
        {
            $this->gmapiLicensee = $this->entityManager->getRepository("model\\gmapi\\GmapiLicensee")->findOneByIdLicensee(intval(trim($lId)));
            if (is_object($this->gmapiLicensee) && $this->gmapiLicensee instanceof GmapiLicensee) {
                $licenseeId= $this->gmapiLicensee->getIdLicensee();
                $configurationId = $this->gmapiLicensee->getConfigurationId();
                $regulationId = $this->gmapiLicensee->getRegulationNumber();
                $dbPrefix = $this->gmapiLicensee->getDbPrefix();
                $secretKey = $this->gmapiLicensee->getSceretKey();
            }
            else
            {
                throw new SoapException("The licensee id provided ".$lId." could not be used to fetch the licensee details ", 1);
            }

        }
        catch(\Exception $ex)
        {
                $this->fault = "There was an error while trying to get the licensee details with message: ".$ex->getMessage();
        }

        return array($licenseeId, $configurationId, $regulationId, $dbPrefix, $secretKey);
    }

    public function setRequest($pId, $pName, $lId, $cId, $rId, $gId, $sId, $hInfo, $methodName, $lp, $pp, $operator = null, array $otherParameters = array())
    {
        $_REQUEST['pid']   = $pId;
        $_REQUEST['pname']       = $pName;
        $_REQUEST['lid']         = $lId;
        $_REQUEST['cid']         = $cId;
        $_REQUEST['rid']         = $rId;
        $_REQUEST['gid']         = $gId;
        $_REQUEST['sid']         = $sId;
        $_REQUEST['hinfo']       = $hInfo;
        $_REQUEST['method_name'] = $methodName;
        $_REQUEST['lp']          = $lp;
        $_REQUEST['pp']          = $pp;
        $_REQUEST['operator']    = $operator;



        foreach($otherParameters as $key => $value)
        {
            $_REQUEST[$key] = $value;
        }

    }


    /**
     * @param $licenseeId
     * @param $secretKey
     * @param $gameId
     * @param $skinId
     * @param $licenseeConfId
     * @return string
     */
    public function getHashInfo($licenseeId, $secretKey, $gameId, $skinId, $licenseeConfId)
    {
        $datatime   = new \DateTime();

        $hash = "";
        $hash .= $licenseeId;
        $hash .= "+";
        $hash .= $secretKey;
        $hash .= "+";
        $hash .= $gameId;
        $hash .= "+";
        $hash .= $skinId;
        $hash .= "+";
        $hash .= $licenseeConfId;
        $hash .= "+";

        $day_of_year = $datatime->format("z");

        $day_of_year = intval($day_of_year) + 1;

        return hash("sha512", ($hash . $day_of_year));
    }




} 