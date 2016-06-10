<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 29/09/14
 * Time: 12:29
 */

namespace component\communication;


interface RequestResponseInterface {

    public function isRequestSuccessful();

    public function getRequestResponse();

    public function getRequestError();

    public function getRequestErrorType();
} 