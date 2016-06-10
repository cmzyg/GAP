<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 23/09/14
 * Time: 14:14
 */

namespace component\communication\v1;
use component\communication\CommunicationInterface;
use component\communication\CurlWrapper;
use component\communication\ErrorList;
use component\communication\OutGoingRequest;
use component\configurationmanager\ConfigurationManager;
use component\request\GetLicenseeDetailsRequest;
use component\request\GetMethodsListRequest;
use component\request\ProgressiveServerReadByCurrencyRequest;
use component\request\SaveScreenRelatedRequest;
use component\request\WalletRequest;
use component\validation\Validation as validationComponent;
use DOMDocument;


/**
 * Class Communication
 * @package component\communication\v1
 * @version 1.0
 * @author Samuel .I.Amaziro
 */
class Communication extends \component\communication\Communication implements CommunicationInterface{

    /**
     * Request object for any outgoing request
     * @var \component\communication\OutGoingRequest
     */
    private $outGoingRequest;
    /**
     * The validation component to use in validating incoming request
     * @var \component\validation\v1\Validation
     */
    protected $validationComponent;

    /**
     * Response to send back for current request
     * @var mixed
     */
    protected $incomingRequestResponse;

    /**
     * Status of current request
     * @var bool
     */
    protected $incomingRequestStatus = false;

    /**
     * @var \component\configurationmanager\v1\ConfigurationManager
     */
    protected $configurationManager;
    /**
     * The status of incoming errors if they should be set so they can be displayed or not
     * @var bool
     */
    protected $incomingRequestShowError = false;

    protected $validationErrorCode = null;
    protected $validationResponseError = false;


    public $provider;


    public function __construct()
    {
        parent::__construct();
        $conf = new ConfigurationManager();
        $this->configurationManager = $conf->loadComponent();
        $com = new validationComponent();
        $this->validationComponent = $com->loadComponent();

    }




    /**
     * Receive Get request from client
     * @return mixed
     */
    public function receiveGetRequest()
    {
        $this->getLogger()->setInternalRequest($_REQUEST);
        $validation = $this->validationComponent->validateRequest($this->inComingRequest);
        
        
        if($validation)
        {
            $validatedRequest = $this->validationComponent->getValidatedRequest();
            $this->inComingRequest = $validatedRequest;

            if(!($validatedRequest instanceof GetLicenseeDetailsRequest))
            {
                $obj = $this->configurationManager->getConfiguration($validatedRequest->getLicenseeId());
               $validatedRequest->setLicenseeObject($obj);
            }
            
            $cmd = new CommandInterpreter($validatedRequest, $this);
            $this->incomingRequestStatus = $cmd->getStatus();
            $this->incomingRequestResponse = $cmd->getOutput();
            $this->incomingRequestShowError = $cmd->getShowErrors();

            if($this->incomingRequestStatus === false)
            {
                ErrorList::$failedRequest = true;
            }
        }
        else
        {
            ErrorList::$validationError = true;
            $this->incomingRequestResponse = $this->validationComponent->getErrorMessage();
            $this->validationResponseError = true;
            $this->validationErrorCode = $this->validationComponent->getCode();
        }
        $this->getLogger()->setInternalResponse($this->incomingRequestResponse);
    }

    /**
     * @return mixed
     */
    public function getIncomingRequestResponse()
    {
        return $this->incomingRequestResponse;
    }

    /**
     * Receive Post request from client
     * @return mixed
     */
    public function receivePostRequest()
    {
        // TODO: Implement receivePostRequest() method.
    }

    /**
     * Send a Get request to a given host
     * @return \component\communication\RequestResponse
     */
    public function sendGetRequest()
    {
        $curl = new CurlWrapper();
        $curl->useGet();
        $parameters = http_build_query($this->outGoingRequest->getData(), '', '&');
        $url = $this->outGoingRequest->getUrl().'?'.$parameters;
        $this->outGoingRequest->setUrl($url);
        $curl->setRequestType($this->outGoingRequest->getRequestType());
        $curl->executeRequest($this->outGoingRequest->getUrl());
        return $curl->getRequestResponse();
    }

    /**
     * Send a Post request to a given host
     * @return \component\communication\RequestResponse
     */
    public function sendPostRequest()
    {
        $curl = new CurlWrapper();
        if($this->outGoingRequest->getSendPostOnLink())
        {
            $curl->usePost(array());
            $parameters = http_build_query($this->outGoingRequest->getData(), '', '&');
            $url = $this->outGoingRequest->getUrl().'?'.$parameters;
        }
        else
        {
            $curl->usePost($this->outGoingRequest->getData());
            $url = $this->outGoingRequest->getUrl();
        }

        $this->outGoingRequest->setUrl($url);
        $curl->setRequestType($this->outGoingRequest->getRequestType());
        $curl->executeRequest($this->outGoingRequest->getUrl());
        return $curl->getRequestResponse();
    }

    /**
     * Send a Soap request to a given host
     * @return mixed
     */
    public function sendSoapRequest()
    {
        // TODO: Implement sendSoapRequest() method.
    }

    /**
     * Sends a reply to a client
     * @return mixed
     */
    public function replyResponse()
    {
        $jsonFormatter = function($response, $requestStatus, $showStatus, $secretKey, $diffEnc = false)
        {
            $standardCrypt = function($output, $secretKey){
               $enc = ((is_null($output)) ? $output : base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$secretKey,$output,MCRYPT_MODE_CBC,"81a577a68f9e94d6cc02fe23b6ee64a4")));
                return $enc;
            };

            $methodDetailsCrypt = function($output){
                $secretKey = '86ac84f40b940c946856919080f27e20';
                $enc = ((is_null($output)) ? $output : base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$secretKey,$output,MCRYPT_MODE_CBC,"81a577a68f9e94d6cc02fe23b6ee64a4")));
                return $enc;
            };

            $generateXMLAnswer = function($answer){

                $params = array("lid", "gid", "sid", "cid", "rid", "tx", "tv", "lp", "uid", "pid", "ai", "act");

                $xml = null;
                $dom = new DOMDocument("1.0");
                $root = $dom->createElement("gmapi", $answer);
                $dom->appendChild($root);
                $_REQUEST['tv'] = "";
                $_REQUEST['uid'] = "";
                $_REQUEST['tx'] = "";
                $_REQUEST['ai'] = '';
                foreach ($_REQUEST as $key => $val) {
                    if (in_array($key, $params)) {
                        $attrName = $dom->createAttribute($key);
                        $root->appendChild($attrName);
                        $val = (mb_check_encoding($val, 'UTF-8'))?$val:'';
                        $attrValue = $dom->createTextNode($val);
                        $attrName->appendChild($attrValue);
                    }
                }
                $attrName = $dom->createAttribute("act");
                $root->appendChild($attrName);
                $attrValue = $dom->createTextNode("server");
                $attrName->appendChild($attrValue);
                if ($dom->saveXML()){
                    $xml = $dom->saveXML();
                }

                return $xml;
            };

            $mainResponse = array();

            if(!is_array($response))
            {
                $mainResponse['result'] = "internal API error";
                $mainResponse['result_code'] = "303";
                $mainResponse['result_description'] = "Body of module file is empty";
                $mainResponse['answer_xml'] = null;

                return json_encode($mainResponse);
            }

            $mainResponse['result'] = "success";
            $mainResponse['result_code'] = "00";
            $mainResponse['result_description'] = "CALL SUCCESSFUL";


            $response['status'] = (($requestStatus) ? 'success' : (($showStatus) ? 'fail' : 'error'));

            \application\BaseComponent::getLogger()->setInternalResponse($response);
            
            $mainResponse['answer_xml'] = $generateXMLAnswer((($diffEnc) ? $methodDetailsCrypt(json_encode($response)) : $standardCrypt(json_encode($response), $secretKey)));
            //$mainResponse['answer_xml'] = $generateXMLAnswer(json_encode($response));

            return json_encode($mainResponse);
        };

        $validationFormatter = function($description, $errorCode)
        {
                $mainResponse = array();
                $mainResponse['result'] = "internal API error";
                $mainResponse['result_code'] = $errorCode;
                $mainResponse['result_description'] = $description;
                $mainResponse['answer_xml'] = null;

                return json_encode($mainResponse);
        };

        $output = null;

        if($this->validationResponseError)
        {
            $output = $validationFormatter($this->incomingRequestResponse, $this->validationErrorCode);
        }


        if(is_null($output))
        {
            if($this->inComingRequest instanceof GetLicenseeDetailsRequest)
            {
                $output = $jsonFormatter($this->incomingRequestResponse, $this->isLastRequestSuccessful(), $this->incomingRequestShowError, "",true);
            }
            elseif($this->inComingRequest instanceof GetMethodsListRequest || $this->inComingRequest instanceof ProgressiveServerReadByCurrencyRequest)
            {
                $output = $this->incomingRequestResponse;
            }
            else
            {
                $output = $jsonFormatter($this->incomingRequestResponse, $this->isLastRequestSuccessful(), $this->incomingRequestShowError, $this->inComingRequest->getLicenseeObject()->getSceretKey());
            }

        }

        return $output;
    }



    public function isLastRequestSuccessful()
    {
        return $this->incomingRequestStatus;
    }

    /**
     * @param \component\communication\OutGoingRequest $outGoingRequest
     */
    public function setOutGoingRequest($outGoingRequest)
    {
        $this->outGoingRequest = $outGoingRequest;
    }

    /**
     * @return \component\configurationmanager\v1\ConfigurationManager
     */
    public function getConfigurationManager()
    {
        return $this->configurationManager;
    }




}