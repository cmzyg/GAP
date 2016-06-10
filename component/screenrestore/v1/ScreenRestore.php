<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 30/10/14
 * Time: 15:24
 */

namespace component\screenrestore\v1;


use component\request\LoadScreenRequest;
use component\request\Request;
use component\request\SaveScreenRequest;
use component\screenrestore\ScreeRestoreInterface;
use exceptions\SaveScreenException;
use model\GMAPIDataRepository;

class ScreenRestore extends \component\screenrestore\ScreenRestore implements ScreeRestoreInterface {

    public function loadScreenRestore()
    {

        $data = new GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix().'_mdpadmin');
        $result = $data->fetchSaveScreenData($this->request->getUserId(), $this->request->getSkinId());

        if($result === false)
        {
            self::getLogger()->addException(new SaveScreenException("Problem with getting an info from save screen table.", 2, 0));
            $this->processResponse = array("error" => "Problem with getting an info from save screen table.");
        }
        elseif ((is_array($result) && sizeof($result) == 0) || $result === null )
        {
            $updateOutput = $data->insertSaveScreenData($this->request->getUserId(), $this->request->getSkinId());
            if($updateOutput === false)
            {
                self::getLogger()->addException(new SaveScreenException("Problem with getting an info from save screen table.", 1, 1));
                $this->processResponse = array("error" => "Problem with inserting into save screen table.");
            }

            $this->processResponse = array();
            $this->processResponse['status']     = 'success';
            $this->processResponse['state']      = (int)0;
            $this->processResponse['connected']  = (int)0;
            $this->processResponse['ssstring']   = "-";
            $this->processResponse['gmstring']   = "-";
            $this->processStatus = true;
        }
        else
        {
            $result['gmstring'] = ((is_null($result['gmstring']) || $result['gmstring'] == '') ? '-' : $result['gmstring']);
            if($result['connected'] != 1)
            {
                $updateOutput = $data->updateSaveScreenData($this->request->getUserId(), $this->request->getSkinId());
                if($updateOutput === false)
                {
                    self::getLogger()->addException(new SaveScreenException("Problem with updating save screen table.", 1, 1));
                    $this->processResponse = array("error" => "Problem with updating save screen table.");
                }
            }

            $this->processResponse = array();
            $this->processResponse['status']     = 'success';
            $this->processResponse['state']      = (int)$result['state'];
            $this->processResponse['connected']  = (int)$result['connected'];
            $this->processResponse['ssstring']   = $result['ssstring'];
            $this->processResponse['gmstring']   = $result['gmstring'];
            $this->processStatus = true;
        }

    }

    public function saveScreenRestore()
    {
        $data = new GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix().'_mdpadmin');
        $updateOutput = $data->updateSaveScreenDataWithData(
            $this->request->getStatus(),
            $this->request->getDescription(),
            $this->request->getAi(),
            $this->request->getGmai(),
            $this->request->getUserId(),
            $this->request->getSkinId());

        if($updateOutput === false)
        {
            self::getLogger()->addException(new SaveScreenException("Problem with updating save screen table.", 2, 3));
            $this->processResponse = array("error" => "Problem with updating savescreen table.");
        }
        else
        {
            $result = $data->fetchRows();
            if ((is_array($result) && sizeof($result) == 0) || $result === null )
            {
                self::getLogger()->addException(new SaveScreenException("Problem with updating save screen table. No recordes for the current session.", 3, 3));
                $this->processResponse = array("error" => "Problem with updating savescreen table. No recordes for the current session.");
            }
            else
            {
                $this->processResponse = array();
                $this->processResponse['status']     = 'success';
                $this->processResponse['result']     = 'OK';
                $this->processStatus = true;
            }
        }
    }

    public function execute(Request $request)
    {
        $this->request = $request;

        if($request instanceof LoadScreenRequest)
        {
            $this->loadScreenRestore();
        }
        elseif($request instanceof SaveScreenRequest)
        {
            $this->saveScreenRestore();
        }
        else
        {
            $this->processResponse = array("error" => $this->NotSupported());
        }
    }
} 