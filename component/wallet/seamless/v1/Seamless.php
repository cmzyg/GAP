<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/11/14
 * Time: 14:32
 */

namespace component\wallet\seamless\v1;

use component\communication\Communication;
use component\communication\ErrorList;
use component\communication\OutGoingRequest;
use component\request\BeforeLoginRequest;
use component\request\BalanceRequest;
use component\request\EndRequest;
use component\request\PlaceAndSettleBetRequest;
use component\request\PlaceBetRequest;
use component\request\Request;
use component\request\SettleBetRequest;
use component\request\CancelRoundRequest;
use component\wallet\WalletInterface;
use component\wallet\WalletResponse;
use exceptions\FreeRoundException;
use model\GMAPIDataRepository;
use exceptions\ValidationException;

/**
 * Class Seamless
 * @package component\wallet\seamless\v1
 */
class Seamless extends \component\wallet\seamless\Seamless implements WalletInterface {

    /**
     * Before Login Process
     * @return mixed
     */
    public function beforeLogin()
    {
        if (!$this->accounting->isSessionExists())
        {
            $output = $this->balance();
            $response = $output[0];
            $curlCode = $output[1];
            $httpCode = $output[2];
            $httpHeader = $output[3];
            $sendRequest = $output[4];

            $this->processResponse = array();

            if ($curlCode === 0 && $httpCode == 200)
            {
                if (is_int($response->getBalance()))
                {
                    $userBalance = intval($response->getBalance());
                    $userId = $this->request->getPlayerId();
                    $cashBalance = intval($response->getBalance());
                    $sessionId = $this->request->getSessionId();
                    $token = "";
                    $freeRoundId = intval($response->getFreeRoundId());
                    $freeRoundProvider = $this->request->getLicenseeObject()->getFreeRound();
                    $betWinSupport = $this->request->getLicenseeObject()->getFastSpeed();
                    $coinValue = '1,2,5,10,20,50,100';
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
                    $this->accounting->startSession((float) ($response->getBalance() / 100));


                    $walletResponse = new WalletResponse();
                    $this->processResponse = $walletResponse->beforeLoginResponse($userId, $userBalance, $cashBalance, $sessionId, $freeRoundProvider, $freeRoundId, $token, $betWinSupport, $coinValue, $coinValueDefault, $currencyCode, $currencyDecimal, $currencyThousand, $currencyDecimalDigits, $currencyPrefix, $currencySuffix, $promotion, $freeBalance);

                    if (!is_null($response->getFreeRoundId()))
                    {
                        if ($this->request->getLicenseeObject()->getFreeRound() == 2)
                        {
                            $response = new SeamlessResponseParser("");
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
                    $this->errorLogger(self::THIRD_PARTY_EXCEPTION, __FUNCTION__ . " :: Incorrect Wallet Answer has occurred", $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $curlCode, $httpCode, $httpHeader);
                    $this->processResponse['status'] = 'error';
                    $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
                    $this->processResponse['Incorrect Wallet Answer'] = $response;
                }
            } elseif ($curlCode !== 0 && $curlCode !== 22)
            {
                $this->errorLogger(self::CURL_EXCEPTION, __FUNCTION__ . " :: CURL error has occurred", $sendRequest->getUrl(), "", $curlCode . " - " . $response, $httpCode, $httpHeader);
                $this->processResponse['status'] = 'error';
                $this->processResponse['seamless_error'] = "Connection problem with the wallet";
                $this->processResponse['curl_code'] = $curlCode . " - " . $response;
            } elseif ($httpCode !== 200)
            {
                $this->errorLogger(self::HTTP_EXCEPTION, __FUNCTION__ . " :: HTTP error has occurred", $sendRequest->getUrl(), $response, $curlCode, $httpCode, $httpHeader);
                $this->processResponse['status'] = 'error';
                $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
                $this->processResponse['Description'] = (is_array($httpHeader) && isset($httpHeader['description'])? : $response);
            }
        } else
        {
            $ex = new ValidationException('Session already exist', 4, 2);
            $this->getLogger()->addException($ex);
            $this->processResponse['status'] = 'error';
            $this->processResponse['validation_error'] = 'Session already exist';
            \component\communication\ErrorList::$sessionExist = true;
        }
    }

    /**
     * Before Login Process
     * @return mixed
     */
    public function getBalance()
    {
        $output = $this->balance();
        $response = $output[0];
        $curlCode = $output[1];
        $httpCode = $output[2];
        $httpHeader = $output[3];
        $sendRequest = $output[4];

        $this->processResponse = array();

        if ($curlCode === 0 && $httpCode == 200)
        {
            if (is_int($response->getBalance()))
            {
                $userBalance = intval($response->getBalance());
                $userId = $this->request->getPlayerId();
                $cashBalance = intval($response->getBalance());
                $sessionId = $this->request->getSessionId();
                $token = "";
                $freeRoundId = intval($response->getFreeRoundId());
                $freeRoundProvider = $this->request->getLicenseeObject()->getFreeRound();
                $betWinSupport = $this->request->getLicenseeObject()->getFastSpeed();
                $coinValue = '1,2,5,10,20,50,100';
                $coinValueDefault = '1';
                $currencyCode = $this->request->getCurrency();
                $currencyDecimal = "";
                $currencyThousand = "";
                $currencyDecimalDigits = "";
                $currencyPrefix = "";
                $currencySuffix = "";
                $freeBalance = 0;
                $promotion = 0;
                $this->processStatus = true;


                //NO ACCOUNTING CHANGES

                $walletResponse = new WalletResponse();
                $this->processResponse = $walletResponse->beforeLoginResponse($userId, $userBalance, $cashBalance, $sessionId, $freeRoundProvider, $freeRoundId, $token, $betWinSupport, $coinValue, $coinValueDefault, $currencyCode, $currencyDecimal, $currencyThousand, $currencyDecimalDigits, $currencyPrefix, $currencySuffix, $promotion, $freeBalance);

                if (!is_null($response->getFreeRoundId()))
                {
                    if ($this->request->getLicenseeObject()->getFreeRound() == 2)
                    {
                        $response = new SeamlessResponseParser("");
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


            } else
            {
                $this->errorLogger(self::THIRD_PARTY_EXCEPTION, __FUNCTION__ . " :: Incorrect Wallet Answer has occurred", $sendRequest->getUrl(), $sendRequest->getNonParsedResponse(), $curlCode, $httpCode, $httpHeader);
                $this->processResponse['status'] = 'error';
                $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
                $this->processResponse['Incorrect Wallet Answer'] = $response;
            }
        } elseif ($curlCode !== 0 && $curlCode !== 22)
        {
            $this->errorLogger(self::CURL_EXCEPTION, __FUNCTION__ . " :: CURL error has occurred", $sendRequest->getUrl(), "", $curlCode . " - " . $response, $httpCode, $httpHeader);
            $this->processResponse['status'] = 'error';
            $this->processResponse['seamless_error'] = "Connection problem with the wallet";
            $this->processResponse['curl_code'] = $curlCode . " - " . $response;
        } elseif ($httpCode !== 200)
        {
            $this->errorLogger(self::HTTP_EXCEPTION, __FUNCTION__ . " :: HTTP error has occurred", $sendRequest->getUrl(), $response, $curlCode, $httpCode, $httpHeader);
            $this->processResponse['status'] = 'error';
            $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
            $this->processResponse['Description'] = (is_array($httpHeader) && isset($httpHeader['description'])? : $response);
        }
    }

    /**
     * Place Bet Process
     * @return mixed
     */
    public function placeBet()
    {
        $databaseName = $this->request->getLicenseeObject()->getDbPrefix() . "_accounting";

        if(strlen($this->request->getProviderName()) > 5 && $this->request->getProviderId() > 0)
        {
            GMAPIDataRepository::$dbProviderPrefix = $this->request->getProviderName()."_";
        }

        $data = new GMAPIDataRepository($databaseName);
        $roundInfo = $data->fetchAccountingWagers(trim($this->request->getRoundId()));
        $databaseName = $this->request->getLicenseeObject()->getDbPrefix() . "_accounting_archives";
        $data2 = new GMAPIDataRepository($databaseName);
        $roundInfoArchives = $data2->fetchAccountingWagers(trim($this->request->getRoundId()));

        if(((is_array($roundInfoArchives) && sizeof($roundInfoArchives) > 0) || (is_array($roundInfo) && sizeof($roundInfo) > 0)) && strlen($this->request->getProviderName()) > 5 && $this->request->getProviderId() > 0)
        {
            ErrorList::$duplicateRoundId = true;
            $walletResponse = new WalletResponse();
            $walletResponse->errorResponse(0, "Duplicate round id");
            $this->errorLogger(self::THIRD_PARTY_EXCEPTION, __FUNCTION__ . " :: Duplicate round id ".$this->request->getRoundId(),"", "", 0, 0, "");
            return false;
        }

        $walletResponse = new WalletResponse();

        $output = $this->bet();
        $response = $output[0];
        $curlCode = $output[1];
        $httpCode = $output[2];
        $httpHeader = $output[3];
        $sendRequest = $output[4];

        if ($httpCode == 409)
        {
            $output = $this->balance();
            $response = $output[0];
            $curlCode = $output[1];
            $httpCode = $output[2];
            $httpHeader = $output[3];
            $sendRequest = $output[4];
        }



        $this->processResponse = array();

        if ($curlCode === 0 && $httpCode == 200)
        {

            $this->processResponse = $walletResponse->placeBetResponse(intval($response->getBalance()), intval($response->getBalance()), 0, '', 0);
            $this->accounting->betSession(intval($response->getBalance()));

            $jackPotContribution = $jackPotJson = null;

            if(is_null($this->request->getProviderName()) && $this->request->getProviderId() === 0)
            {
                try
                {
                    $this->progressive->execute($this->request);
                    $jpc = $this->progressive->getProcessResponse();
                    $jackPotContribution = $jpc['sumOfJackpotValueLevels'];
                    $jackPotJson = $jpc['jsonLevelsJackpotRepresentation'];
                    $this->progressive->updateProgressiveContribution($this->request);
                    $out = $this->accounting->registerJackPotContribution($this->request->getCoinValue(), $jackPotContribution, $jackPotJson);

                    if($out === false)
                    {
                        $this->processResponse = $walletResponse->errorResponse(0, $this->accounting->getProcessResponse());
                        return false;
                    }

                }
                catch (\Exception $ex)
                {
                    $walletResponse->errorResponse(0, $ex->getMessage());
                    self::getLogger()->addException($ex);
                    $this->processResponse = $walletResponse->errorResponse(0, $ex->getMessage());
                    return false;
                }
            }

            $this->processStatus = true;



        } elseif ($curlCode !== 0)
        {
            if ($curlCode == 28 && $this->retryCount < 3)
            {
                $this->retryCount = $this->retryCount + 1;
                $this->placeBet();
            } else
            {
                $this->cancel();
                $this->processResponse['seamless_error'] = $response;
                $this->processResponse['curl_code'] = $curlCode;
            }

            if(intval($httpCode) === 403)
            {
                ErrorList::$insufficientFund = true;
            }
        } elseif ($httpCode !== 200)
        {
            if($httpCode == 401){
                \component\communication\ErrorList::$userNotFound = true;
            }

            if(intval($httpCode) === 403)
            {
                ErrorList::$insufficientFund = true;
            }

            $this->errorLogger(self::HTTP_EXCEPTION, __FUNCTION__ . " :: HTTP error has occurred", $sendRequest->getUrl(), $response, $curlCode, $httpCode, $httpHeader);
            $this->processResponse['status'] = 'error';
            $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
            $this->processResponse['Description'] = (is_array($httpHeader) && isset($httpHeader['description'])? : $response);
        
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
        $walletResponse = new WalletResponse();


        if ($curlCode === 0 && $httpCode == 200)
        {

            $this->processResponse = $walletResponse->settleBetResponse(intval($response->getBalance()), intval($response->getBalance()), '', 0);
            $this->accounting->winSession(intval($response->getBalance()));
            $this->processStatus = true;
        } elseif ($curlCode !== 0)
        {
            if ($curlCode == 28 && $this->retryCount < 3)
            {
                $this->retryCount = $this->retryCount + 1;
                $this->settleBet();
            } else
            {
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
     * Place and Settle Bet Process
     * @return mixed
     */
    public function placeAndSettleBet()
    {
        //TODO: Implement placeAndSettleBet() method.
    }

    /**
     * Cancel Bet Process
     * @return mixed
     */
    public function cancelRound()
    {
        $round = $this->accounting->getRound();

        if ($round->getBets() !== 0)
        {
            if ($this->accounting->isRoundSettled())
            {
                //cancel bet transactions for this round
                foreach ($round->getBets() as $bet)
                {
                    /* @var $bet \component\accounting\v1\entities\AccountingWagers */
                    $output = $this->cancel($bet);
                    $response = $output[0];
                    $curlCode = $output[1];
                    $httpCode = $output[2];
                    $httpHeader = $output[3];

                    if(intval($httpCode) === 403)
                    {
                        ErrorList::$cancelNotPossible = true;
                    }

                    if ($httpCode != 200 || $curlCode !== 0)
                    {
                        break;
                    }
                    $bet->setWagerStatus('CANCELLED')
                            ->setRoundStatus('CANCELLED')
                            ->update();
                }
                //cancel win transactions for this round
                foreach ($round->getWins() as $win)
                {
                    /* @var $bet \component\accounting\v1\entities\AccountingWagers */
                    $output = $this->cancel($win);
                    $response = $output[0];
                    $curlCode = $output[1];
                    $httpCode = $output[2];
                    $httpHeader = $output[3];

                    if(intval($httpCode) === 403)
                    {
                        ErrorList::$cancelNotPossible = true;
                    }

                    if ($httpCode != 200 || $curlCode !== 0)
                    {
                        break;
                    }
                    $bet->setWagerStatus('CANCELLED')
                            ->setRoundStatus('CANCELLED')
                            ->update();
                }
                //get current balance
                if ($httpCode == 200 || $curlCode === 0)
                {
                    $output = $this->balance();
                    $response = $output[0];
                    $curlCode = $output[1];
                    $httpCode = $output[2];
                    $httpHeader = $output[3];
                }
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
                    $userBalance = intval($response->getBalance());
                    $userId = $this->request->getPlayerId();
                    $cashBalance = intval($response->getBalance());
                    $sessionId = $this->request->getSessionId();
                    $token = "";
                    $freeRoundId = intval($response->getFreeRoundId());
                    $freeRoundProvider = $this->request->getLicenseeObject()->getFreeRound();
                    $betWinSupport = $this->request->getLicenseeObject()->getFastSpeed();
                    $coinValue = '1,2,5,10,20,50,100';
                    $coinValueDefault = '1';
                    $currencyCode = $this->request->getCurrency();
                    $currencyDecimal = "";
                    $currencyThousand = "";
                    $currencyDecimalDigits = "";
                    $currencyPrefix = "";
                    $currencySuffix = "";
                    $freeBalance = 0;
                    $promotion = 0;

                    $walletResponse = new WalletResponse();
                    $this->processResponse = $walletResponse->beforeLoginResponse($userId, $userBalance, $cashBalance, $sessionId, $freeRoundProvider, $freeRoundId, $token, $betWinSupport, $coinValue, $coinValueDefault, $currencyCode, $currencyDecimal, $currencyThousand, $currencyDecimalDigits, $currencyPrefix, $currencySuffix, $promotion, $freeBalance);

                    if (!is_null($response->getFreeRoundId()))
                    {
                        if ($this->request->getLicenseeObject()->getFreeRound() == 2)
                        {
                            $response = new SeamlessResponseParser("");
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
                } elseif ($curlCode !== 0)
                {
                    $this->processResponse['seamless_error'] = $response;
                    $this->processResponse['curl_code'] = $curlCode;
                } elseif ($httpCode !== 200)
                {
                    $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
                    $this->processResponse['Description'] = $httpHeader;
                }
            } else
            {
                //cancel bet transactions for this round
                foreach ($round->getBets() as $bet)
                {
                    /* @var $bet \component\accounting\v1\entities\AccountingWagers */
                    $output = $this->cancel($bet);
                    $response = $output[0];
                    $curlCode = $output[1];
                    $httpCode = $output[2];
                    $httpHeader = $output[3];

                    if(intval($httpCode) === 403)
                    {
                        ErrorList::$cancelNotPossible = true;
                    }

                    if ($httpCode != 200 || $curlCode !== 0)
                    {
                        break;
                    }
                    $bet->setWagerStatus('CANCELLED')
                            ->setRoundStatus('CANCELLED')
                            ->update();
                }
                //get current balance
                if ($httpCode == 200 || $curlCode === 0)
                {
                    $output = $this->balance();
                    $response = $output[0];
                    $curlCode = $output[1];
                    $httpCode = $output[2];
                    $httpHeader = $output[3];
                }
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
                    $userBalance = intval($response->getBalance());
                    $userId = $this->request->getPlayerId();
                    $cashBalance = intval($response->getBalance());
                    $sessionId = $this->request->getSessionId();
                    $token = "";
                    $freeRoundId = intval($response->getFreeRoundId());
                    $freeRoundProvider = $this->request->getLicenseeObject()->getFreeRound();
                    $betWinSupport = $this->request->getLicenseeObject()->getFastSpeed();
                    $coinValue = '1,2,5,10,20,50,100';
                    $coinValueDefault = '1';
                    $currencyCode = $this->request->getCurrency();
                    $currencyDecimal = "";
                    $currencyThousand = "";
                    $currencyDecimalDigits = "";
                    $currencyPrefix = "";
                    $currencySuffix = "";
                    $freeBalance = 0;
                    $promotion = 0;

                    $walletResponse = new WalletResponse();
                    $this->processResponse = $walletResponse->beforeLoginResponse($userId, $userBalance, $cashBalance, $sessionId, $freeRoundProvider, $freeRoundId, $token, $betWinSupport, $coinValue, $coinValueDefault, $currencyCode, $currencyDecimal, $currencyThousand, $currencyDecimalDigits, $currencyPrefix, $currencySuffix, $promotion, $freeBalance);

                    if (!is_null($response->getFreeRoundId()))
                    {
                        if ($this->request->getLicenseeObject()->getFreeRound() == 2)
                        {
                            $response = new SeamlessResponseParser("");
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
                } elseif ($curlCode !== 0)
                {
                    $this->processResponse['seamless_error'] = $response;
                    $this->processResponse['curl_code'] = $curlCode;
                } elseif ($httpCode !== 200)
                {
                    $this->processResponse['seamless_error'] = "HTTP " . $httpCode;
                    $this->processResponse['Description'] = $httpHeader;
                }
            }
        } else
        {
            $this->processResponse['validation_error'] = 'Round doesn\'t exist';
        }
    }

    /**
     * End Process
     * @return mixed
     */
    public function end()
    {
        $this->accounting->endSession();
        // TODO: Implement end() method.
    }

    protected function balance()
    {
        $walletRequest = new SeamlessRequestData("balance", $this->request->getLicenseeObject()->getSceretKey());
        $walletRequest->setSessionId($this->request->getSessionId());
        $walletRequest->setPlayer($this->request->getPlayerId());
        $walletRequest->setGameId($this->request->getSkinId());
        $walletRequest->setOperator($this->request->getOperatorId());
        $seamlessRequest = new OutGoingRequest();
        $seamlessRequest->setSendPostOnLink(true);
        $seamlessRequest->setUrl($this->request->getLicenseeObject()->getUrl());
        $seamlessRequest->setData($walletRequest->getRequestData());
        $seamlessRequest->setRequestType("JSON2ARRAY");
        $this->communicationComponent->setOutGoingRequest($seamlessRequest);
        $sendRequest = $this->communicationComponent->sendPostRequest();

        if ($sendRequest->isRequestSuccessful())
        {
            $response = new SeamlessResponseParser($sendRequest->getRequestResponse());
        } else
        {
            $response = $sendRequest->getRequestError();
        }
        $this->getLogger()->log('request', array('request' => json_encode($walletRequest->getRequestData()), 'request_url' => $this->request->getLicenseeObject()->getUrl() . '?' . http_build_query($walletRequest->getRequestData()), 'response' => ($response instanceof SeamlessResponseParser) ? $sendRequest->getRequestResponse() : $response));
        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);
    }

    protected function bet()
    {
        $walletRequest = new SeamlessRequestData("bet", $this->request->getLicenseeObject()->getSceretKey());
        $walletRequest->setSessionId($this->request->getSessionId());
        $walletRequest->setPlayer($this->request->getUserId());
        $walletRequest->setGameId($this->request->getSkinId());
        $walletRequest->setOperator($this->request->getOperatorId());
        $walletRequest->setRoundId($this->request->getRoundId());
        $walletRequest->setTransactionId($this->request->getTransactionId());
        $walletRequest->setCredit($this->request->getBetAmount());
        $walletRequest->setJackPotContribution(0);
        if ($this->request->getFreeRoundId() > 0)
        {
            $walletRequest->setFreeRoundId($this->request->getFreeRoundId());
            $walletRequest->setFreeRoundCoinValue($this->request->getCoinValue());
            $walletRequest->setFreeRoundLines($this->request->getLines());
            $walletRequest->setFreeRoundLineBet($this->request->getLineBet());
        }

        $seamlessRequest = new OutGoingRequest();
        $seamlessRequest->setSendPostOnLink(true);
        $seamlessRequest->setUrl($this->request->getLicenseeObject()->getUrl());
        $seamlessRequest->setData($walletRequest->getRequestData());
        $seamlessRequest->setRequestType("JSON2ARRAY");
        $this->communicationComponent->setOutGoingRequest($seamlessRequest);
        $sendRequest = $this->communicationComponent->sendPostRequest();

        if ($sendRequest->isRequestSuccessful())
        {
            $response = new SeamlessResponseParser($sendRequest->getRequestResponse());
        } else
        {
            $response = $sendRequest->getRequestError();
        }
        $this->getLogger()->log('request', array('request' => json_encode($walletRequest->getRequestData()), 'request_url' => $this->request->getLicenseeObject()->getUrl() . '?' . http_build_query($walletRequest->getRequestData()), 'response' => ($response instanceof SeamlessResponseParser) ? $sendRequest->getRequestResponse() : $response));
        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);
    }

    protected function win()
    {
        $walletRequest = new SeamlessRequestData("win", $this->request->getLicenseeObject()->getSceretKey());
        $walletRequest->setSessionId($this->request->getSessionId());
        $walletRequest->setPlayer($this->request->getUserId());
        $walletRequest->setGameId($this->request->getSkinId());
        $walletRequest->setOperator($this->request->getOperatorId());
        $walletRequest->setRoundId($this->request->getRoundId());
        $walletRequest->setTransactionId($this->request->getTransactionId());
        $walletRequest->setCredit($this->request->getWinAmount());
        $walletRequest->setCloseRound(((in_array($this->request->getFreeSpin(), array(1, 2))) ? "false" : "true"));
        $walletRequest->setFinish((($this->request->getDescription() == 1) ? "false" : "true"));
        $walletRequest->setJackPotWin(0);
        if ($this->request->getFreeRoundId() > 0)
        {
            $walletRequest->setFreeRoundId($this->request->getFreeRoundId());
            $walletRequest->setFreeRoundCoinValue($this->request->getCoinValue());
            $walletRequest->setFreeRoundLines($this->request->getLines());
            $walletRequest->setFreeRoundLineBet($this->request->getLineBet());
        }

        $seamlessRequest = new OutGoingRequest();
        $seamlessRequest->setSendPostOnLink(true);
        $seamlessRequest->setUrl($this->request->getLicenseeObject()->getUrl());
        $seamlessRequest->setData($walletRequest->getRequestData());
        $seamlessRequest->setRequestType("JSON2ARRAY");
        $this->communicationComponent->setOutGoingRequest($seamlessRequest);
        $sendRequest = $this->communicationComponent->sendPostRequest();

        if ($sendRequest->isRequestSuccessful())
        {
            $response = new SeamlessResponseParser($sendRequest->getRequestResponse());
        } else
        {
            $response = $sendRequest->getRequestError();
        }
        $this->getLogger()->log('request', array('request' => json_encode($walletRequest->getRequestData()), 'request_url' => $this->request->getLicenseeObject()->getUrl() . '?' . http_build_query($walletRequest->getRequestData()), 'response' => ($response instanceof SeamlessResponseParser) ? $sendRequest->getRequestResponse() : $response));
        return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);
    }

    protected function cancel(\component\accounting\v1\entities\AccountingWagers $transaction = NULL)
    {
        if ($transaction == NULL)
        {
            $walletRequest = new SeamlessRequestData("cancel", $this->request->getLicenseeObject()->getSceretKey());
            $walletRequest->setSessionId($this->request->getSessionId());
            $walletRequest->setPlayer($this->request->getUserId());
            $walletRequest->setGameId($this->request->getSkinId());
            $walletRequest->setOperator($this->request->getOperatorId());
            $walletRequest->setRoundId($this->request->getRoundId());
            $walletRequest->setTransactionId($this->request->getTransactionId());
            $walletRequest->setCredit($this->request->getBetAmount());
            $walletRequest->setJackPotContribution(0);
            if ($this->request->getFreeRoundId() > 0)
            {
                $walletRequest->setFreeRoundId($this->request->getFreeRoundId());
                $walletRequest->setFreeRoundCoinValue($this->request->getCoinValue());
                $walletRequest->setFreeRoundLines($this->request->getLines());
                $walletRequest->setFreeRoundLineBet($this->request->getLineBet());
            }
            $seamlessRequest = new OutGoingRequest();
            $seamlessRequest->setSendPostOnLink(true);
            $seamlessRequest->setUrl($this->request->getLicenseeObject()->getUrl());
            $seamlessRequest->setData($walletRequest->getRequestData());
            $seamlessRequest->setRequestType("JSON2ARRAY");
            $this->communicationComponent->setOutGoingRequest($seamlessRequest);
            $sendRequest = $this->communicationComponent->sendPostRequest();

            if ($sendRequest->isRequestSuccessful())
            {
                $response = new SeamlessResponseParser($sendRequest->getRequestResponse());
            } else
            {
                $response = $sendRequest->getRequestError();
            }
            $this->getLogger()->log('request', array('request' => json_encode($walletRequest->getRequestData()), 'request_url' => $this->request->getLicenseeObject()->getUrl() . '?' . http_build_query($walletRequest->getRequestData()), 'response' => ($response instanceof SeamlessResponseParser) ? $sendRequest->getRequestResponse() : $response));
            return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);
        } else
        {
            $walletRequest = new SeamlessRequestData("cancel", $this->request->getLicenseeObject()->getSceretKey());
            $walletRequest->setSessionId($this->request->getSessionId());
            $walletRequest->setPlayer($this->request->getUserId());
            $walletRequest->setGameId($this->request->getSkinId());
            $walletRequest->setOperator($this->request->getOperatorId());
            $walletRequest->setRoundId($this->request->getRoundId());
            $walletRequest->setTransactionId($transaction->getTransactionId());
            $walletRequest->setCredit(($transaction->getWagerType() == 'BET') ? $transaction->getBetAmount() : $transaction->getWinAmount());
            $walletRequest->setJackPotContribution(0);
            $walletRequest->setJackPotWin(0);
            if ($this->request->getFreeRoundId() > 0)
            {
                $walletRequest->setFreeRoundId($this->request->getFreeRoundId());
                $walletRequest->setFreeRoundCoinValue($this->request->getCoinValue());
                $walletRequest->setFreeRoundLines($this->request->getLines());
                $walletRequest->setFreeRoundLineBet($this->request->getLineBet());
            }
            $seamlessRequest = new OutGoingRequest();
            $seamlessRequest->setSendPostOnLink(true);
            $seamlessRequest->setUrl($this->request->getLicenseeObject()->getUrl());
            $seamlessRequest->setData($walletRequest->getRequestData());
            $seamlessRequest->setRequestType("JSON2ARRAY");
            $this->communicationComponent->setOutGoingRequest($seamlessRequest);
            $sendRequest = $this->communicationComponent->sendPostRequest();

            if ($sendRequest->isRequestSuccessful())
            {
                $response = new SeamlessResponseParser($sendRequest->getRequestResponse());
            } else
            {
                $response = $sendRequest->getRequestError();
            }

            $this->getLogger()->log('request', array('request' => json_encode($walletRequest->getRequestData()), 'request_url' => $this->request->getLicenseeObject()->getUrl() . '?' . http_build_query($walletRequest->getRequestData()), 'response' => ($response instanceof SeamlessResponseParser) ? $sendRequest->getRequestResponse() : $response));
            return array($response, $sendRequest->getStatusCode(), $sendRequest->getHttpCode(), $sendRequest->getRequestHeader(), $sendRequest);
        }
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
                $this->getLogger()->addException(new FreeRoundException("Could not fetch operator for free round ", 1, 1));
            } else
            {
                $id = $result['operator_id'];
            }
        }


        return $id;
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
        } elseif ($request instanceof BalanceRequest)
        {
            $this->getBalance();
        } elseif ($request instanceof PlaceBetRequest)
        {
            $this->placeBet();
        } elseif ($request instanceof SettleBetRequest)
        {
            $this->settleBet();
        } elseif ($request instanceof PlaceAndSettleBetRequest)
        {
            $this->processResponse = array("error" => $this->NotSupported());
        } elseif ($request instanceof CancelRoundRequest)
        {
            $this->cancelRound();
        } elseif ($request instanceof EndRequest)
        {
            $this->end();
        } else
        {
            $this->processResponse = array("error" => $this->NotSupported());
        }
    }

}
