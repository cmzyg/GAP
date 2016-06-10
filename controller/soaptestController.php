<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 23/12/14
 * Time: 15:27
 */

namespace controller;

use application\BaseController;
use component\communication\Communication;
use \component\logger\v1\Logger as Log;

class soaptestController extends BaseController {

    public function index()
    {
        $method_name = "blogin";
        $gid = "2091";
        $lid = "101";
        $sid = "1507";
        $cid = "55";
        $rid = "127";
        $operator = "0";
        $hinfo = "b55a6cdde1de59a7060acf6cbb7b1c9bc920e507d6fbc1c128407577e167e5fafbe8470f0cd299449e9512f99fa03af6ad57d1e6a55df637b36990a0137fb18f";
        $lp = "unknown";
        $pp = $_GET['token'].",,EUR,GB,real";
        $free_spin = "0";

        $_REQUEST = array('lid'=>$lid,'cid'=>$cid,'rid'=>$rid,'gid'=>$gid,'sid'=>$sid,'hinfo'=>$hinfo,'method_name'=>$method_name,'lp'=>$lp,'pp'=>$pp,'operator'=>$operator,'free_spin'=>$free_spin);

        $com = new Communication();
        $com = $com->loadComponent();
        
        $com->receiveGetRequest();

        $gmapiResponse = $com->getIncomingRequestResponse();
        
        print $gmapiResponse;
        
        
    }


}
