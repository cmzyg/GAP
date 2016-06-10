<?php

/**
 * Description of Soap
 *
 * @author rafal
 */

namespace component\soap\v1;

use \component\communication\Provider;
use \SoapServer;

class Soap extends \component\soap\Soap {


    public function __construct()
    {
        parent::__construct();
    }


    public function loadSoapServer()
    {
        
        
        $this->getLogger()->setSoapRequest(file_get_contents('php://input'));
        $provider = new Provider();
        
        $server = new SoapServer($provider->getProviderWebService());
        $server->setClass($this->loadProviderObject($provider->getProviderName()));
        ob_start();
        $server->handle();
        $gapResponse = ob_get_clean();
        $this->getLogger()->setSoapResponse($gapResponse);
        print $gapResponse;
    }

    public function loadProviderObject($name)
    {
        $class = __NAMESPACE__."\\".ucfirst($name);
        return  $class;
    }

}
