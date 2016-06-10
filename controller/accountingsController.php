<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 30/09/14
 * Time: 09:52
 */

namespace controller;


use application\BaseController;
use component\accounting\Accounting;
use component\accounting\v1\entities\AcccountingPlayers;
use component\accounting\v1\entities\AccountingProgressive;
use component\accounting\v1\entities\AccountingWagers;
use component\accounting\v1\entities\AccountingGames;

class accountingsController extends BaseController{


    public function index()
    {
        $acc = new Accounting();
        $acc = $acc->getCurrentVersion();
        
        $acp = new AcccountingPlayers('dummyapi',9);
        
        $acp->setBetsSum(999);
        
        var_dump($acp->getBetsSum());
        
        $acp->update();
//        
//        
//        $acg = new AccountingGames('dummyapi',720);
//        
//        var_dump($acg->getTotalBetsCash());
//        
//        $acw = new AccountingWagers('dummyapi',58);
//        
//        var_dump($acw->getAccountingPlayersId());
//        
//        $acpr = new AccountingProgressive('dummyapi',22);
//        
//        var_dump($acpr->getJackpotValues());
        
        
    }
} 