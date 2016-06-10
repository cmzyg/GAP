<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 27/10/14
 * Time: 11:49
 */

namespace controller;


use application\BaseController;
use component\communication\Communication;

class progreadcurrController extends BaseController{

    public function index()
    {
        $_REQUEST['method_name'] = "progreadcurr";
        $com = new Communication();
        $com = $com->loadComponent();
        $com->receiveGetRequest();
        echo $com->replyResponse();
    }
} 