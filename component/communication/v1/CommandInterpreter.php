<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 13/10/14
 * Time: 14:31
 */

namespace component\communication\v1;



use application\BaseComponent;
use component\externalgetters\v1\ExternalGetters;
use component\progressive\Progressive;
use component\request\GetLicenseeDetailsRequest;
use component\request\GetMethodsListRequest;
use component\request\ProgressiveRequest;
use component\request\Request;
use component\request\SaveScreenRelatedRequest;
use component\request\WalletRequest;
use component\screenrestore\ScreenRestore;
use component\wallet\Wallet;

/**
 * Class CommandInterpreter
 * @package component\communication\v1
 * @author Samuel I Amaziro
 */
class CommandInterpreter {
    /**
     * @var \component\request\Request
     */
    private $request;
    /**
     * @var string
     */
    private $output;
    /**
     * @var \component\communication\Communication
     */
    private $communicationComponent;
    /**
     * @var bool
     */
    private $showErrors = false;
    /**
     * @var bool
     */
    private $status = false;



    public function __construct(Request $request, $communication)
    {
        $this->request = $request;
        $this->communicationComponent = $communication;
        $this->interpreter();
    }

    /**
     * Interprets a given request
     */
    public function interpreter()
    {

        if($this->request->getPp() == "fun")
        {
            $w = new Wallet();
            $w->setCurrentVersion(strtolower("fun"));
            // Load Wallet type
            $wallet = $w->loadComponent();

            // Load Wallet version
            $wallet = $wallet->loadComponent();
            $wallet->executeProcess($this->request, $this->communicationComponent);
            $this->output = $wallet->getProcessResponse();
            $this->showErrors = $wallet->getShowProcessError();
            $this->status = $wallet->isProcessSuccessful();
        }
        elseif($this->request->getMode() == "tournament")
        {
            $w = new Wallet();
            $w->setCurrentVersion(strtolower("tournament"));

            // Load Wallet type
            $wallet = $w->loadComponent();

            // Load Wallet version
            $wallet = $wallet->loadComponent();
            $wallet->executeProcess($this->request, $this->communicationComponent);
            $this->output = $wallet->getProcessResponse();
            $this->showErrors = $wallet->getShowProcessError();
            $this->status = $wallet->isProcessSuccessful();
        }
        elseif($this->request instanceof WalletRequest)
        {
            $w = new Wallet();
            $walletName = $this->request->getLicenseeObject()->getGmapiConfiguration()->getWallet()->getName();
            $w->setCurrentVersion(strtolower($walletName));
            // Load Wallet Type
            $wallet = $w->loadComponent();
            // Load wallet version

            $wallet->setCurrentVersion($this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getVersion());
            BaseComponent::getLogger()->log('test', $this->request->getLicenseeObject()->getGmapiConfiguration()->getWalletAuthorisation()->getVersion());
            $wallet = $wallet->loadComponent();
            $wallet->executeProcess($this->request, $this->communicationComponent);
            $this->output = $wallet->getProcessResponse();
            $this->showErrors = $wallet->getShowProcessError();
            $this->status = $wallet->isProcessSuccessful();

        }
        elseif($this->request instanceof SaveScreenRelatedRequest)
        {
            $screenRestore = new ScreenRestore();
            $sr = $screenRestore->loadComponent();
            $sr->execute($this->request);
            $this->output = $sr->getProcessResponse();
            $this->status = $sr->isProcessSuccessful();
        }
        elseif($this->request instanceof ProgressiveRequest)
        {
            $progressive = new Progressive();
            $pr = $progressive->loadComponent();
            $pr->execute($this->request);

            $this->output = $pr->getProcessResponse();
            $this->status = $pr->isProcessSuccessful();
        }
        elseif($this->request instanceof GetLicenseeDetailsRequest || $this->request instanceof GetMethodsListRequest)
        {
            $eg = new ExternalGetters();
            $eg = $eg->loadComponent();
            $eg->execute($this->request);

            $this->output = $eg->getProcessResponse();
            $this->status = $eg->isProcessSuccessful();
        }
        else
        {
            $this->output = array("error" => "call to unknown method");
            $this->showErrors = false;
            $this->status = false;
        }
    }


    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return boolean
     */
    public function getShowErrors()
    {
        return $this->showErrors;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }



} 