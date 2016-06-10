<?php

/**
 * Description of BetRadar
 *
 * @author rafal
 */

namespace component\soap\v1;
use component\communication\Communication;
use component\communication\ErrorList;
use component\soap\SoapInterface;
use \component\communication\Provider;
use model\GMAPIDataRepository as db;
use \component\logger\v1\Logger as Log;
use SoapFault;


class Betradar implements SoapInterface{

    public function callRequestExecutor()
    {
        $com = new Communication();
        $com = $com->loadComponent();
        $com->receiveGetRequest();
        $response = $com->getIncomingRequestResponse();
        return $response;
    }

    /**
     * @param $params
     * @return \StdClass
     */
    public function userInfo($params)
    {
        $index = 0;
        $response = null;


        $requestParameter = ((is_array($params->requests->UserInfoRequest)) ? $params->requests->UserInfoRequest[$index] : $params->requests->UserInfoRequest);

        $responseMaker = function($output, $status, $correlationNumber = null, $balance = null, $currencyCode = null, $languageCode = null, $userId = null, $userName = null){

        $output = ((is_null($output)) ? new \StdClass() : $output );

        $output->userInfoResult->UserInfoResponse = ((isset($output->userInfoResult->UserInfoResponse)) ? $output->userInfoResult->UserInfoResponse :array() );

        $size = sizeof($output->userInfoResult->UserInfoResponse);

        $output->userInfoResult->UserInfoResponse[$size]['Status'] = $status;
        $output->userInfoResult->UserInfoResponse[$size]['Balance'] = $balance;
        $output->userInfoResult->UserInfoResponse[$size]['CorrelationNumber'] = $correlationNumber;
        $output->userInfoResult->UserInfoResponse[$size]['LanguageCode'] = $languageCode;
        $output->userInfoResult->UserInfoResponse[$size]['CurrencyCode'] = $currencyCode;
        $output->userInfoResult->UserInfoResponse[$size]['UserId'] = $userId;
        $output->userInfoResult->UserInfoResponse[$size]['Username'] = $userName;

        return $output;
        };

        $request = new BetradarRequest($params, "UserInfoRequest");


      

        if($request->isFault())
        {
            return $responseMaker(null, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                ? $requestParameter->CorrelationNumber : null));
        }

        $sizeOfRequest = ((is_array($params->requests->UserInfoRequest)) ? sizeof($params->requests->UserInfoRequest) : 0);

        Request:{
        ErrorList::resetErrors();
        $requestParameter = ((is_array($params->requests->UserInfoRequest)) ? $params->requests->UserInfoRequest[$index] : $params->requests->UserInfoRequest);

        if(!$request->userInfoRequest(false, $index))
        {
            $index++;

            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                    ? $requestParameter->CorrelationNumber : null));
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                    ? $requestParameter->CorrelationNumber : null));
            }
        }
        }

        $requestResponse = $this->callRequestExecutor();
        
        \application\BaseComponent::getLogger()->log('GMAPI',$requestResponse);

        $index++;
        
        if(ErrorList::$sessionExist)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$userNotFound)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$userBlocked)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$failedRequest)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$validationError)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }

        if( !is_array($requestResponse) || !isset($requestResponse['user_balance']) ||!isset($requestResponse['ccy_code']) ||
            !isset($requestResponse['uid']))
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }


        $token = explode(",", $requestParameter->Token);

        $status = "OK";
        $corrNum = $requestParameter->CorrelationNumber;
        $balance =  number_format((float)($requestResponse['user_balance'] / 100), 2, '.', '');
        $langCode = "en";
        $curCode = $requestResponse['ccy_code'];
        $userId = $requestResponse['uid'];
        $userName = ((is_array($token) && sizeof($token) >= 5) ? $token[5] : $requestResponse['uid']);

        $response = $responseMaker($response, $status, $corrNum, $balance, $curCode, $langCode, $userId, $userName);

        if($index < $sizeOfRequest)
        {
            goto Request;
        }

        
        Log::log('soap', json_encode($response), 'betradar');
        return $response;
    }


    /**
     * @param $params
     * @return null
     */
    public function queryBalance($params)
    {

        $response = null;
        $index = 0;
        $requestParameter = ((is_array($params->requests->QueryBalanceRequest)) ? $params->requests->QueryBalanceRequest[$index] : $params->requests->QueryBalanceRequest);

        $responseMaker = function($output, $status, $correlationNumber = null, $balance = null, $currencyCode = null){

            $output = ((is_null($output)) ? new \StdClass() : $output );

            $output->queryBalanceResult->GenericResponse = ((isset($output->queryBalanceResult->GenericResponse)) ? $output->queryBalanceResult->GenericResponse :array() );
            $size = sizeof($output->queryBalanceResult->GenericResponse);
            $output->queryBalanceResult->GenericResponse[$size]['Status'] = $status;
            $output->queryBalanceResult->GenericResponse[$size]['Balance'] = $balance;
            $output->queryBalanceResult->GenericResponse[$size]['CorrelationNumber'] = $correlationNumber;
            $output->queryBalanceResult->GenericResponse[$size]['CurrencyCode'] = $currencyCode;

            return $output;
        };

        $request = new BetradarRequest($params, "QueryBalanceRequest");

        if($request->isFault())
        {
            return $responseMaker(null, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                ? $requestParameter->CorrelationNumber : null));
        }

        $sizeOfRequest = ((is_array($params->requests->QueryBalanceRequest)) ? sizeof($params->requests->QueryBalanceRequest) : 0);

        Request:
        {
            ErrorList::resetErrors();
            $requestParameter = ((is_array($params->requests->QueryBalanceRequest)) ? $params->requests->QueryBalanceRequest[$index] : $params->requests->QueryBalanceRequest);

            if(!$request->queryBalanceRequest($index))
            {
                $index++;
                if($index < $sizeOfRequest)
                {
                    $response = $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                        ? $requestParameter->CorrelationNumber : null));
                    goto Request;
                }
                else
                {
                    return $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                        ? $requestParameter->CorrelationNumber : null));
                }
            }
        }

        $requestResponse = $this->callRequestExecutor();
        
        \application\BaseComponent::getLogger()->log('GMAPI',$requestResponse);

        $index++;

        if(ErrorList::$sessionExist)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$userNotFound)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$failedRequest)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }


        if(ErrorList::$validationError)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }

        if( !is_array($requestResponse) || !isset($requestResponse['user_balance']) ||!isset($requestResponse['ccy_code']) ||
            !isset($requestResponse['uid']))
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }


        $status = "OK";
        $corrNum = $requestParameter->CorrelationNumber;
        $balance = number_format((float)($requestResponse['user_balance'] / 100), 2, '.', '');
        $curCode = $requestResponse['ccy_code'];

        $response = $responseMaker($response, $status, $corrNum, $balance, $curCode);

        if($index < $sizeOfRequest)
        {
            goto Request;
        }

        Log::log('soap', json_encode($response), 'betradar');
        return $response;
    }

    /**
     * @param $params
     * @return null
     */
    public function reserveFunds($params)
    {
        $response = null;
        $index = 0;
        $requestParameter = ((is_array($params->requests->ReserveFundsRequest)) ? $params->requests->ReserveFundsRequest[$index] : $params->requests->ReserveFundsRequest);

        $responseMaker = function($output, $status, $correlationNumber = null, $balance = null, $currencyCode = null){
            $output = ((is_null($output)) ? new \stdClass() : $output);
            $output->reserveFundsResult->GenericResponse = ((isset($output->reserveFundsResult->GenericResponse)) ? $output->reserveFundsResult->GenericResponse : array());
            $size = sizeof($output->reserveFundsResult->GenericResponse);
            $output->reserveFundsResult->GenericResponse[$size]['Status'] = $status;
            $output->reserveFundsResult->GenericResponse[$size]['Balance'] = $balance;
            $output->reserveFundsResult->GenericResponse[$size]['CorrelationNumber'] = $correlationNumber;
            $output->reserveFundsResult->GenericResponse[$size]['CurrencyCode'] = $currencyCode;
            return $output;
        };

        $request = new BetradarRequest($params, "ReserveFundsRequest");

        if($request->isFault())
        {
            return $responseMaker(null, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                ? $requestParameter->CorrelationNumber : null));
        }

        Request:
        {
            ErrorList::resetErrors();
            $requestParameter = ((is_array($params->requests->ReserveFundsRequest)) ? $params->requests->ReserveFundsRequest[$index] : $params->requests->ReserveFundsRequest);

            if(!$request->reserveFundRequest($index))
            {
                $index++;
                $size = ((is_array($params->requests->ReserveFundsRequest)) ? sizeof($params->requests->ReserveFundsRequest) : 0);
                if($index < $size)
                {
                    $response = $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                    ? $requestParameter->CorrelationNumber : null));
                    goto Request;
                }
                else
                {
                    return $responseMaker(null, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                        ? $requestParameter->CorrelationNumber : null));
                }
            }
        }

        $requestResponse = $this->callRequestExecutor();


        \application\BaseComponent::getLogger()->log('GMAPI',$requestResponse);

        $index++;
        $sizeOfRequest = ((is_array($params->requests->ReserveFundsRequest)) ? sizeof($params->requests->ReserveFundsRequest) : 0);

        if(ErrorList::$sessionExist)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$insufficientFund)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(3), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(3), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$userNotFound)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(4), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(4), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$userBlocked)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(6), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(6), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$duplicateRoundId)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(7), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(7), $requestParameter->CorrelationNumber);
            }

        }

        if(ErrorList::$failedRequest)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$validationError)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }


        if( !is_array($requestResponse) || !isset($requestResponse['user_balance']) )
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }


        $status = "OK";
        $corrNum = $requestParameter->CorrelationNumber;
        $balance = number_format((float)($requestResponse['user_balance'] / 100), 2, '.', '');
        $curCode = $requestParameter->CurrencyCode;

        $response = $responseMaker($response, $status, $corrNum, $balance, $curCode);


        if($index < $sizeOfRequest)
        {
            goto Request;
        }

        Log::log('soap', json_encode($response), 'betradar');
        return $response;
    }

    /**
     * @param $params
     * @return null
     */
    public function payment($params)
    {
        $response = null;
        $index = 0;
        $requestParameter = ((is_array($params->requests->PaymentRequest)) ? $params->requests->PaymentRequest[$index] : $params->requests->PaymentRequest);

        $responseMaker = function($output, $status, $correlationNumber = null, $balance = null, $currencyCode = null){
            $output = ((is_null($output)) ? new \stdClass() : $output);
            $output->paymentResult->GenericResponse = ((isset($output->paymentResult->GenericResponse)) ? $output->paymentResult->GenericResponse : array());
            $size = sizeof($output->paymentResult->GenericResponse);
            $output->paymentResult->GenericResponse[$size]['Status'] = $status;
            $output->paymentResult->GenericResponse[$size]['Balance'] = $balance;
            $output->paymentResult->GenericResponse[$size]['CorrelationNumber'] = $correlationNumber;
            $output->paymentResult->GenericResponse[$size]['CurrencyCode'] = $currencyCode;

            return $output;
        };

        $request = new BetradarRequest($params, "PaymentRequest");

        if($request->isFault())
        {
            return $responseMaker(null, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                ? $requestParameter->CorrelationNumber : null));
        }

        $sizeOfRequest = ((is_array($params->requests->PaymentRequest)) ? sizeof($params->requests->PaymentRequest) : 0);

        Request:
        {
            ErrorList::resetErrors();
            $requestParameter = ((is_array($params->requests->PaymentRequest)) ? $params->requests->PaymentRequest[$index] : $params->requests->PaymentRequest);

            if(!$request->paymentRequest($index))
            {
                $index++;
                if($index < $sizeOfRequest)
                {
                    $response = $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                        ? $requestParameter->CorrelationNumber : null));
                    goto Request;
                }else
                {
                    return $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                        ? $requestParameter->CorrelationNumber : null));
                }
            }
        }
        $requestResponse = $this->callRequestExecutor();
        
        \application\BaseComponent::getLogger()->log('GMAPI',$requestResponse);

        $index++;

        if(ErrorList::$userNotFound)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(4), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(4), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$duplicateRoundId)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(7), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(7), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$invalidRoundId)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(8), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(8), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$failedRequest)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$validationError)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(2), $requestParameter->CorrelationNumber);
            }
        }

        if( !is_array($requestResponse) || !isset($requestResponse['user_balance']) )
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }



        $status = "OK";
        $corrNum = $requestParameter->CorrelationNumber;
        $balance = number_format((float)($requestResponse['user_balance'] / 100), 2, '.', '');
        $curCode = $requestParameter->CurrencyCode;

        $response = $responseMaker($response, $status, $corrNum, $balance, $curCode);

        if($index < $sizeOfRequest)
        {
            goto Request;
        }

        Log::log('soap', json_encode($response), 'betradar');
        return $response;
    }

    /**
     * @param $params
     * @return null
     */
    public function approve($params)
    {
        $index = 0;
        $response = null;
        $requestParameter = ((is_array($params->requests->ApproveRequest)) ? $params->requests->ApproveRequest[$index] : $params->requests->ApproveRequest);

        $responseMaker = function($output, $status, $correlationNumber = null, $balance = null, $currencyCode = null){

            if($output == null)
            {
                $output = new \stdClass();
            }

            if(!isset($output->approveResult->GenericResponse))
            {
                $output->approveResult->GenericResponse = array();
            }

            $size = sizeof($output->approveResult->GenericResponse);
            $output->approveResult->GenericResponse[$size]['Status'] = $status;
            $output->approveResult->GenericResponse[$size]['Balance'] = $balance;
            $output->approveResult->GenericResponse[$size]['CorrelationNumber'] = $correlationNumber;
            $output->approveResult->GenericResponse[$size]['CurrencyCode'] = $currencyCode;

            return $output;
        };

        $request = new BetradarRequest($params, "ApproveRequest");

        if($request->isFault())
        {
            return $responseMaker(null, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                ? $requestParameter->CorrelationNumber : null));
        }

        $sizeOfRequest = ((is_array($params->requests->ApproveRequest)) ? sizeof($params->requests->ApproveRequest) : 0);

        Request:
        {
            ErrorList::resetErrors();
            $requestParameter = ((is_array($params->requests->ApproveRequest)) ? $params->requests->ApproveRequest[$index] : $params->requests->ApproveRequest);

            if($index < $sizeOfRequest)
            {
                if(!$request->approveRequest($index))
                {
                    $index++;
                    if($index < $sizeOfRequest)
                    {
                        $response = $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                            ? $requestParameter->CorrelationNumber : null));
                        goto Request;
                    }
                    else
                    {
                        return  $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                            ? $requestParameter->CorrelationNumber : null));
                    }
                }
            }

        }


        $requestResponse = $this->callRequestExecutor();
        
        \application\BaseComponent::getLogger()->log('GMAPI',$requestResponse);

        $index++;

        if(ErrorList::$userBlocked)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(6), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(6), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$invalidRoundId)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(8), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(8), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$failedRequest)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$validationError)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(1), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(1), $requestParameter->CorrelationNumber);
            }
        }


        if( !is_array($requestResponse) || !isset($requestResponse['user_balance']) )
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }
            else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }


        $status = "OK";
        $balance = number_format(((float)($requestResponse['user_balance']) / 100), 2, '.', '');
        $currencyCode = $request::$currencyForFree;

        $response = $responseMaker($response, $status, $requestParameter->CorrelationNumber, $balance, $currencyCode);

        if($index < $sizeOfRequest)
        {
            goto Request;
        }

        Log::log('soap', json_encode($response), 'betradar');
        return $response;
    }

    /**
     * @param $params
     * @return null
     */
    public function manualPayment($params)
    {
        $index = 0;
        $response = null;
        $doWin = null;
        $requestParameter = ((is_array($params->requests->ManualPaymentRequest)) ? $params->requests->ManualPaymentRequest[$index] : $params->requests->ManualPaymentRequest);

        $responseMaker = function($output,  $status, $correlationNumber = null, $balance = null, $currencyCode = null){

            $output = ((is_null($output)) ? new \StdClass() : $output);

            if(!isset($output->manualPaymentResult->GenericResponse))
            {
                $output->manualPaymentResult->GenericResponse = array();
            }
            $size = sizeof($output->manualPaymentResult->GenericResponse);
            $output->manualPaymentResult->GenericResponse[$size]['Status'] = $status;
            $output->manualPaymentResult->GenericResponse[$size]['Balance'] = $balance;
            $output->manualPaymentResult->GenericResponse[$size]['CorrelationNumber'] = $correlationNumber;
            $output->manualPaymentResult->GenericResponse[$size]['CurrencyCode'] = $currencyCode;
            return $output;
        };

        $request = new BetradarRequest($params, "ManualPaymentRequest");


        if($request->isFault())
        {
            return $responseMaker(null, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                ? $requestParameter->CorrelationNumber : null));
        }

        $sizeOfRequest = ((is_array($params->requests->ManualPaymentRequest))  ? sizeof($params->requests->ManualPaymentRequest)  : 0);

        Request:{

        ErrorList::resetErrors();

        if($index < $sizeOfRequest)
        {
            if(!$request->manuelPaymentRequest($index, $doWin))
            {
                $index++;
                if($index < $sizeOfRequest)
                {
                    $response = $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                        ? $requestParameter->CorrelationNumber : null));
                    goto Request;
                }else
                {
                    return $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                        ? $requestParameter->CorrelationNumber : null));
                }
            }
        }
        }

        $requestResponse = $this->callRequestExecutor();
        
        \application\BaseComponent::getLogger()->log('GMAPI',$requestResponse);

        $requestParameter = ((is_array($params->requests->ManualPaymentRequest)) ? $params->requests->ManualPaymentRequest[$index] : $params->requests->ManualPaymentRequest);

        $index++;

        if(ErrorList::$userBlocked)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(6), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(6), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$duplicateRoundId)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(7), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(7), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$invalidRoundId)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(8), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(8), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$failedRequest)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$validationError)
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(1), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(1), $requestParameter->CorrelationNumber);
            }
        }

        if( !is_array($requestResponse) || !isset($requestResponse['user_balance']) )
        {
            if($index < $sizeOfRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }

        $status = "OK";
        $corrNum = $requestParameter->CorrelationNumber;
        $balance = number_format((float)($requestResponse['user_balance'] / 100), 2, '.', '');
        $curCode = $request::$currencyForFree;

        if(is_null($doWin) && ($index < $sizeOfRequest))
        {
            $index--;
            $doWin = "win";
            goto Request;
        }
        elseif(!is_null($doWin) && ($index < $sizeOfRequest))
        {
            $response = $responseMaker($response, $status, $corrNum, $balance, $curCode);
            $doWin = null;
            goto Request;
        }

        if(is_null($doWin) && ($index === $sizeOfRequest))
        {
            $index--;
            $doWin = "win";
            goto Request;
        }


        $response = $responseMaker($response, $status, $corrNum, $balance, $curCode);

        Log::log('soap', json_encode($response), 'betradar');
        return $response;

    }

    /**
     * @param $params
     * @return null
     */
    public function cancel($params)
    {
        $index = 0;
        $response = null;
        $useAmount = "none";

        $requestParameter = ((is_array($params->requests->CancelRequest)) ? $params->requests->CancelRequest[$index] : $params->requests->CancelRequest);

        $responseMaker = function($output,  $status, $correlationNumber = null, $balance = null, $currencyCode = null){

                $output = ((is_null($output)) ? new \StdClass() : $output);

            if(!isset($output->cancelResult->GenericResponse))
            {
               $output->cancelResult->GenericResponse = array();
            }
            $size = sizeof($output->cancelResult->GenericResponse);
            $output->cancelResult->GenericResponse[$size]['Status'] = $status;
            $output->cancelResult->GenericResponse[$size]['Balance'] = $balance;
            $output->cancelResult->GenericResponse[$size]['CorrelationNumber'] = $correlationNumber;
            $output->cancelResult->GenericResponse[$size]['CurrencyCode'] = $currencyCode;


            return $output;
        };

        $request = new BetradarRequest($params, "CancelRequest");


        if($request->isFault())
        {
            return $responseMaker(null, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                ? $requestParameter->CorrelationNumber : null));
        }

        $sizeOfCancelRequest = ((is_array($params->requests->CancelRequest))  ? sizeof($params->requests->CancelRequest)  : 0);


        Request:{

        ErrorList::resetErrors();

            if($index < $sizeOfCancelRequest)
            {
                if($useAmount === "none")
                {
                    $useAmount = true;
                }

                if(!$request->cancelRequest($index, $useAmount))
                {
                    $index++;
                    if($index < $sizeOfCancelRequest)
                    {
                        $response = $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                            ? $requestParameter->CorrelationNumber : null));
                        goto Request;
                    }else
                    {
                        return $responseMaker($response, $request->getFaultCode(), ((isset($requestParameter->CorrelationNumber))
                            ? $requestParameter->CorrelationNumber : null));
                    }
                }
            }

        }

        $requestResponse = $this->callRequestExecutor();


        \application\BaseComponent::getLogger()->log('GMAPI',$requestResponse);

        $index++;

        if(ErrorList::$duplicateRoundId)
        {
            if($index < $sizeOfCancelRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(7), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(7), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$userBlocked)
        {
            if($index < $sizeOfCancelRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(6), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(6), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$invalidRoundId)
        {
            if($index < $sizeOfCancelRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(8), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(8), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$cancelNotPossible)
        {
            if($index < $sizeOfCancelRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(10), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(10), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$failedRequest)
        {
            if($index < $sizeOfCancelRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }

        if(ErrorList::$validationError)
        {
            if($index < $sizeOfCancelRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(1), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(1), $requestParameter->CorrelationNumber);
            }
        }

        if( !is_array($requestResponse) || !isset($requestResponse['user_balance']) )
        {
            if($index < $sizeOfCancelRequest)
            {
                $response = $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
                goto Request;
            }else
            {
                return $responseMaker($response, $request->getStatusCodes(12), $requestParameter->CorrelationNumber);
            }
        }


        $status = "OK";
        $corrNum = $requestParameter->CorrelationNumber;
        $balance = number_format((float)($requestResponse['user_balance'] / 100), 2, '.', '');
        $curCode = null;//$requestResponse['currency_code'];



        if($useAmount === true)
        {
            $index = $index - 1;
            $useAmount = false;
             goto Request;
        }
        elseif($useAmount === false && $index < $sizeOfCancelRequest)
        {
            $response = $responseMaker($response, $status, $corrNum, $balance, $curCode);
            $useAmount = "none";
            goto Request;
        }

        $response = $responseMaker($response, $status, $corrNum, $balance, $curCode);

        Log::log('soap', json_encode($response), 'betradar');

        return $response;
    }

    
}
