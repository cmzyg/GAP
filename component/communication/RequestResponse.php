<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 29/09/14
 * Time: 14:15
 */

namespace component\communication;


class RequestResponse implements RequestResponseInterface{

    private $errorType = "CURL/SOAP";
    private $errorMessage;
    private $requestResponse;
    private $error = false;
    private $requestHeader;
    private $statusCode = 0;
    private $httpCode = 0;
    private $nonParsedResponse;
    private $url;

    public function isRequestSuccessful()
    {
        return (!($this->error));
    }

    public function getRequestResponse()
    {
       return $this->requestResponse;
    }

    public function getRequestError()
    {
        return $this->errorMessage;
    }

    public function getRequestErrorType()
    {
        return $this->errorType;
    }

    /**
     * @param boolean $error
     */
    public function setError($error)
    {
            $this->error = $error;
    }

    /**
     * @param mixed $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @param string $errorType
     */
    public function setErrorType($errorType)
    {
        $this->errorType = $errorType;
    }

    /**
     * @param mixed $requestResponse
     */
    public function setRequestResponse($requestResponse)
    {
        $this->requestResponse = $requestResponse;
    }

    /**
     * @param mixed $requestHeader
     */
    public function setRequestHeader($requestHeader)
    {
        $this->requestHeader = $requestHeader;
    }

    /**
     * @return mixed
     */
    public function getRequestHeader()
    {
        return $this->requestHeader;
    }

    /**
     * @param int $httpCode
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $nonParsedResponse
     */
    public function setNonParsedResponse($nonParsedResponse)
    {
        $this->nonParsedResponse = $nonParsedResponse;
    }

    /**
     * @return mixed
     */
    public function getNonParsedResponse()
    {
        return $this->nonParsedResponse;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }



} 