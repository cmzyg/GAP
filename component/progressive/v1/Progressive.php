<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 03/11/14
 * Time: 16:52
 */

namespace component\progressive\v1;


use component\accounting\Accounting;
use component\configurationmanager\ConfigurationManager;
use component\progressive\ProgressiveInterface;
use component\progressive\Progressive as BaseProgressive;
use component\request\PlaceAndSettleBetRequest;
use component\request\PlaceBetRequest;
use component\request\ProgressiveServerReadByCurrencyRequest;
use component\request\ProgressiveWinByCurrencyRequest;
use component\request\Request;
use exceptions\ProgressiveException;
use model\GMAPIDataRepository;

class Progressive extends BaseProgressive implements ProgressiveInterface {

    private $licenseeConfig;

    /**
     *
     * @var null|\component\request\Request|\component\request\BeforeLoginRequest|\component\request\PlaceBetRequest|\component\request\PlaceAndSettleBetRequest|\component\request\SettleBetRequest
     */
    protected $request;

    public function progressiveServerCallToReadLevels()
    {
        $dbPrefix = $this->licenseeConfig->getDbPrefix();

        $getData = new DataGetter($this->request);

        $operatorId     = $getData->getOperatorId();
        $currency       = $getData->getCurrency($dbPrefix);
        $coinValue      = (double)$this->request->getCoinValue() / 100;
        $progressiveId  = $this->request->getInternalProgressiveId();

        $dbName = $dbPrefix.'_casino_games';
        $data = new GMAPIDataRepository($dbName);
        $result = $data->fetchProgressiveLevel($operatorId,$progressiveId,$currency,$coinValue);
        if($result === -1 || $result === '-1' || $result === false )
        {
            throw new ProgressiveException('Progressive server. Read progressive value for level failed.',2,5);
        }

        $this->processResponse = null;
        foreach ($result as $particularValue) {
            $this->processResponse = $particularValue;
            $this->processStatus = true;
            return true;
        }
    }

    public function progressiveWinUpdateByCurrency($flag = 1)
    {

        try
        {
            $accounting = new Accounting();
            $accounting = $accounting->loadComponent();
            $accounting->setRequest($this->request);
            $dbPrefix = $this->request->getLicenseeObject()->getDbPrefix();

            // Set values
            $getData = new DataGetter($this->request);
            $operatorId = $getData->getOperatorId();
            $progressiveId = $this->request->getInternalProgressiveId();
            $currency = $this->request->getCurrency();
            $coinValue = (double)($this->request->getCoinValue() / 100);
            $userId = $this->request->getUserId();
            $percentage = $this->request->getPercentage();

            $dbName = $dbPrefix.'_casino_games';
            $data = new GMAPIDataRepository($dbName);
            $result = $data->fetchProgressiveWin($operatorId,$progressiveId,$currency,$coinValue,$userId,1,$percentage,$flag);

            if($result === null || $result === '' || $result === false )
            {
                throw new ProgressiveException('Problem to get JP current values.',2,3);
            }

            $progWin = array();
            foreach ($result as $particular_value) {
                $progWin = (double)$particular_value;
            }

            $this->processResponse = array();
            $this->processResponse['progressive_win'] = (round($progWin,2,PHP_ROUND_HALF_UP))*100;

            /* Preparing Data to save to accouting */

            $result = $data->fetchProgressiveLevel($operatorId,$progressiveId,$currency,$coinValue);
            if($result === -1 || $result === '-1')
            {
                $this->processStatus = false;
                throw new ProgressiveException('Problem to get JP current values.',2,5);
            }

            $progJackPot = null;
            foreach ($result as $particularValue) {
                $progJackPot = $particularValue;
                break;
            }


            $jackpotStats = array();
            $jackpotStats['jackpotValue'] = (double)$progJackPot;
            $jackpotStats['jackpotWin'] = (double)$progWin;
            $jackpotStats['coinValue'] = $coinValue;

            $jackPotJson = json_encode($jackpotStats);

            $jpw = (double)$progWin;

            if(!$accounting->registerJackPotWin($coinValue, $jpw, $jackPotJson))
            {
                $this->processStatus = false;
                throw new ProgressiveException("Could not register accounting jackpot win", 4);
            }

            $this->processStatus = true;
            return $this->processResponse;
        }catch (\Exception $ex)
        {
            $this->processResponse = array("jackpot_error" => $ex->getMessage());

            return false;
        }

    }

    public function getProgressiveContributionValue()
    {
        $dbPrefix = $this->licenseeConfig->getDbPrefix();
        $dbName = $dbPrefix.'_casino_games';

        $getData = new DataGetter($this->request);
        $operatorId = $getData->getOperatorId();

        $coinValue = (double)($this->request->getCoinValue() / 100);
        $currency = $this->request->getCurrency();
        $progressiveBet = (double)($this->request->getProgressiveBet() / 100);
        $progressiveId1 = $this->request->getProgressiveId1();
        $progressiveId2 = $this->request->getProgressiveId2();

        $jackpotValues = array();
        $contribution = array();
        $json = array();

        $data = new GMAPIDataRepository($dbName);
        for ($level = $progressiveId1; $level <= $progressiveId2; $level++) 
        {
            $result = $data->fetchProgressiveLevel($operatorId,$level,$currency,$coinValue);
            if($result === -1 || $result === '-1' || $result === false )
            {
                throw new ProgressiveException('Problem to get JP current values.',2,3);
            }

            foreach ($result as $particularValue) {
                $jackpotValues[$level] = $particularValue;
                break;
            }
        }

        $i = 0;
        $test = array();
        for ($level = $progressiveId1; $level <= $progressiveId2; $level++)
        {
            $result = $data->fetchProgressiveLevelAfterIncrementAll($operatorId,$level,$currency,$progressiveBet,0);
            if($result === -1 || $result === '-1' || $result === false || (!isset($result['contribution'])) || (isset($result['contribution']) && $result['contribution'] === null))
            {
                throw new ProgressiveException('Problem to calculate Jackpot Contribution.',2,1);
            }
            $test[] = $result;
            $contribution[] = $result['contribution'];

            $json[$i]['jackpotContribution'] = $this->convertAnnotationE($result['contribution']);
            $json[$i]['jackpotValue'] = $jackpotValues[$level] + ($result['contribution']);
            $json[$i]['coinValue'] = $coinValue;
        }

        $this->processResponse = array();
        $this->processStatus = true;

        $convert = array_sum($contribution);
        // for client Request and accounting
        $this->processResponse['sumOfJackpotValueLevels'] = $this->convertAnnotationE($convert);

        // for accounting
        $this->processResponse['jsonLevelsJackpotRepresentation'] = stripslashes(json_encode($json));

        // Don't call to game server !!!!

    }

    public function updateProgressiveContribution(Request $request)
    {
        if($request instanceof PlaceBetRequest || $request instanceof PlaceAndSettleBetRequest)
        {
            $dbPrefix = $this->licenseeConfig->getDbPrefix();
            $dbName = $dbPrefix.'_casino_games';

            $getData = new DataGetter($this->request);
            $operatorId = $getData->getOperatorId();

            $currency = $this->request->getCurrency();
            $progressiveBet = (double) $this->request->getProgressiveBet();
            $progressiveId1 = $this->request->getProgressiveId1();
            $progressiveId2 = $this->request->getProgressiveId2();

            $data = new GMAPIDataRepository($dbName);

            for ($level = $progressiveId1; $level <= $progressiveId2; $level++)
            {
                $result = $data->fetchProgressiveLevelAfterIncrementAll($operatorId,$level,$currency,$progressiveBet,1);
                if($result === -1 || $result === '-1' || $result === false || (!isset($result['contribution'])) || (isset($result['contribution']) && $result['contribution'] === null))
                {
                    throw new ProgressiveException('Problem to update progressive with a contribution.',2,1);
                }
            }
        }
        else
        {
            return false;
        }
    }

    public function execute(Request $request)
    {
        $this->request = $request;

        $cm = new ConfigurationManager();
        $cm = $cm->loadComponent();
        $this->licenseeConfig = $cm->getConfiguration($this->request->getLicenseeId());

        try
        {
            if($request instanceof ProgressiveServerReadByCurrencyRequest)
            {
                // progreadcurr.php - external method
                $this->progressiveServerCallToReadLevels();
            }
            elseif($request instanceof ProgressiveWinByCurrencyRequest)
            {
                // client.php?method_name=progwincurr
                $this->progressiveWinUpdateByCurrency();
            }
            elseif($request instanceof PlaceBetRequest || $request instanceof PlaceAndSettleBetRequest)
            {
                // update progressive lvl after each place bet PBET
                $this->getProgressiveContributionValue();
            }
            else
            {
                $this->processResponse = array("error" => $this->NotSupported());
            }
        }
        catch(\Exception $error)
        {
            self::getLogger()->addException($error);
            $this->processResponse = array("error" => $error->getMessage());
        }
    }

    private function convertAnnotationE($number)
    {
        $string     = (string)$number;
        $position   = strpos($string, "E-");
        if( $position !== false )
        {
            $number_fix     = $number + 1;
            $string_fix     = (string)$number_fix;
            $string_fix[0]  = "0";
            return $string_fix;
        }
        return $number;
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

} 