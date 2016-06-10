<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 29/09/14
 * Time: 17:25
 */

namespace component\communication;


class OutGoingRequest {

    private $url;
    private $data;
    private $requestType;
    private $sendPostOnLink = false;

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
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

    /**
     * @param mixed $requestType
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;
    }

    /**
     * @return mixed
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * @param mixed $sendPostOnLink
     */
    public function setSendPostOnLink($sendPostOnLink)
    {
        $this->sendPostOnLink = $sendPostOnLink;
    }

    /**
     * @return mixed
     */
    public function getSendPostOnLink()
    {
        return $this->sendPostOnLink;
    }





} 