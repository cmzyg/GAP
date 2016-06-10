<?php
/**
 * @author Samuel I. Amaziro
 */
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

require_once dirname(dirname(dirname(dirname(dirname(realpath(dirname(__FILE__))))))).DIRECTORY_SEPARATOR."bootstrap.php";
/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * @var null| \component\configurationmanager\v1\ConfigurationManager
     */
    private $configurationManager;
    /**
     * @var \component\accounting\v1\Accounting|null
     */
    private $account;
    /**
     * @var null|\component\request\Request
     */
    private $request;

    private $wagerRequest;

    private $winRequest;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->configurationManager = new \component\configurationmanager\ConfigurationManager();
        $this->configurationManager = $this->configurationManager->loadComponent();
        $this->account = new \component\accounting\Accounting();
        $this->account = $this->account->loadComponent();
    }


    /**
     * @Given /^that i have a "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)"$/
     */
    public function thatIHaveA($licenseeId, $playerId, $sessionId, $gameId, $gameSkinId, $currency, $username, $country)
    {
        $lObj = $this->configurationManager->getConfiguration($licenseeId);

        $request = new \component\request\Request();
        $request->setLicenseeId($licenseeId);
        $request->setPlayerId($playerId);
        $request->setSessionId($sessionId);
        $request->setGameId($gameId);
        $request->setSkinId($gameSkinId);
        $request->setCurrency($currency);
        $request->setUsername($username);
        $request->setCountry($country);
        $request->setLicenseeObject($lObj);
        $request->setProviderId(1);
        $this->request = $request;
    }

    /**
     * @Given /^there is no session with this session id$/
     */
    public function thereIsNoSessionWithThisSessionId()
    {
        $this->account->setRequest($this->request);
        PHPUnit_Framework_Assert::assertFalse($this->account->isSessionExists(), "Session is closed, no win or wager transaction should be carried out for a closed session");
    }

    /**
     * @When /^i run createNewSession with player "([^"]*)"$/
     */
    public function iRunCreatenewsessionWithPlayer($balance)
    {
        $this->account->setRequest($this->request);
        $this->account->startSession(floatval($balance));
    }

    /**
     * @Then /^i should have an open session with "([^"]*)" "([^"]*)" and "([^"]*)"$/
     */
    public function iShouldHaveAnOpenSessionWithAnd($sessionId, $playerId, $playerBalance)
    {
        $repo = new \model\GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix()."_accounting");
        $data = $repo->fetchAccountingPlayersBySessionId($sessionId);
        PHPUnit_Framework_Assert::assertEquals($playerId, $data['player_id'], "Incorrect player id found in accounting database it should be: ".$playerId);
        PHPUnit_Framework_Assert::assertEquals($playerBalance, $data['start_cash_balance'], "Incorrect player balance found in accounting database should be: ".$playerBalance);
        PHPUnit_Framework_Assert::assertEquals("ACTIVE", $data['session_status'], "Session is closed it should be ACTIVE" );
    }



    /**
     * @Given /^that i have "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)"$/
     */
    public function thatIHave($sessionId, $roundId, $transactionId,$coinValue, $licenseeId)
    {
        $lObj = $this->configurationManager->getConfiguration($licenseeId);
        $request = new \component\request\PlaceBetRequest();
        $request->setLicenseeId($licenseeId);
        $request->setSessionId($sessionId);
        $request->setRoundId($roundId);
        $request->setTransactionId($transactionId);
        $request->setCoinValue($coinValue);
        $request->setLicenseeObject($lObj);
        $request->setProviderId(1);
        $this->wagerRequest = $request;
        $request = new \component\request\SettleBetRequest();
        $request->setLicenseeId($licenseeId);
        $request->setSessionId($sessionId);
        $request->setRoundId($roundId);
        $request->setTransactionId($transactionId);
        $request->setCoinValue($coinValue);
        $request->setLicenseeObject($lObj);
        $request->setProviderId(1);
        $this->winRequest = $request;
        $request = new \component\request\Request();
        $request->setLicenseeId($licenseeId);
        $request->setLicenseeObject($lObj);
        $request->setProviderId(1);
        $this->request = $request;
    }

    /**
     * @Given /^the session with "([^"]*)" is still open$/
     */
    public function theSessionWithIsStillOpen($sessionId)
    {
        $repo = new \model\GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix()."_accounting");
        $data = $repo->fetchAccountingPlayersBySessionId($sessionId);
        PHPUnit_Framework_Assert::assertEquals("ACTIVE", $data['session_status'], "Session is not open, session should be ACTIVE");
    }

    /**
     * @When /^i run createWagerForOpenSession with "([^"]*)" and "([^"]*)"$/
     */
    public function iRunCreatewagerforopensessionWithAnd($playerBalance, $wagerAmount)
    {
        $this->wagerRequest->setBetAmount($wagerAmount * 100);
        $this->request = $this->wagerRequest;
        $this->account->setRequest($this->request);
        $this->account->betSession($playerBalance);
    }

    /**
     * @Then /^i should have in the database a transaction with the same "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)"$/
     */
    public function iShouldHaveInTheDatabaseATransactionWithTheSame($roundId, $transactionId,$coinValue, $amount)
    {
        $repo = new \model\GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix()."_accounting");
        $data = $repo->fetchAccountingWagers(0, $transactionId);
        $actualTransactionId = (( is_array($data) && isset($data[0]['transaction_id'])) ? $data[0]['transaction_id'] :"none" );
        $actualCoinValue = (( is_array($data) && isset($data[0]['coin_value'])) ? $data[0]['coin_value'] : "none");
        $actualRoundId = (( is_array($data) && isset($data[0]['round_id'])) ? $data[0]['round_id'] : "none");

        PHPUnit_Framework_Assert::assertEquals($transactionId, $actualTransactionId, "Transaction id does not match");
        PHPUnit_Framework_Assert::assertEquals($coinValue, $actualCoinValue, "Coin value mismatch");
        PHPUnit_Framework_Assert::assertEquals($roundId, $actualRoundId, "Round Id mismatch");
        if($this->request instanceof \component\request\PlaceBetRequest)
        {
          PHPUnit_Framework_Assert::assertEquals($amount,((is_array($data) && isset($data[0]['bet_amount'])) ? $data[0]['bet_amount'] : "none"), "Bet amount mismatch, bet amount should be: ".$amount);
        }

        if($this->request instanceof \component\request\SettleBetRequest)
        {
            PHPUnit_Framework_Assert::assertEquals($amount,((is_array($data) && isset($data[0]['win_amount'])) ? $data[0]['win_amount'] : "none"), "Win amount mismatch, win amount should be: ".$amount);
        }

    }

    /**
     * @When /^i run createWinForOpenSession with "([^"]*)" and "([^"]*)"$/
     */
    public function iRunCreatewinforopensessionWithAnd($playerBalance, $winAmount)
    {
        $this->winRequest->setWinAmount($winAmount *100);
        $this->request = $this->winRequest;
        $this->account->setRequest($this->request);
        $this->account->winSession($playerBalance);
    }



    /**
     * @When /^i run endAnOpenSession with "([^"]*)"$/
     */
    public function iRunEndanopensessionWith($playerBalance)
    {
        $this->account->setRequest($this->request);
        $this->account->endSession($playerBalance);
    }

    /**
     * @Then /^i should have a closed session with "([^"]*)" "([^"]*)" and "([^"]*)"$/
     */
    public function iShouldHaveAClosedSessionWithAnd($sessionId, $playerId, $playerBalance)
    {
        $repo = new \model\GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix()."_accounting");
        $data = $repo->fetchAccountingPlayersBySessionId($sessionId);
        PHPUnit_Framework_Assert::assertEquals($playerId, $data['player_id'], "Incorrect player id found in accounting database it should be: ".$playerId);
        PHPUnit_Framework_Assert::assertEquals($playerBalance, $data['end_cash_balance'], "Incorrect player balance found in accounting database should be: ".$playerBalance);
        PHPUnit_Framework_Assert::assertEquals("INACTIVE", $data['session_status'], "Session is not closed it should be INACTIVE" );
    }

    /**
     * @Given /^the session with "([^"]*)" is closed$/
     */
    public function theSessionWithIsClosed($sessionId)
    {
        $repo = new \model\GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix()."_accounting");
        $data = $repo->fetchAccountingPlayersBySessionId($sessionId);
        PHPUnit_Framework_Assert::assertEquals("INACTIVE", $data['session_status'], "Session is open, session should be INACTIVE");
    }

    /**
     * @Then /^i should not have in the database a transaction with the same "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)"$/
     */
    public function iShouldNotHaveInTheDatabaseATransactionWithTheSame($roundId, $transactionId,$coinValue, $amount)
    {
        $repo = new \model\GMAPIDataRepository($this->request->getLicenseeObject()->getDbPrefix()."_accounting");
        $data = $repo->fetchAccountingWagers(0, $transactionId);
        $size = sizeof($data);
        for($k=0; $k < $size; $k++)
        {
            $actualTransactionId = (( is_array($data) && isset($data[$k]['transaction_id'])) ? $data[$k]['transaction_id'] :"none" );
            $actualCoinValue = (( is_array($data) && isset($data[$k]['coin_value'])) ? $data[$k]['coin_value'] : "none");
            $actualRoundId = (( is_array($data) && isset($data[$k]['round_id'])) ? $data[$k]['round_id'] : "none");
            $actualAmount = "none";

            if($this->request instanceof \component\request\PlaceBetRequest)
            {
                $actualAmount = ((is_array($data) && isset($data[$k]['bet_amount'])) ? $data[$k]['bet_amount'] : "none");
            }

            if($this->request instanceof \component\request\SettleBetRequest)
            {
                $actualAmount = ((is_array($data) && isset($data[$k]['win_amount'])) ? $data[$k]['win_amount'] : "none");
            }

            $actual = array($actualTransactionId, $actualRoundId, $actualCoinValue, $actualAmount);
            $expected = array($transactionId, $roundId, $coinValue, $amount);

            PHPUnit_Framework_Assert::assertNotEquals($expected, $actual, "Duplicate Transaction found ");
        }

    }




    /**
     * @BeforeFeature
     */
    public static function cleanAccountingDatabase()
    {
        $database = "dummyapi_accounting";
        $repo = new \model\GMAPIDataRepository($database);
        $sql = "DELETE FROM ".$database.".accounting_players";
        $repo->getConn()->query($sql);
        $sql = "DELETE FROM ".$database.".accounting_wagers";
        $repo->getConn()->query($sql);
        $sql = "DELETE FROM ".$database.".accounting_games";
        $repo->getConn()->query($sql);
    }

}
