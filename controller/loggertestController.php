<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 30/09/14
 * Time: 09:52
 */

namespace controller;


use application\BaseController;
use component\logger\Logger;
use component\testcomponent\TestComponent;

class loggertestController extends BaseController{


    public function index()
    {
       /* $log = new Logger();
        $log = $log->loadComponent();
        $log->addMySQLError('There is no Database Connection You silly!', 'SELECT * FROM your_own_brain');
        $log->addException(new \Exception("First Exception, I should have MySQL Error",1));
        $log->addException(new \exceptions\ProgressiveException("Second Exception, I shouldn't have MySQL Error but should be FreeRound Exception",2,2));
        $log->addMySQLError('There is no Database Connection You silly! AGAIN?!', 'SELECT * FROM your_own_brain');
        $log->addException(new \Exception("Third Exception, I shoul have MySQL Error also",3));
        
        $log->getExceptionMessages(array('status'=>'error','response'=>'SO MANY EXCEPTIONS!'));*/
        $com = new TestComponent();
        $com = $com->loadComponent();
        $com->testEx();
        
        //ae;ffff23qwfkehndsf]pi24h
        
        echo "End of the test";
    }
} 