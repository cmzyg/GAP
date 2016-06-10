<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/10/14
 * Time: 12:05
 */

namespace controller;


use application\BaseController;
use component\configurationmanager\ConfigurationManager;

class reloadController extends BaseController{

    public function index()
    {
        $conf = new ConfigurationManager();
        $mng = $conf->loadComponent();
        $licenseeId = $_GET['lid'];
        if(intval($licenseeId) > 0)
        {
            $out = $mng->reloadConfiguration($licenseeId);
        }

        echo $mng->getLoadConfigErrorMessage();
    }
} 