<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 23/09/14
 * Time: 13:03
 */

namespace controller;


use application\BaseController;
use component\communication\Communication;

class pagenotfoundController extends BaseController{

    public function index()
    {
        $com = new Communication();
        $com = $com->loadComponent();
        $com->receiveGetRequest();
        echo $com->replyResponse();
    }

} 