<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/11/14
 * Time: 14:32
 */

namespace component\wallet\purse\v1;

use application\Template;
use component\accounting\Accounting;
use component\communication\Communication;
use component\communication\OutGoingRequest;
use component\request\BeforeLoginRequest;
use component\request\EndRequest;
use component\request\PlaceAndSettleBetRequest;
use component\request\PlaceBetRequest;
use component\request\Request;
use component\request\SettleBetRequest;
use component\request\WalletRequest;
use component\wallet\WalletInterface;
use component\wallet\WalletResponse;
use exceptions\FreeRoundException;
use model\GMAPIDataRepository;
use exceptions\ValidationException;

/**
 * Class Seamless
 * @package component\wallet\seamless\v1
 */
class Purse extends \component\wallet\purse\Purse implements WalletInterface {

    protected $retryCount = 0;
    private $walletRequest;

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
        // generate new session ID
        $newSessionId = $this->generateSessionId($this->request->getPlayerId());
        $newToken     = $this->request->getSessionId();
        $this->request->setSessionId($newSessionId);

        if(!$this->accounting->isSessionExists())
        {
            if(!$this->validateCurrencyFormat($this->request->getCurrency()))
            {
                $ve = new ValidationException("Currency has incorrect format", 5, 1);
                $this->getLogger()->addException($ve);
                $this->processResponse['status'] = 'error';
                $this->processResponse['validation_error'] = "Currency has incorrect format";
                return false;
            }

            $operator = $this->getOperatorIdAndStatus($this->request->getLicenseeId(), $this->request->getOperatorName());
            $coins    = $this->validateCurrencyAndGetCoinValue($operator['operator_id'], $operator['status'], $this->request->getCurrency(), $this->request->getSkinId());

            if($coins === false)
            {
                $ve = new ValidationException("Currency / Operator doesnt exist in backoffice configuration", 5, 2);
                $this->getLogger()->addException($ve);
                $this->processResponse['status'] = 'error';
                $this->processResponse['validation_error'] = "Currency / Operator doesnt exist in backoffice configuration";
                return false;
            }

            $output = $this->init($newSessionId, $newToken);
            /* @var $response PurseResponseParser */
            $response = $output[0];
            $curlCode = $output[1];
            $httpCode = $output[2];
            $httpHeader = $output[3];

            $this->processResponse = array();

            if ($curlCode === 0 && $httpCode == 200)
            {
                if (intval($response->getBalance()) >= 0 && $response->isRequestSuccessful())
                {
                    $userBalance = intval($response->getBalance());
                    $userId = $this->request->getPlayerId();
                    $cashBalance = intval($response->getBalance());
                    $sessionId = $this->request->getSessionId();
                    $token = '';
                    $freeRoundId = intval($response->getFreeRoundId());
                    $freeRoundProvider = $this->request->getLicenseeObject()->getFreeRound();
                    $betWinSupport = $this->request->getLicenseeObject()->getFastSpeed();
                    $coinValue = $coins;
                    $coinValueDefault = '1';
                    $currencyCode = $this->request->getCurrency();
                    $currencyDecimal = "";
                    $currencyThousand = "";
                    $currencyDecimalDigits = "";
                    $currencyPrefix = "";
                    $currencySuffix = "";
                    $freeBalance = 0;
                    $promotion = 0;

                    //accountings;
                    $this->accounting->startSession((float) ($response->getBalance()));
                    //$this->accounting->startSession($this->request, (float) ($response->getBalance() / 100));


                    $walletResponse = new WalletResponse();
                    $this->processResponse = $walletResponse->beforeLoginResponse($userId, $userBalance, $cashBalance, $sessionId, $freeRoundProvider, $freeRoundId, $token, $betWinSupport, $coinValue, $coinValueDefault, $currencyCode, $currencyDecimal, $currencyThousand, $currencyDecimalDigits, $currencyPrefix, $currencySuffix, $promotion, $freeBalance);

                    if (!is_null($response->getFreeRoundId()))
                    {
                        if ($this->request->getLicenseeObject()->getFreeRound() == 2)
                        {
                            $response = new PurseResponseParser("");
                            $this->processResponse['fround_rounds'] = intval($response->getFreeRoundRounds());
                            $this->processResponse['fround_coin_value'] = intval($response->getFreeRoundCoinValue());
                            $this->processResponse['fround_lines'] = intval($response->getFreeRoundLines());
                            $this->processResponse['fround_line_bet'] = intval($response->getFreeRoundLineBet());
                        } elseif ($this->request->getLicenseeObject()->getFreeRound() == 1)
                        {
                            $databaseName = $this->request->getLicenseeObject()->getDbPrefix() . "_casino_games";
                            $operatorId = $this->getOperatorId();
                            $data = new GMAPIDataRepository($databaseName);
                            $freeRound = $data->fetchFreeRound($this->request->getCurrency(), $this->request->getPlayerId(), $this->request->getSkinId(), $operatorId);
                            $remaining = $freeRound['limit_per_player'] - $freeRound['amount_spent'];

                            $this->processResponse['fround_rounds'] = intval($remaining);
                            $this->processResponse['fround_coin_value'] = intval($freeRound['coin_value']);
                            $this->processResponse['fround_lines'] = intval($freeRound['lines']);
                            $this->processResponse['fround_line_bet'] = intval($freeRound['line_bet']);
                        }
                    } else
                    {
                        $this->processResponse['fround_id'] = 0;
                        $this->processResponse['fround_rounds'] = 0;
                        $this->processResponse['fround_coin_value'] = 0;
                        $this->processResponse['fround_lines'] = 0;
                        $this->processResponse['fround_line_bet'] = 0;
                    }

                    $this->processStatus = true;
                } else
                {
                    $this->processResponse['seamless_error'] = $httpCode;
                    $this->processResponse['Incorrect Wallet Answer'] = $response->getCode()." ".$response->getMessage();

                }
            } elseif ($curlCode !== 0)
            {
                 $this->processResponse['seamless_error'] = $response;
                $this->processResponse['curl_code'] = $curlCode;
            } elseif ($httpCode !== 200)
            {
                $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
                $this->processResponse['Description'] = $httpHeader;
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
        $output = $this->bet();

        $response = $output[0];
        $curlCode = $output[1];
        $httpCode = $output[2];
        $httpHeader = $output[3];

        if ($httpCode == 409)
        {
            $output = $this->balance();
            $response = $output[0];
            $curlCode = $output[1];
            $httpCode = $output[2];
            $httpHeader = $output[3];
        }

        $this->processResponse = array();

        // do reconciliation if there's something wrong with the bet
        $data = new GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix() . '_accounting');
        if($this->retryCount == 0)
        {
            // $data->saveRequestToReconciliation("BET", $this->request, $this->request->getSessionId());
        }

        if ($curlCode === 0 && $httpCode == 200)
        {
           //  $data->deleteReconciliation("BET", $this->request, $this->request->getSessionId());
            $walletResponse = new WalletResponse();
            $this->accounting->betSession(intval($response->getBalance() * 100));
            $this->processResponse = $walletResponse->placeBetResponse(intval($response->getBalance() * 100), intval($response->getBalance() * 100), 0, '', 0);
            $this->processStatus = true;
        } elseif ($curlCode !== 0)
        {
            if ($curlCode == 28 && $this->retryCount < 3)
            {
                $this->retryCount = $this->retryCount + 1;
                $this->placeBet();
            } else
            {
                $this->cancel(true);
                $this->processResponse['seamless_error'] = $response;
                $this->processResponse['curl_code'] = $curlCode;
            }
        } elseif ($httpCode !== 200)
        {
            $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
            $this->processResponse['Description'] = $httpHeader;
        }
    }

    /**
     * Settle Bet Process
     * @return mixed
     */
    public function settleBet()
    {
        $output = $this->win();
        $response = $output[0];
        $curlCode = $output[1];
        $httpCode = $output[2];
        $httpHeader = $output[3];

        if ($httpCode == 409)
        {
            $output = $this->balance();
            $response = $output[0];
            $curlCode = $output[1];
            $httpCode = $output[2];
            $httpHeader = $output[3];
        }

        $this->processResponse = array();

        if ($curlCode === 0 && $httpCode == 200)
        {

            $walletResponse = new WalletResponse();
            $this->processResponse = $walletResponse->settleBetResponse(intval($response->getBalance()), intval($response->getBalance() * 100), '', 0);
            $this->processStatus = true;

            if($this->request->getFreeSpin() == 2) { $this->request->setFreeSpin(1); }
            if($this->request->getFreeSpin() == 2 || $this->request->getFreeSpin() == 1)
            {
                $round_status = 'ACTIVE';
            }
            else
            {
                $round_status = 'INACTIVE';
            }

            $this->accounting->winSession(intval($response->getBalance() * 100), $round_status);


        } elseif ($curlCode !== 0)
        {
            if ($curlCode == 28 && $this->retryCount < 3)
            {
                $this->retryCount = $this->retryCount + 1;
                $this->placeBet();
            } else
            {
                $this->processResponse['seamless_error'] = $response;
                $this->processResponse['curl_code'] = $curlCode;
            }
        } elseif ($httpCode !== 200)
        {
            $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
            $this->processResponse['Description'] = $response;
        }
    }

    /**
     * Place and Settle Bet Process
     * @return mixed
     */
    public function placeAndSettleBet()
    {
        // TODO: Implement placeAndSettleBet() method.
    }

    /**
     * End Process
     * @return mixed
     */
    public function end()
    {
        if(is_null($this->request))
        {
            $walletRequest = new PurseRequestData();
            $walletRequest->setCurrency($this->request->getCurrency());
            $walletRequest->setSessionId($this->request->getSessionId());
            $walletRequest->setGameId($this->request->getSkinId());
            $walletRequest->setPlayerId($this->request->getPlayerId());
            $walletRequest->setOperator($this->request->getOperatorName());


            $findResult = $this->findErrorsForSession($this->request->getSessionId());
            $error = false;

            if (is_array($findResult) && sizeof($findResult) > 0)
            {
                $walletRequest->endRequest("ERROR");
                $error = true;
            }
            else
            {
                $walletRequest->endRequest();
            }

            $requestData = $walletRequest->getRequestArray();
        }
        else
        {
            $sessionID = $this->request->getSessionId();
            $userId    = $this->request->getPlayerId();
        }

        if(isset($sessionID) && isset($userId))
        {
            if ($sessionID != '' && $userId != '')
            {
                $this->getLogger()->log('endmethod', json_encode($sessionID . ' ' . $userId));
                $purseRequest = new OutGoingRequest();
                $purseRequest->setUrl($this->purseUrl($requestData, $this->request->getLicenseeObject()->getUrl(), $this->request->getLicenseeObject()->getSceretKey()));
                $purseRequest->setData(json_encode($requestData));
                $purseRequest->setRequestType("JSON2ARRAY");
                $this->communicationComponent->setOutGoingRequest($purseRequest);
                $sendRequest = $this->communicationComponent->sendPostRequest();

                // $data = new GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix() . '_accounting');
                // $data->saveRequestToReconciliation("END", $this->request, $this->request->getSessionId(), "RECON");

                $this->accounting->endSession();

                if ($sendRequest->isRequestSuccessful())
                {
                    $response = new PurseResponseParser($sendRequest->getRequestResponse());
                }
                else
                {
                    $response = $sendRequest->getRequestError();
                }

                // $this->processResponse = array("result" => "Ok");
                // $this->processStatus = true;
            }
        }
        else
        {
            $response = json_encode($response);
        }

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader());
    }

    protected function generateSessionId($salt)
    {
        return md5($salt.microtime());
    }

    protected function purseUrl($data,$url, $secretKey)
    {
        $build = hash_hmac('SHA256',json_encode($data),trim($secretKey));
        $u = $url. '?hash='. $build;
        return $u;
    }

    protected function init($sessionId, $token)
    {   
        //$walletRequest = new PurseRequestData("init", $this->request->getLicenseeObject()->getSceretKey());
        $walletRequest = new PurseRequestData();
        $walletRequest->setCurrency($this->request->getCurrency());
        $walletRequest->setSessionId($sessionId);
        $walletRequest->setGameId($this->request->getSkinId());
        $walletRequest->setPlayerId($this->request->getPlayerId());
        $walletRequest->setOperator(((is_null($this->request->getOperatorId())) ? 0 : $this->request->getOperatorId()));
        $walletRequest->initRequest($token);
        $purseRequest = new OutGoingRequest();
        $purseRequest->setSendPostOnLink(false);
        $requestData = $walletRequest->getRequestArray();
        $purseRequest->setUrl($this->purseUrl($requestData, $this->request->getLicenseeObject()->getUrl(), $this->request->getLicenseeObject()->getSceretKey()));
        $purseRequest->setData(json_encode($requestData));
        $purseRequest->setRequestType("JSON2ARRAY");
        
        $this->communicationComponent->setOutGoingRequest($purseRequest);
        $sendRequest = $this->communicationComponent->sendPostRequest();
        if ($sendRequest->isRequestSuccessful())
        {
            $response = new PurseResponseParser($sendRequest->getRequestResponse());
        } else
        {
            $response = $sendRequest->getRequestError();
        }

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader());
    }

    protected function balance()
    {
        $walletRequest = new PurseRequestData();
        $walletRequest->setCurrency($this->request->getCurrency());
        $walletRequest->setSessionId($this->request->getSessionId());
        $walletRequest->setGameId($this->request->getSkinId());
        $walletRequest->setPlayerId($this->request->getPlayerId());
        $walletRequest->setOperator(((is_null($this->request->getOperatorId())) ? 0 : $this->request->getOperatorId()));
        $walletRequest->balanceRequest();

        $purseRequest = new OutGoingRequest();
        $requestData = $walletRequest->getRequestArray();
        $purseRequest->setUrl($this->purseUrl($requestData, $this->request->getLicenseeObject()->getUrl(), $this->request->getLicenseeObject()->getSceretKey()));
        $purseRequest->setData(json_encode($requestData));
        $purseRequest->setRequestType("JSON2ARRAY");
        $this->communicationComponent->setOutGoingRequest($purseRequest);
        $sendRequest = $this->communicationComponent->sendPostRequest();

        if ($sendRequest->isRequestSuccessful())
        {
            $response = new PurseResponseParser($sendRequest->getRequestResponse());
        } else
        {
            $response = $sendRequest->getRequestError();
        }

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader());
    }

    protected function bet()
    {
        $walletRequest = new PurseRequestData();
        $walletRequest->setCurrency($this->request->getCurrency());
        $walletRequest->setSessionId($this->request->getSessionId());
        $walletRequest->setGameId($this->request->getSkinId());
        $walletRequest->setPlayerId($this->request->getPlayerId());
        $walletRequest->setOperator(((is_null($this->request->getOperatorId())) ? 0 : $this->request->getOperatorId()));

        $transactionId = $this->request->getTransactionId();
        $roundId       = $this->request->getRoundId();
        $wager         = $this->request->getBetAmount();
        $jackpotC      = '0.000025000';
        $freeRoundData = true;

        $walletRequest->betRequest($transactionId, $roundId, $wager, $jackpotC, $freeRoundData);

        $seamlessRequest = new OutGoingRequest();
        $requestData = $walletRequest->getRequestArray();
        $seamlessRequest->setUrl($this->purseUrl($requestData, $this->request->getLicenseeObject()->getUrl(), $this->request->getLicenseeObject()->getSceretKey()));
        $seamlessRequest->setData(json_encode($requestData));
        $seamlessRequest->setRequestType("JSON2ARRAY");
        $this->communicationComponent->setOutGoingRequest($seamlessRequest);
        $sendRequest = $this->communicationComponent->sendPostRequest();

        $this->walletRequest = $walletRequest;

        if ($sendRequest->isRequestSuccessful())
        {
            $response = new PurseResponseParser($sendRequest->getRequestResponse());
        } else
        {
            $response = $sendRequest->getRequestError();
        }

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader());
    }

    protected function win()
    {
        $walletRequest = new PurseRequestData();

        $walletRequest->setCurrency($this->request->getCurrency());
        $walletRequest->setSessionId($this->request->getSessionId());
        $walletRequest->setGameId($this->request->getSkinId());
        $walletRequest->setPlayerId($this->request->getPlayerId());
        $walletRequest->setOperator(((is_null($this->request->getOperatorId())) ? 0 : $this->request->getOperatorId()));

        $transactionId = $this->request->getTransactionId();
        $roundId       = $this->request->getRoundId();
        $winAmount     = $this->request->getWinAmount();
        $closeRound    = in_array($this->request->getFreeSpin(), array(1, 2)) ? false : true;
        $jackpotW      = '0.000025000';
        $freeRoundData = true;

        $walletRequest->winRequest($transactionId, $roundId, $winAmount, $jackpotW, $closeRound, $freeRoundData);

        $requestData = $walletRequest->getRequestArray();

        /*
        $walletRequest->setCredit($this->request->getWinAmount());
        $walletRequest->setCloseRound(((in_array($this->request->getFreeSpin(), array(1, 2))) ? "false" : "true"));
        $walletRequest->setFinish((($this->request->getDescription() == 1) ? "false" : "true"));
        $walletRequest->setJackPotWin(0.000025000);
        if ($this->request->getFreeRoundId() > 0)
        {
            $walletRequest->setFreeRoundId($this->request->getFreeRoundId());
            $walletRequest->setFreeRoundCoinValue($this->request->getCoinValue());
            $walletRequest->setFreeRoundLines($this->request->getLines());
            $walletRequest->setFreeRoundLineBet($this->request->getLineBet());
        }
        */

        $purseRequest = new OutGoingRequest();
        $purseRequest->setUrl($this->purseUrl($requestData, $this->request->getLicenseeObject()->getUrl(), $this->request->getLicenseeObject()->getSceretKey()));
        $purseRequest->setData(json_encode($requestData));
        $purseRequest->setRequestType("JSON2ARRAY");
        $this->communicationComponent->setOutGoingRequest($purseRequest);
        $sendRequest = $this->communicationComponent->sendPostRequest();

     
        if ($sendRequest->isRequestSuccessful())
        {
            $response = new PurseResponseParser($sendRequest->getRequestResponse());
        } else
        {
            $response = $sendRequest->getRequestError();
        }

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader());
    }

    protected function cancel($receivedFromProcess = false)
    {
        if(is_object($this->walletRequest) && $this->walletRequest instanceof PurseRequestData && $receivedFromProcess == true)
        {
            $this->walletRequest->cancelUseBetParameters();
        }
        else
        {
            $this->walletRequest = new PurseRequestData();
        }

        $requestData = $this->walletRequest->getRequestArray();

        $purseRequest = new OutGoingRequest();
        $purseRequest->setSendPostOnLink(true);
        $purseRequest->setUrl($this->purseUrl($requestData, $this->request->getLicenseeObject()->getUrl(), $this->request->getLicenseeObject()->getSceretKey()));
        $purseRequest->setData(json_encode($requestData));
        $purseRequest->setRequestType("JSON2ARRAY");
        $this->communicationComponent->setOutGoingRequest($purseRequest);
        $sendRequest = $this->communicationComponent->sendPostRequest();

        if ($sendRequest->isRequestSuccessful())
        {
            $response = new PurseResponseParser($sendRequest->getRequestResponse());
        } else
        {
            // $data = new GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix() . '_accounting');
            // $data->saveRequestToReconciliation("CANCEL", $this->request, $this->request->getSessionId(), "RECON");
            $response = $sendRequest->getRequestError();
        }

        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader());
    }

    protected function getOperatorId()
    {
        $id = null;
        if ($this->request->getOperatorName() == '0')
        {
            $id = $this->request->getOperatorName();
        } else
        {
            $data = new GMAPIDataRepository('bo_operators_per_licensee');
            $result = $data->fetchOperatorIdByLicenseeAndOperatorName($this->request->getLicenseeId(), $this->request->getOperatorName());

            if ((is_array($result) && sizeof($result) == 0) || $result === false)
            {
                self::getLogger()->addException(new FreeRoundException("Could not fetch operator for free round ", 1, 1));
            } else
            {
                $id = $result['operator_id'];
            }
        }


        return $id;
    }

    public function findErrorsForSession($sessionId)
    {
        $data   = new GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix() . '_accounting');
        $result = $data->findErrorsForSession($sessionId);
        $result = $result[0];
        if($result == 0 || sizeof($result) < 1)
        {
            return array();
        }

        return $result;
    }


    /**
     * Execute Process
     * @param Request $request
     * @param \component\communication\Communication $communication
     * @return mixed
     */
    public function executeProcess(Request $request, Communication $communication)
    {
        $this->communicationComponent = $communication;
        $this->request = $request;
        $this->accounting->setRequest($this->request);

        if ($request instanceof BeforeLoginRequest)
        {
            $this->beforeLogin();
        } elseif ($request instanceof PlaceBetRequest)
        {
            $this->placeBet();
        } elseif ($request instanceof SettleBetRequest)
        {
            $this->settleBet();
        } elseif ($request instanceof PlaceAndSettleBetRequest)
        {
            $this->processResponse = array("error" => $this->NotSupported());
        } elseif ($request instanceof EndRequest)
        {
            $this->end();
        } else
        {
            $this->processResponse = array("error" => $this->NotSupported());
        }
    }

}
