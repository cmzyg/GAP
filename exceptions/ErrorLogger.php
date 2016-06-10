<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/07/14
 * Time: 14:35
 */

namespace exceptions;

class ErrorLogger {

    private $lastToken;

    public static  $logPath;
    public static $licenseeId;

    public function __construct(\Exception $ex, $method, $summaryLevel = null, $query = null)
    {
        $this->createLog($ex, $method, $summaryLevel, $query);
    }


    public function createLog(\Exception $exception, $method, $sub_summary = null, $query = null)
    {
        //Global Variables
        Global $config;
        
        $partner    = $config['module']['name'];
        $logPath    = $config['log']['file']['root'];
        //End Global Variables

        if(is_null($config))
        {
            $moduleApiPath = realpath(dirname(dirname(dirname(__FILE__))));
            $moduleApiPath = $moduleApiPath.DIRECTORY_SEPARATOR;
            $logPath = self::$logPath;
            $licenseePath = $moduleApiPath."helpers".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."licenses.php";

            if(file_exists($licenseePath))
            {
                require $licenseePath;
                $partner = $licenses[self::$licenseeId] ? $licenses[self::$licenseeId] : "NoLicenseeSpecified";
            }
        }

        //Log Formatting starts here
        $token      = time().rand(0, 99);            
        $this->lastToken = $token;

        $message = $this->cleanItem($exception->getMessage());
        $stackTrace = $this->cleanItem($exception->getTraceAsString());

        if($sub_summary || $sub_summary >= 0)
        {
            $logformat = $token."; ".date("Y-m-d H:i:s")."; ".$exception->getType()."; ".$exception->getSubType()."; ".$exception->getSummary($sub_summary)."; ".$message."; ".$partner."; ".$method."; ".$exception->getCurrentStatus()."; ".$stackTrace."; ".$this->addNewParameters().PHP_EOL;

        }
        else
        {
            $logformat = $token."; ".date("Y-m-d H:i:s")."; ".$exception->getType()."; ".$exception->getSubType()."; ".$exception->getSummary()."; ".$message."; ".$partner."; ".$method."; ".$exception->getCurrentStatus()."; ".$stackTrace."; ".$this->addNewParameters().PHP_EOL;
        }

        $this->storeLogToFileSystem($logPath."gmapilog_".date("j.n.Y").'.log', $logformat);

        if(isset($query))
        {
            $this->storeLogToFileSystem($logPath."gmapisqllog_".date("j.n.Y").".log", $this->formatSqlLog($token, $query));
        }
    }

    public function addNewParameters()
    {
        $parameters = "";

        if(isset($_REQUEST['sid']))
        {
            $skinId = $_REQUEST['sid'];
            $playerId = $_REQUEST['lp'];
            $pp =  $_REQUEST['pp'];
            @list($sessionId, $username, $others) = explode(",", $pp);
            $parameters = $skinId."; ".$playerId."; ".$username;
        }

        return $parameters;
    }

    public function cleanItem($item)
    {
        $newItem = str_replace(';','',$item);
        $cleanItem = trim(preg_replace('~[\r\n]+~', '', $newItem));
        return $cleanItem;
    }

    public function formatSqlLog($token, $query)
    {
        $query = trim(preg_replace('~[\r\n]+~', '', $query));
        return date("Y-m-d H:i:s")."; ".$token."; ".$query.PHP_EOL;
    }

    public function storeLogToFileSystem($logPath, $log)
    {
        @file_put_contents($logPath, $log, FILE_APPEND| LOCK_EX); //, FILE_APPEND| LOCK_EX
    }


} 