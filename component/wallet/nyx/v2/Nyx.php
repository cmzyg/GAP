<?php
namespace component\wallet\nyx\v2;


use component\communication\Communication;
use component\communication\OutGoingRequest;
use component\progressive\Progressive;
use component\request\BeforeLoginRequest;
use component\request\EndRequest;
use component\request\PlaceAndSettleBetRequest;
use component\request\PlaceBetRequest;
use component\request\Request;
use component\request\SettleBetRequest;
use component\wallet\WalletInterface;
use component\wallet\WalletResponse;
use model\GMAPIDataRepository;
use exceptions\ValidationException;


/**
 * Class Nyx
 * @author Zygimantas Simkus
 * @package component\wallet\nyx\v2
 */
class Nyx extends \component\wallet\nyx\Nyx implements WalletInterface{

private $extraConfiguration;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Before Login Process
     * @return mixed
     */
    public function beforeLogin()
    {
        if(!$this->accounting->isSessionExists())
        {
            die("i am here");
            $outPut = $this->getBalance();

            $response = $outPut[0];
            $curlCode = $outPut[1];
            $httpCode = $outPut[2];
            $httpHeader = $outPut[3];
            $sendRequest = $outPut[4];

            $this->processResponse = array();
            $walletResponse = new WalletResponse();
            

            //ToDo Check If Session Already Exist
            //ToDo Check player currency if it is the same

            if($curlCode === 0 && $httpCode == 200)
            {
                if($response->getReturnCode() == 0)
                {
                    $userBalance = intval(($response->getBalance() * 100));
                    $userId = $response->getAccountId();
                    $this->request->setPlayerId($userId);

                    $cashBalance = intval(($response->getBalance() * 100));
                    $sessionId = $this->request->getSessionId().'::'.time();
                    $this->request->setSessionId($sessionId);
                    $token = "";
                    $freeRoundId = 0;
                    $freeRoundProvider = 0;
                    $betWinSupport = $this->request->getLicenseeObject()->getFastSpeed();
                    $coinValue = '1,2,5,10,20,50,100';
                    $coinValueDefault = '1';
                    $currencyCode =  "";
                    $currencyDecimal = "";
                    $currencyThousand = "";
                    $currencyDecimalDigits = "";
                    $currencyPrefix = "";
                    $currencySuffix = ""; 
                    $freeBalance = intval($response->getBonusBalance() * 100);
                    $promotion = 0;




                
                    //accountings;
                    $this->accounting->startSession((float) ($response->getBalance()));
                
                    //TODO: Accountings

                    $this->processResponse = $walletResponse->beforeLoginResponse($userId,$userBalance, $cashBalance, $sessionId, $freeRoundProvider, $freeRoundId, $token, $betWinSupport,
                        $coinValue, $coinValueDefault, $currencyCode, $currencyDecimal, $currencyThousand, $currencyDecimalDigits, $currencyPrefix, $currencySuffix, $promotion, $freeBalance);
                    $this->processStatus = true;
                }
                else
                {
                    $this->getLogger()->log('hothot', json_encode($httpCode));
                    $this->processResponse = $walletResponse->errorResponse($httpCode, $response->getError());
                    $errorMessage = __FUNCTION__ . " :: Incorrect Wallet Answer ";
                    $this->errorLogger(self::THIRD_PARTY_EXCEPTION, $errorMessage, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $curlCode, $httpCode, $httpHeader);
                }
            }
            elseif($curlCode !== 0)
            {
                $this->processResponse = $walletResponse->curlErrorResponse($curlCode, $response);
                $errorMessage = __FUNCTION__ . " :: A Curl error has occurred  ";
                $this->errorLogger(self::CURL_EXCEPTION, $errorMessage, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $curlCode, $httpCode, $httpHeader);

            }elseif($httpCode !== 200)
            {
                $this->processResponse = $walletResponse->httpErrorResponse($httpCode, $httpHeader);
                $errorMessage = __FUNCTION__." :: An Http error has occurred";
                $this->errorLogger(self::HTTP_EXCEPTION, $errorMessage, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $sendRequest->getStatusCode(), $httpCode, $httpHeader);
            }

        }else
        {
            $ex = new ValidationException('Session already exist', 4, 2);
            $this->getLogger()->addException($ex);
            $this->processResponse['status'] = 'error';
            $this->processResponse['validation_error'] = 'Session already exist';
            \component\communication\ErrorList::$sessionExist = true;
        }

    }

    /**
     * Place Bet Process
     * @return mixed
     */
    public function placeBet()
    {
        $progressive = new Progressive();
        $progressive = $progressive->loadComponent();
        $progressive->execute($this->request);

        $progressive->updateProgressiveContribution($this->request);

        $outPut = $this->wager();

        $response = $outPut[0];
        $curlCode = $outPut[1];
        $httpCode = $outPut[2];
        $httpHeader = $outPut[3];
        $sendRequest = $outPut[4];

        $this->processResponse = array();
        $walletResponse = new WalletResponse();

        if($curlCode === 0 && $httpCode == 200)
        {
            if($response->getReturnCode() == 0)
            {
                $this->processResponse = $walletResponse->placeBetResponse(intval($response->getBalance() * 100), intval($response->getBalance() * 100), 0, '',0); //intval($response->getBonusBalance()*100));
                $this->accounting->betSession(intval($response->getBalance() * 100));
                $this->processStatus = true;
            }
            else
            {
                $this->processResponse = $walletResponse->errorResponse($httpCode, $response->getError());
                $errorMessage = __FUNCTION__ . " :: Incorrect Wallet Answer ";
                $this->errorLogger(self::THIRD_PARTY_EXCEPTION, $errorMessage, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $curlCode, $httpCode, $httpHeader);
            }
        }
        elseif($curlCode !== 0)
        {
            if($curlCode == 28 && $this->retryCount < 3)
            {
                $this->retryCount = $this->retryCount+1;
                $this->placeBet();
            }
            else
            {
                $this->cancelWager();
                $this->processResponse = $walletResponse->curlErrorResponse($curlCode, $response);
                $errorMessage = __FUNCTION__ . " :: A Curl error has occurred  ";
                $this->errorLogger(self::CURL_EXCEPTION, $errorMessage, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $curlCode, $httpCode, $httpHeader);
            }
        }elseif($httpCode !== 200)
        {
            $this->processResponse = $walletResponse->httpErrorResponse($httpCode, $httpHeader);
            $errorMessage = __FUNCTION__." :: An Http error has occurred";
            $this->errorLogger(self::HTTP_EXCEPTION, $errorMessage, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $sendRequest->getStatusCode(), $httpCode, $httpHeader);
        }
    }

    /**
     * Settle Bet Process
     * @return mixed
     */
    public function settleBet()
    {
        if($this->request->getFreeSpin() == 1 || $this->request->getFreeSpin() == 2)
        {
            $this->request->setWinAmount(($this->request->getWinAmount()/100)+(1/100));
            if($this->request->getFreeSpin() == 2)
            {
                $this->wager(1);
            }
            
            $outPut = $this->result(false);
        }
        else
        {
            $this->wager(1);
            $outPut = $this->result();
        }






        $response = $outPut[0];
        $curlCode = $outPut[1];
        $httpCode = $outPut[2];
        $httpHeader = $outPut[3];
        $sendRequest = $outPut[4];

        $this->processResponse = array();
        $walletResponse = new WalletResponse();

        if($curlCode === 0 && $httpCode == 200)
        {
            if($response->getReturnCode() == 0)
            {
                if($this->request->getFreeSpin() == 2) { $this->request->setFreeSpin(1); }
                if($this->request->getFreeSpin() == 2 || $this->request->getFreeSpin() == 1) 
                {
                    $round_status = 'ACTIVE'; 
                }
                else
                {
                    $round_status = 'INACTIVE';
                }

                $this->processResponse = $walletResponse->settleBetResponse(intval($response->getBalance()*100), intval($response->getBalance()*100), '', 0); //intval($response->getBonusBalance()*100));
                $this->accounting->winSession(intval($response->getBalance() * 100), $round_status);
                $this->processStatus = true;
            }
            else
            {
                $this->processResponse = $walletResponse->errorResponse($httpCode, $response->getError());
                $errorMessage = __FUNCTION__ . " :: Incorrect Wallet Answer ";
                $this->errorLogger(self::THIRD_PARTY_EXCEPTION, $errorMessage, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $curlCode, $httpCode, $httpHeader);
            }
        }
        elseif($curlCode !== 0)
        {
            if($curlCode == 28 && $this->retryCount < 3)
            {
                $this->retryCount = $this->retryCount+1;
                $this->settleBet();
            }
            else
            {
                $this->processResponse = $walletResponse->curlErrorResponse($curlCode, $response);
                $errorMessage = __FUNCTION__ . " :: A Curl error has occurred  ";
                $this->errorLogger(self::CURL_EXCEPTION, $errorMessage, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $curlCode, $httpCode, $httpHeader);
            }
        }elseif($httpCode !== 200)
        {
            $this->processResponse = $walletResponse->httpErrorResponse($httpCode, $httpHeader);
            $errorMessage = __FUNCTION__." :: An Http error has occurred";
            $this->errorLogger(self::HTTP_EXCEPTION, $errorMessage, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $sendRequest->getStatusCode(), $httpCode, $httpHeader);
        }
    }

    /**
     * Place and Settle Bet Process
     * @return mixed
     */
    public function placeAndSettleBet()
    {
        $this->processResponse = array("internal error" => "end Not Implemented");
    }

    /**
     * End Process
     * @return mixed
     */
    public function end()
    {
        $this->processResponse = array("result" => "Ok");
        $this->processStatus = true;
        $this->accounting->endSession();
    }

    /**
     * @return array
     */
    protected function getAccount()
    {
        // New Request
        $nyxRequest = new OutGoingRequest();
        $nyxRequest->setUrl($this->request->getLicenseeObject()->getUrl());
        // Set Authorization for communication to 3rd Party
        $login = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getLogin();
        $password = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getPassword();
        $database = new GMAPIDataRepository('irsbo');
        $rows = $database->fetchGameAndSkinId($this->request->getGameId(), $this->request->getSkinId());
        $gameType = ((strtolower($rows['type']) == 'flash') ? 'Flash' : 'HTML');
        // Compose request data
        $nyxRequestData = new NyxRequestData();
        $nyxRequestData->setClientType($gameType);
        $nyxRequestData->setCurrency($this->request->getCurrency());
        $this->extraConfiguration = $this->communicationComponent->getConfigurationManager()->getExtraConfiguration($this->request->getLicenseeObject());
        $gpId = 0;

        foreach($this->extraConfiguration as $config)
        {
            if(isset($config['GPID']))
            {
                $gpId = $config['GPID'];
                break;
            }
        }

        die(var_dump($gpId));
        //$nyxRequestData->setGameProviderGameId($this->communicationComponent->)
        // Set request data and request response conversion type
        $nyxRequest->setData($nyxRequestData->getRequestData());
        $nyxRequest->setRequestType("XML2ARRAY");
        // Set Request Object
        $this->communicationComponent->setOutGoingRequest($nyxRequest);
        // Parse sent request response
        $sendRequest = $this->communicationComponent->sendGetRequest();
        if($sendRequest->isRequestSuccessful())
        {
            $response = new NyxResponseParser($sendRequest->getRequestResponse());
        }
        else
        {
            $response = $sendRequest->getRequestError();
            $this->errorLogger(self::CURL_EXCEPTION,__FUNCTION__." :: ".$response, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $sendRequest->getStatusCode());

        }
        
        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);

    }

    /**
     * @return array
     */
    protected function getBalance()
    {
        //Get Account Details
        $account = $this->getAccount();
        $accountObject = $account[0];
        if(!is_object($accountObject))
        {
            return $account;
        }
        // New Request
        $nyxRequest = new OutGoingRequest();
        $nyxRequest->setUrl($this->request->getLicenseeObject()->getUrl());
        // Set Authorization for communication to 3rd Party
        $login = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getLogin();
        $password = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getPassword();
        // Compose request data
        $nyxRequestData = new NyxRequestData($login,$password,$this->request->getSessionId(), "getbalance");

        $nyxRequestData->setGameId($this->request->getSkinId());

        // Set request data and request response conversion type
        $nyxRequest->setData($nyxRequestData->getRequestData());
        $nyxRequest->setRequestType("XML2ARRAY");
        // Set Request Object
        $this->communicationComponent->setOutGoingRequest($nyxRequest);
        // Parse sent request response
        $sendRequest = $this->communicationComponent->sendGetRequest();
        if($sendRequest->isRequestSuccessful())
        {
            $response = new NyxResponseParser($sendRequest->getRequestResponse());
            $response->setAccountId($accountObject->getAccountId());
        }
        else
        {
            $response = $sendRequest->getRequestError();
            $this->errorLogger(self::CURL_EXCEPTION,__FUNCTION__." :: ".$response, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $sendRequest->getStatusCode());
        }

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);
    }

    protected function wager($betAmount = null)
    {
        // New Request
        $nyxRequest = new OutGoingRequest();
        $nyxRequest->setUrl($this->request->getLicenseeObject()->getUrl());
        // Set Authorization for communication to 3rd Party
        $login = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getLogin();
        $password = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getPassword();
        // Compose request data
        @list($nyxSessionId, $notNeeded) = explode("::", $this->request->getSessionId());
        $nyxRequestData = new NyxRequestData($login,$password,$nyxSessionId, "wager");
        // Set parameters for request
        $nyxRequestData->setAccountId($this->request->getUserId());
        // $nyxRequestData->setBetAmount(((is_null($betAmount)) ? $this->request->getBetAmount()/100 : $betAmount/100));
        $nyxRequestData->setBetAmount(((is_null($betAmount)) ? $this->request->getBetAmount()/100 : $betAmount/1000));
        $nyxRequestData->setRoundId($this->request->getRoundId());
        $nyxRequestData->setTransactionId(((is_null($betAmount)) ? $this->request->getTransactionId() : $this->fakeBetTransactionIdGenerator()));
        $nyxRequestData->setGameId($this->request->getSkinId());
        $nyxRequestData->setDescription("");
        $database = new GMAPIDataRepository('irsbo');
        $rows = $database->fetchGameAndSkinId($this->request->getGameId(), $this->request->getSkinId());
        $gameType = ((strtolower($rows['type']) == 'flash') ? 'Flash' : 'HTML');
        $nyxRequestData->setGameClientType($gameType);
        // Set request data and request response conversion type
        $nyxRequest->setData($nyxRequestData->getRequestData());
        $nyxRequest->setRequestType("XML2ARRAY");
        //var_dump($nyxRequestData->getRequestData());
        // Set Request Object
        $this->communicationComponent->setOutGoingRequest($nyxRequest);
        // Parse sent request response
        $sendRequest = $this->communicationComponent->sendGetRequest();
        if($sendRequest->isRequestSuccessful())
        {
            $response = new NyxResponseParser($sendRequest->getRequestResponse());
        }
        else
        {
            $response = $sendRequest->getRequestError();
            $this->errorLogger(self::CURL_EXCEPTION,__FUNCTION__." :: ".$response, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $sendRequest->getStatusCode());

        }

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);
    }

    /**
     * @param bool $complete
     * @return array
     */
    protected function result($complete = true)
    {
        // New Request
        $nyxRequest = new OutGoingRequest();
        $nyxRequest->setUrl($this->request->getLicenseeObject()->getUrl());

        // Set Authorization for communication to 3rd Party
        $login = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getLogin();
        $password = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getPassword();
        // Compose request data
       
        list($nyxSessionId, $notNeeded) = explode("::", $this->request->getSessionId());
        $nyxRequestData = new NyxRequestData($login,$password,$nyxSessionId, "result");
        // Set parameters for request
        $nyxRequestData->setAccountId($this->request->getUserId());
        $nyxRequestData->setResult($this->request->getWinAmount()/100);
        $nyxRequestData->setRoundId($this->request->getRoundId());;
        $nyxRequestData->setTransactionId($this->request->getTransactionId());
        $nyxRequestData->setGameId($this->request->getSkinId());
        $nyxRequestData->setDescription("");
        $database = new GMAPIDataRepository('irsbo');
        $rows = $database->fetchGameAndSkinId($this->request->getGameId(), $this->request->getSkinId());
        $gameType = ((strtolower($rows['type']) == 'flash') ? 'Flash' : 'HTML');
        $nyxRequestData->setGameClientType($gameType);
        $nyxRequestData->setGameStatus((($complete) ? 'complete' : 'pending'));
        // Set request data and request response conversion type
        $nyxRequest->setData($nyxRequestData->getRequestData());
        $nyxRequest->setRequestType("XML2ARRAY");

        // Set Request Object
        $this->communicationComponent->setOutGoingRequest($nyxRequest);
        // Parse sent request response
        $sendRequest = $this->communicationComponent->sendGetRequest();
        if($sendRequest->isRequestSuccessful())
        {
            $response = new NyxResponseParser($sendRequest->getRequestResponse());
        }
        else
        {
            $response = $sendRequest->getRequestError();
            $this->errorLogger(self::CURL_EXCEPTION,__FUNCTION__." :: ".$response, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $sendRequest->getStatusCode());

        }
        

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);
    }

    public function fakeBetTransactionIdGenerator(){
        $min = 9000000000000000000 + time();
        $max = 9223372036854775807;

        return mt_rand($min, $max);
    }

    protected function cancelWager()
    {
        // New Request
        $nyxRequest = new OutGoingRequest();
        $nyxRequest->setUrl($this->request->getLicenseeObject()->getUrl());
        // Set Authorization for communication to 3rd Party
        $login = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getLogin();
        $password = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getPassword();
        // Compose request data

        list($nyxSessionId, $notNeeded) = explode("::", $this->request->getSessionId());
        $nyxRequestData = new NyxRequestData($login,$password,$nyxSessionId, "cancelwager");
        // Set parameters for request
        $nyxRequestData->setAccountId($this->request->getUserId());
        $nyxRequestData->setCancelWagerAmount($this->request->getBetAmount());
        $nyxRequestData->setRoundId($this->request->getRoundId());;
        $nyxRequestData->setTransactionId($this->request->getTransactionId());
        $nyxRequestData->setGameId($this->request->getSkinId());

        // Set request data and request response conversion type
        $nyxRequest->setData($nyxRequestData->getRequestData());
        $nyxRequest->setRequestType("XML2ARRAY");

        // Set Request Object
        $this->communicationComponent->setOutGoingRequest($nyxRequest);
        // Parse sent request response
        $sendRequest = $this->communicationComponent->sendGetRequest();
        if($sendRequest->isRequestSuccessful())
        {
            $response = new NyxResponseParser($sendRequest->getRequestResponse());
        }
        else
        {
            $response = $sendRequest->getRequestError();
            $this->errorLogger(self::CURL_EXCEPTION,__FUNCTION__." :: ".$response, $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $sendRequest->getStatusCode());
        }

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);
    }







    /**
     * @param Request $request
     * @param Communication $communication
     * @return mixed|void
     */
    public function executeProcess(Request $request, Communication $communication)
    {
        $this->communicationComponent = $communication;
        $this->request = $request;
        $this->accounting->setRequest($this->request);
        
        if($request instanceof BeforeLoginRequest)
        {
            $this->beforeLogin();
        }
        elseif($request instanceof PlaceBetRequest)
        {
            $this->placeBet();
        }
        elseif($request instanceof SettleBetRequest)
        {
            $this->settleBet();
        }
        elseif($request instanceof PlaceAndSettleBetRequest)
        {
            $this->processResponse = array("error" => $this->NotSupported());
        }
        elseif($request instanceof EndRequest)
        {
            $this->end();
        }
        else
        {
            $this->processResponse = array("error" => $this->NotSupported());
        }
    }



} 