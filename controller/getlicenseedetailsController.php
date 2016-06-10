<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 15/10/14
 * Time: 14:56
 */

namespace controller;


use application\BaseController;
use component\communication\Communication;

class getlicenseedetailsController extends BaseController{

    public function index()
    {
        $_REQUEST['method_name'] = "get_licensee_details";
        $com = new Communication();
        $com = $com->loadComponent();
        $com->receiveGetRequest();
        echo $com->replyResponse();
    }
} 