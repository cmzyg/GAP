<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 15/10/14
 * Time: 15:00
 */

namespace controller;


use application\BaseController;
use component\communication\Communication;

class getmethodslistController  extends BaseController{

    public function index()
    {
        $_REQUEST['method_name'] = "get_methods_list";
        $com = new Communication();
        $com = $com->loadComponent();
        $com->receiveGetRequest();
        echo $com->replyResponse();
    }
} 