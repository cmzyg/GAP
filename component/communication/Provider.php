<?php
/**
 * Created by PhpStorm.
 * User: anna.rabiej
 * Date: 30/12/2014
 * Time: 14:17
 */
namespace component\communication;
use Doctrine\ORM\EntityManager;
use exceptions\SoapException;
use model\gmapi\GmapiProviders;
use model\gmapi\GmapiProvidersIp;
use \component\logger\v1\Logger as Log;

class Provider {

    private $providerId;
    private $entityManager;
    private $providerName;
    private $providerIp;
    private $providerAPItype;
    private $providerDirectory;
    private $providerWebService;
    /**
     * @var null|\model\gmapi\GmapiProvidersIp
     */
    private $gmapiProvidersIp;
    /**
    * @var null|\model\gmapi\GmapiProviders
    */
    private $gmapiProviders;

    public function __construct(){
        $this->entityManager = \component\EntityManager::createEntityManager();
        $this->setProviderIp();
        $this->setProvider();
    }

    protected function setProviderIp()
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $this->providerIp = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->providerIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $this->providerIp = $_SERVER['REMOTE_ADDR'];
        }
    }

    protected function setProvider(){

        $this->gmapiProvidersIp = $this->entityManager->getRepository("model\gmapi\GmapiProvidersIp")->findOneByIp($this->providerIp);

        if ($this->gmapiProvidersIp) {
            $this->setProviderId(($this->gmapiProvidersIp->getIdProvider()->getId()));

            $this->setProviderName($this->gmapiProvidersIp->getIdProvider()->getProviderName());

            $this->setProviderApiType(($this->gmapiProvidersIp->getIdProvider()->getProviderApiType()));

            $this->setProviderDirectory();

            $this->setProviderWebService();
        }else{
            Log::log('ProviderProblem', "Unknown provider - IP has not been recognized - ".$this->providerIp , 'betradar');
            die("Unknown provider - IP has not been recognized");
        }
    }

    protected function setProviderId($id){
       $this->providerId = $id;
    }

    public function getProviderId(){
        return $this->providerId;
    }

    protected function setProviderName($name){
       $this->providerName = $name;
    }

    public function getProviderName(){
        return $this->providerName;
    }

    protected function setProviderApiType($type){
        $this->providerAPItype = $type;
    }

    public function getProviderApiType(){
        return $this->providerAPItype;
    }

    protected function setProviderDirectory(){
        $this->providerDirectory = '\component\\'.$this->providerAPItype.'\v1\\'.ucfirst($this->providerName);
    }

    public function getProviderDirectory(){
        return $this->providerDirectory;
    }


    protected function setProviderWebService()
    {
        try
        {
            if ($this->providerAPItype == 'soap')
            {
                if(file_exists(_SITE_PATH .  "config".DIRECTORY_SEPARATOR.$this->providerName.".wsdl.xml"))
                {
                    $this->providerWebService = _SITE_PATH .  "config".DIRECTORY_SEPARATOR.$this->providerName.".wsdl.xml";
                }
                elseif(file_exists(_SITE_PATH .  "config".DIRECTORY_SEPARATOR.$this->providerName.".wsdl"))
                {
                    $this->providerWebService = _SITE_PATH .  "config".DIRECTORY_SEPARATOR.$this->providerName.".wsdl";
                }
                else
                {
                    throw new SoapException("Could not find WSDL for provider in".
                        _SITE_PATH .  "config".DIRECTORY_SEPARATOR.$this->providerName.".wsdl.xml or in ".
                        _SITE_PATH .  "config".DIRECTORY_SEPARATOR.$this->providerName.".wsdl path specified ");
                }
            }
            else{
                $this->providerWebService = null;
            }
        }catch (\Exception $ex)
        {
            \component\logger\Logger::getLogger()->addException($ex);
        }

    }

    public function getProviderWebService(){
        return $this->providerWebService;
    }


}