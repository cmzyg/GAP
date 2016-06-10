<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 23/12/14
 * Time: 15:27
 */

namespace controller;

use application\BaseController;
use \component\soap\Soap;
use \component\logger\v1\Logger as Log;

class soapController extends BaseController {

    public function index()
    {
    }

    public function service()
    {
            //log Request
            Log::log('soap', array('input'=>file_get_contents("php://input")),'betradar');

            $soap = new Soap();
            $soap = $soap->loadComponent();
            $soap->loadSoapServer();
    }

}
