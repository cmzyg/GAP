<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 30/10/14
 * Time: 16:55
 */

namespace component\externalgetters\v1;


use component\externalgetters\ExternalGettersInterface;
use component\request\GetLicenseeDetailsRequest;
use component\request\GetMethodsListRequest;
use component\request\Request;
use model\GMAPIDataRepository;

class ExternalGetters extends \component\externalgetters\ExternalGetters implements ExternalGettersInterface {

    public function getMethodsList()
    {
        //$data = new GMAPIDataRepository('backoffice_licensees');
        $this->processResponse = "&lt;server config='alogin,alogout,betwin,blogin,end,pbet,progwincurr,sbet'/&gt;";
    }

    public function getLicenseeDetails()
    {
        if( $this->request->getLicenseeId() === null )
        {
            $data = new GMAPIDataRepository('irsbo');
            $result = $data->fetchBackOfficeOperatorsPerLicensee($this->request->getOperatorName());

            if( (is_array($result) && sizeof($result) == 0) || $result === false || $result == null)
            {
                $this->processResponse = array("error" => "Wrong operatorName and no lid provided in request. operatorName: ".$this->request->getOperatorName());
            }
            $this->request->setLicenseeId($result['licensee_id']);

        }

        $data = new GMAPIDataRepository('BackofficeGmapi');
        $result = $data->fetchBackOfficeLicenseesAndBackOfficeConfigurations($this->request->getLicenseeId());
        if( (is_array($result) && sizeof($result) == 0) || $result === false || $result == null)
        {
            $this->processResponse = array("error" => "Licensee lid=".$this->request->getLicenseeId()." not found in Back Office table.");
        }

        $this->processResponse = array();
        $this->processResponse['lid'] = $this->request->getLicenseeId();
        $this->processResponse['lp'] = $this->request->getPlayerId();
        $this->processResponse['oid'] = ((is_null($this->request->getOperatorName())) ? '0' : $this->request->getOperatorName());
        $this->processResponse['cid'] = (int)$result['cid'];
        $this->processResponse['rid'] = $result['rid'];
        $this->processResponse['transaction_id_length'] = (int)$result['transaction_id_length'];
        $this->processResponse['round_id_length'] = (int)$result['round_id_length'];
        $this->processResponse['encode'] = (int)$result['encode'];
        $this->processResponse['secret_key'] = $result['secret_key'];
        $this->processResponse['db_prefix'] = $result['db_prefix'];
        $this->processStatus = true;
    }

    public function execute(Request $request)
    {
        $this->request = $request;

        if($request instanceof GetMethodsListRequest)
        {
            $this->getMethodsList();
        }
        elseif($request instanceof GetLicenseeDetailsRequest)
        {
            $this->getLicenseeDetails();
        }
        else
        {
            $this->processResponse = $this->NotSupported();
        }
    }

}