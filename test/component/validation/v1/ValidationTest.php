<?php 

namespace test\component\validation\v1;


use component\request\Request;
use component\validation\v1\Validation;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    protected $hash1;

    protected $hash2;

    protected function setUp()
    {
        date_default_timezone_set('GMT');

        $datatime   = new \DateTime();
        $hash = "51+c0172ea66506f59c8c435eb66176fb67+2090+1510+6+";

        $hour        = $datatime->format("G");
        $minutes     = $datatime->format("i");
        $day_of_year = $datatime->format("z") + 1;

        $day_in_utc1 = $day_of_year;
        $day_in_utc2 = $day_of_year;

        if ($hour == "23" && $minutes > "49") $day_in_utc2 = $day_in_utc1 + 1;
        if ($hour == "00" && $minutes < "11") $day_in_utc2 = $day_in_utc1 - 1;

        $this->hash1 = hash("sha512", ($hash . $day_in_utc1));
        $this->hash2 = hash("sha512", ($hash . $day_in_utc2));
    }

    /**
     * @dataProvider bloginRequestDataProvider
     */
    public function testBloginValidation($lid,$cid,$rid,$gid,$sid,$hinfo,$operator,$method_name,$lp,$pp)
    {
        $_REQUEST = array('lid'=>$lid,'cid'=>$cid,'rid'=>$rid,'gid'=>$gid,'sid'=>$sid,'hinfo'=>$hinfo,'method_name'=>$method_name,'lp'=>$lp,'pp'=>$pp,'operator'=>$operator,);

        $request = new Request();
        $validation = new Validation();
        $return = $validation->validateRequest($request);

        if($return)
        {
            $this->assertInstanceOf("component\\request\\BloginBetRequest", $validation->getValidatedRequest(), "Failed to return a valid request after validation");
        }
        else
        {
            $this->assertNull($validation->getValidatedRequest(), "Failed to return null on validation failure");
        }
    }

    public function bloginRequestDataProvider()
    {
        return array(
            array(
                'lid'=>'51','cid'=>'6','rid'=>'2','gid'=>'2010','sid'=>'1510','hinfo'=> $this->hash1,'operator'=>'0','method_name'=>'blogin',
                'lp'=>'123','pp'=>'qwerwqer,123,EUR,UK,test',
            ),
        );
    }

    /**
     * @dataProvider endRequestDataProvider
     */
    public function testEndBetValidation($lid,$cid,$rid,$gid,$sid,$hinfo,$operator,$method_name,$lp,$pp,$uid,$sesid)
    {
        $_REQUEST = array('lid'=>$lid,'cid'=>$cid,'rid'=>$rid,'gid'=>$gid,'sid'=>$sid,'hinfo'=>$hinfo,'method_name'=>$method_name,'lp'=>$lp,'pp'=>$pp,'operator'=>$operator,
            'uid'=>$uid,'sesid'=>$sesid,
        );

        $request = new Request();
        $validation = new Validation();
        $return = $validation->validateRequest($request);

        if($return)
        {
            $this->assertInstanceOf("component\\request\\EndBetRequest", $validation->getValidatedRequest(), "Failed to return a valid request after validation");
        }
        else
        {
            $this->assertNull($validation->getValidatedRequest(), "Failed to return null on validation failure");
        }
    }

    public function endRequestDataProvider()
    {
        return array(
            array(
                'lid'=>'51','cid'=>'6','rid'=>'2','gid'=>'2010','sid'=>'1510','hinfo'=> $this->hash1,'operator'=>'0','method_name'=>'end',
                'lp'=>'123','pp'=>'qwerwqer,123,EUR,UK,test','uid'=>'123','sesid'=>'aTXT0U6qnMPtgB9y8fNiRmysH/RPkM0Y7qaRryCRIRwRxutsH++9f3jfX2RDHET/PaKpiiefvQ==',
            ),
        );
    }

    /**
     * @dataProvider placeAndSettleRequestDataProvider
     */
    public function testPlaceAndSettleBetValidation($lid,$cid,$rid,$gid,$sid,$hinfo,$operator,$method_name,$lp,$pp,$uid,$sesid,$wager,$win,$free_spin,$bet_tx,
                                $win_tx,$round_id,$prog_bet,$prog_id1,$prog_id2,$jpw,$cv,$desc,$fround_id,$lines,$line_bet,$fround_provider)
    {
        $_REQUEST = array(
            'lid'=>$lid,'cid'=>$cid,'rid'=>$rid,'gid'=>$gid,'sid'=>$sid,'hinfo'=>$hinfo,'method_name'=>$method_name,'lp'=>$lp,'pp'=>$pp,'operator'=>$operator,
            'uid'=>$uid,'sesid'=>$sesid,'wager'=>$wager,'win'=>$win,'free_spin'=>$free_spin,'bet_tx'=>$bet_tx,'win_tx'=>$win_tx,'round_id'=>$round_id,'prog_bet'=>$prog_bet,
            'prog_id1'=>$prog_id1,'prog_id2'=>$prog_id2,'jpw'=>$jpw,'cv'=>$cv,'desc'=>$desc,'fround_id'=>$fround_id,'lines'=>$lines,'line_bet'=>$line_bet,'fround_provider'=>$fround_provider,
        );

        $request = new Request();
        $validation = new Validation();
        $return = $validation->validateRequest($request);

        if($return)
        {
            $this->assertInstanceOf("component\\request\\EndBetRequest", $validation->getValidatedRequest(), "Failed to return a valid request after validation");
        }
        else
        {
            $this->assertNull($validation->getValidatedRequest(), "Failed to return null on validation failure");
        }
    }

    public function placeAndSettleRequestDataProvider()
    {
        return array(
            array(
                'lid'=>'51','cid'=>'6','rid'=>'2','gid'=>'2010','sid'=>'1510','hinfo'=> $this->hash1,'operator'=>'0','method_name'=>'betwin',
                'lp'=>'123','pp'=>'qwerwqer,123,EUR,UK,test','uid'=>'123','sesid'=>'aTXT0U6qnMPtgB9y8fNiRmysH/RPkM0Y7qaRryCRIRwRxutsH++9f3jfX2RDHET/PaKpiiefvQ==',

                'wager'=>'100.23','win'=>'123.123','free_spin'=>'5','bet_tx'=>'qeqwe123123','win_tx'=>'qweqwer123123','round_id'=>'12312qwe','prog_bet'=>'qwe',
                'prog_id1'=>'eqweq','prog_id2'=>'adsa','jpw'=>'qweqe','cv'=>'123','desc'=>'ewqeq','fround_id'=>'0','lines'=>'10','line_bet'=>'5','fround_provider'=>'0',
            ),
        );
    }

    /**
     * Wallet call: BetWin
     */
    // public function testPlaceAndSettleBetRequest()
    // {
    //     $_REQUEST = array('lid'=>'51','cid'=>'6','rid'=>'2','gid'=>'2010','sid'=>'1510','hinfo'=>HASH1,'method_name'=>'end',
    //         'uid'=>123,'sesid'=>'aTXT0U6qnMPtgB9y8fNiRmysH/RPkM0Y7qaRryCRIRwRxutsH++9f3jfX2RDHET/PaKpiiefvQ==','wager'=>'100.23',
    //         'win'=>'123.123','free_spin'=>'5','bet_tx'=>'qeqwe123123','win_tx'=>'qweqwer123123','round_id'=>'12312qwe','prog_bet'=>'qwe',
    //         'prog_id1'=>'eqweq','prog_id2'=>'adsa','jpw'=>'qweqe','cv'=>'123','desc'=>'ewqeq','fround_id'=>0,'lines'=>10,'line_bet'=>5,'fround_provider'=>0,
    //     );

    //     $request = new \component\request\PlaceAndSettleBetRequest();

    //     $this->assertNotNull($request->getAllRequest());
    //     $this->assertNotNull($request->getLicenseeId());
    //     $this->assertNotNull($request->getConfigurationId());
    //     $this->assertNotNull($request->getRegulationId());
    //     $this->assertNotNull($request->getGameId());
    //     $this->assertNotNull($request->getSkinId());
    //     $this->assertNotNull($request->getHashInformation());
    //     $this->assertNotNull($request->getMethodName());

    //     $this->assertNotNull($request->getUserId());
    //     $this->assertNotNull($request->getSessionId());
    //     $this->assertNotNull($request->getBetAmount());
    //     $this->assertNotNull($request->getWinAmount());
    //     $this->assertNotNull($request->getFreeSpin());
    //     $this->assertNotNull($request->getBetTransactionId());
    //     $this->assertNotNull($request->getWinTransactionId());
    //     $this->assertNotNull($request->getRoundId());
    //     $this->assertNotNull($request->getProgressiveBet());
    //     $this->assertNotNull($request->getProgressiveId1());
    //     $this->assertNotNull($request->getProgressiveId2());
    //     $this->assertNotNull($request->getJackpotWin());
    //     $this->assertNotNull($request->getCoinValue());
    //     $this->assertNotNull($request->getDescription());
    //     $this->assertNotNull($request->getFreeRoundId());
    //     $this->assertNotNull($request->getLines());
    //     $this->assertNotNull($request->getLineBet());
    //     $this->assertNotNull($request->getFreeRoundProvider());
    // }




    /**
     * @dataProvider placeBetRequestDataProvider
     */
    public function testPlaceBetValidation($lid,$cid,$rid,$gid,$sid,$hinfo,$operator,$method_name,$lp,$pp,$uid,$sesid,$wager,$ai,$fround_id,$lines,$line_bet,$fround_provider)
    {
        $_REQUEST = array('lid'=>$lid,'cid'=>$cid,'rid'=>$rid,'gid'=>$gid,'sid'=>$sid,'hinfo'=>$hinfo,'method_name'=>$method_name,
            'uid'=>$uid,'sesid'=>$sesid,'wager'=>$wager,'lp'=>$lp,'pp'=>$pp,'operator'=>$operator,
            'ai'=>$ai,'fround_id'=>$fround_id,'lines'=>$lines,'line_bet'=>$line_bet,'fround_provider'=>$fround_provider,
        );

        $request = new Request();
        $validation = new Validation();
        $return = $validation->validateRequest($request);

        if($return)
        {
            $this->assertInstanceOf("component\\request\\PlaceBetRequest", $validation->getValidatedRequest(), "Failed to return a valid request after validation");
        }
        else
        {
            $this->assertNull($validation->getValidatedRequest(), "Failed to return null on validation failure");
        }

    }

    public function placeBetRequestDataProvider()
    {
        return array(
            array('lid'=>'51','cid'=>'6','rid'=>'2','gid'=>'2090','sid'=>'1510','hinfo'=>$this->hash1."GH",'operator'=>'0','method_name'=>'pbet','lp'=>'123','pp'=>'qwerwqer,123,EUR,UK,test',
                'uid'=>'123','sesid'=>'aTXT0U6qnMPtgB9y8fNiRmysH/RPkM0Y7qaRryCRIRwRxutsH++9f3jfX2RDHET/PaKpiiefvQ==','wager'=>'100',
                'ai'=>'1581315164,234242134,31231,102,103,50','fround_id'=>'0','lines'=>'10','line_bet'=>'5','fround_provider'=>'0',
            ),
        );
    }

    /**
     * @dataProvider settleBetRequestDataProvider
     */
    public function testSettleBetValidation($lid,$cid,$rid,$gid,$sid,$hinfo,$operator,$method_name,$lp,$pp,$uid,$sesid,$win,$free_spin,$ai,$desc,$fround_id,$lines,$line_bet,$fround_provider)
    {
        $_REQUEST = array('lid'=>$lid,'cid'=>$cid,'rid'=>$rid,'gid'=>$gid,'sid'=>$sid,'hinfo'=>$hinfo,'method_name'=>$method_name,
            'uid'=>$uid,'sesid'=>$sesid,'win'=>$win,'lp'=>$lp,'pp'=>$pp,'operator'=>$operator,'free_spin'=>$free_spin,'desc'=>$desc,
            'ai'=>$ai,'fround_id'=>$fround_id,'lines'=>$lines,'line_bet'=>$line_bet,'fround_provider'=>$fround_provider,
        );

        $request = new Request();
        $validation = new Validation();
        $return = $validation->validateRequest($request);

        if($return)
        {
            $this->assertInstanceOf("component\\request\\SettleBetRequest", $validation->getValidatedRequest(), "Failed to return a valid request after validation");
        }
        else
        {
            $this->assertNull($validation->getValidatedRequest(), "Failed to return null on validation failure");
        }
    }

    public function settleBetRequestDataProvider()
    {
        return array(
            array('lid'=>'51','cid'=>'6','rid'=>'2','gid'=>'2090','sid'=>'1510','hinfo'=>$this->hash1,'operator'=>'0','method_name'=>'sbet','lp'=>'123','pp'=>'qwerwqer,123,EUR,UK,test',
                'uid'=>'123','sesid'=>'aTXT0U6qnMPtgB9y8fNiRmysH/RPkM0Y7qaRryCRIRwRxutsH++9f3jfX2RDHET/PaKpiiefvQ==','win'=>'100','free_spin'=>'3',
                'ai'=>'1581315164,234242134,31231,102,103,50','desc'=>'qweqw','fround_id'=>'0','lines'=>'10','line_bet'=>'5','fround_provider'=>'0',
            ),
        );
    }

    /**
     * @dataProvider placeBetRequestDataProvider
     */
    public function testValidation($lid,$cid,$rid,$gid,$sid,$hinfo,$operator,$method_name,$lp,$pp,$uid,$sesid,$wager,$ai,$fround_id,$lines,$line_bet,$fround_provider)
    {
        $_REQUEST = array('lid'=>$lid,'cid'=>$cid,'rid'=>$rid,'gid'=>$gid,'sid'=>$sid,'hinfo'=>$hinfo,'method_name'=>$method_name,
            'uid'=>$uid,'sesid'=>$sesid,'wager'=>$wager,'lp'=>$lp,'pp'=>$pp,'operator'=>$operator,
            'ai'=>$ai,'fround_id'=>$fround_id,'lines'=>$lines,'line_bet'=>$line_bet,'fround_provider'=>$fround_provider,
        );

        $request = new Request();
        $validation = new Validation();
        $return = $validation->validateRequest($request);

        $this->assertTrue(is_bool($return), "Validation failed to return a boolean value after validation");

    }

}
