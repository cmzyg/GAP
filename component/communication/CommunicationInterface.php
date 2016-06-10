<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 23/09/14
 * Time: 14:18
 */

namespace component\communication;


interface CommunicationInterface {

    /**
     * Receive Get request from client
     * @return mixed
     */
    public function receiveGetRequest();

    /**
     * Receive Post request from client
     * @return mixed
     */
    public function receivePostRequest();

    /**
     * Send a Get request to a given host
     * @return mixed
     */
    public function sendGetRequest();

    /**
     * Send a Post request to a given host
     * @return mixed
     */
    public function sendPostRequest();

    /**
     * Send a Soap request to a given host
     * @return mixed
     */
    public function sendSoapRequest();

    /**
     * Sends a reply to a client
     * @return mixed
     */
    public function replyResponse();

} 