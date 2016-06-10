<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 30/09/14
 * Time: 09:52
 */

namespace controller;


use application\BaseController;
use component\communication\Communication;

class comtestController extends BaseController{


    public function index()
    {
        $com = new Communication();
        $com = $com->loadComponent();
        $com->receiveGetRequest();
        $com::getLogger()->getExceptionMessages($com->replyResponse());
        echo $com->replyResponse();
    }
} 