<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 04/11/14
 * Time: 11:25
 */

namespace component\logger\v1;


class TempLogger {



    private $exceptions = array();
    private $mySqlErrors = array();
    private $mySqlErrorPointer = 0;
    private $loginStack = array();
    private $showLogins = false;

    //other components to use

    function __construct()
    {


    }

    /**

     * Method to store all occurred Errors
     * @param \Exception $ex
     * @return boolean - Returns true on success
     */
    public function addException(\Exception $ex, &$thisone = null)
    {

    }

    /**
     * Method to store Exceptions in files, requires gmAPI response to Game Server
     * @param string $gmAPIResponse
     * @return int - Returns number of Exceptions added to Logger
     */
    public function getExceptionMessages($gmAPIResponse)
    {
       return 0;
    }

    public function login(&$class)
    {
        return true;
    }

    public function logout(&$class)
    {
        return true;
    }

    public function addMySQLError($error, $query)
    {

    }

    public function __destruct()
    {

    }

    public function getStack()
    {
        return "";
    }

    private function getMicroTime()
    {
        return date("H:i:s") . substr((string) microtime(), 1, 8);
    }

    private function showLogs($login='start')
    {

    }

    //General Error Handling
    public function registerErrorHandler($errorTypes)
    {

    }

    public function errorHandler($errorNumber, $errorString, $errorFile, $errorLine, $errorContext)
    {


    }

    /** Handlers */
    public function handleNotice($error)
    {

    }

    public function handleUnknown($error)
    {

    }

    public function handleWarning($error)
    {

    }

    public function handleFatal()
    {

    }

    public function handleUser($error)
    {

    }

    /** make log */
    public function log($error)
    {

    }


}
