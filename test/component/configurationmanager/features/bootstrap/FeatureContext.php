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

    private $id;
    private $output;
    private $operator;
    private $currentServer;
    private $serverConfigKey;

    /**
     * @var \component\configurationmanager\v1\ConfigurationManager
     */
    private $configurationManager;
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
    }

    /**
     * @Given /^that i have the licensee id "([^"]*)"$/
     */
    public function thatIHaveTheLicenseeId($id)
    {
        $id = intval($id);
        PHPUnit_Framework_Assert::assertGreaterThan(0, $id);
        $this->id = $id;
    }

    /**
     * @When /^i run getConfiguration by licensee$/
     */
    public function iRunGetconfigurationByLicensee()
    {
        $this->output = $this->configurationManager->getConfiguration($this->id);
    }

    /**
     * @Then /^i should get the licensee details as an instance of "([^"]*)"$/
     */
    public function iShouldGetTheLicenseeDetailsAsAnInstanceOf($instance)
    {
        PHPUnit_Framework_Assert::assertInstanceOf($instance, $this->output);
    }

    /**
     * @Given /^that i have "([^"]*)" as an operator name of the licensee$/
     */
    public function thatIHaveAsAnOperatorNameOfTheLicensee($operatorName)
    {
        $this->operator = $operatorName;
        PHPUnit_Framework_Assert::assertTrue(is_string($operatorName));
    }

    /**
     * @When /^i run getConfiguration by operator$/
     */
    public function iRunGetconfigurationByOperator()
    {
        $this->output = $this->configurationManager->getConfiguration(0, $this->operator, true, $this->operator);
    }

    /**
     * @Given /^that the name of the current server is "([^"]*)"$/
     */
    public function thatTheNameOfTheCurrentServerIs($serverName)
    {
        $this->currentServer = $serverName;
        PHPUnit_Framework_Assert::assertTrue(is_string($serverName));
    }

    /**
     * @Given /^i have the following "([^"]*)" as part of the server configuration$/
     */
    public function iHaveTheFollowingAsPartOfTheServerConfiguration($serverConfigurationKey)
    {
        $this->serverConfigKey = $serverConfigurationKey;
    }

    /**
     * @When /^i run getServerConfiguration$/
     */
    public function iRunGetserverconfiguration()
    {
        $this->output = $this->configurationManager->getServerConfiguration($this->currentServer);
    }

    /**
     * @Then /^i should get an array that contains "([^"]*)"$/
     */
    public function iShouldGetAnArrayThatContains($configurationValue)
    {
        PHPUnit_Framework_Assert::assertTrue(isset($this->output[$this->serverConfigKey]), "There was no configuration found with the configuration key specified");
        PHPUnit_Framework_Assert::assertEquals($configurationValue, $this->output[$this->serverConfigKey]);
    }

    /**
     * @Given /^that i have the following licensee id "([^"]*)"$/
     */
    public function thatIHaveTheFollowingLicenseeId($lid)
    {
        $this->id = intval($lid);
        PHPUnit_Framework_Assert::assertGreaterThan(0, $this->id);
    }

    /**
     * @Given /^that i have not fetched the details of this licensee with this id before$/
     */
    public function thatIHaveNotFetchedTheDetailsOfThisLicenseeWithIdThisBefore()
    {
        PHPUnit_Framework_Assert::assertFalse(file_exists($this->configurationManager->getConfigPath().$this->id.".json"), "Licensee details already exist");
    }

    /**
     * @When /^i run fetchAndStoreLicenseeDetails$/
     */
    public function iRunFetchandstorelicenseedetails()
    {
        $this->output = $this->configurationManager->getConfiguration($this->id);
    }

    /**
     * @Then /^i should have a file created with the name "([^"]*)" and get an instance of "([^"]*)"$/
     */
    public function iShouldHaveAFileCreatedWithTheNameAndGetAnInstanceOf($fileName, $classInstance)
    {
        PHPUnit_Framework_Assert::assertTrue(file_exists($this->configurationManager->getConfigPath().$fileName), "Failed to read licensee details from file");
        PHPUnit_Framework_Assert::assertInstanceOf($classInstance, $this->output);
    }

    /**
     * @BeforeScenario
     */
    public function deleteLicenseeCache()
    {
        $files = scandir($this->configurationManager->getConfigPath());
        if(is_array($files))
        {
            foreach($files as $file)
            {
                if($file !== ".." && $file !== ".")
                {
                    unlink($this->configurationManager->getConfigPath().$file);
                }
            }
        }
    }


}