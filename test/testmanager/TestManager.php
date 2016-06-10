<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 27/01/15
 * Time: 15:22
 */

class TestManager {

    private $basePath;

    private $testBasePath;

    private $tests = array(
        "Accounting" => "accounting",
        "Configuration Manager" => "configurationmanager",
    );

    private $testResults = array();

    public function executeTest(array $test = null)
    {
        $testToExecute = ((is_null($test)) ? $this->tests : $test);

        foreach($testToExecute as $testName => $testPath)
        {
            $response = shell_exec("./test.sh ".$this->basePath." ".$this->testBasePath.$testPath);
        }
    }
} 