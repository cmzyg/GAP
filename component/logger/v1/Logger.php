<?php

/**
 * Description of Logger
 *
 * @author rafal
 */

namespace component\logger\v1;

use component\request\Request;
use component\configurationmanager\ConfigurationManager;
use component\logger\ErrorLoggerInterface;
use exceptions\Exception as InternalException;

class Logger extends \component\logger\Logger implements ErrorLoggerInterface {

    private $exceptions = array();
    private $mySqlErrors = array();
    private $mySqlErrorPointer = 0;
    private $loginStack = array();
    private $showLogins = false;
    private $gapRequest = 'FAKE_CHECK'; //just to also provide request/response on NULL 
    private $gapResponse = 'FAKE_CHECK';
    private $soapRequest = 'FAKE_CHECK';
    private $soapResponse = 'FAKE_CHECK';
    private $internalRequest = NULL;
    private $internalResponse = '{"status":"error","response":"There was no response provided"}';

    /**

     * Method to store all occurred Errors
     * @param \Exception $ex
     * @return boolean - Returns true on success
     */
    public function addException(\Exception $ex, &$thisone = null)
    {

        $troublemaker = ($thisone != null) ? $thisone : end($this->loginStack);

        if ($this->mySqlErrorPointer != count($this->mySqlErrors))
        {
            $this->exceptions[] = array('exception' => $ex, 'name' => $troublemaker, 'mysqlError' => $this->mySqlErrorPointer);
            $this->mySqlErrorPointer++;
        } else
        {
            $this->exceptions[] = array('exception' => $ex, 'name' => $troublemaker);
        }
        return true;
    }

    /**
     * Method to store Exceptions in files, requires gmAPI response to Game Server
     * @param string $gmAPIResponse
     * @return int - Returns number of Exceptions added to Logger
     */
    public function getExceptionMessages($gmAPIResponse = '')
    {
        if ($this->mySqlErrorPointer != count($this->mySqlErrors))
        {
            $this->addException(new \Exception('Unhandled MySQL ERROR'));
        }
        /* temporary error logging */
        $eStr = '';
        foreach ($this->exceptions as $ex)
        {
            $e = $ex['exception'];

            $msgArr = array(
                '=====
<p><b>Exception [<i>' . $e->getCode() . '</i>]:</b>',
                '<b><i>Message:</i></b> ' . $e->getMessage(),
                '<b><i>Name:</i></b> ' . get_class($ex['name']),
                '<b><i>Component stack:</i></b> ' . $this->getStack());

            if ($e instanceof InternalException)
            {
                $msgArr[] = '<b><i>Status:</i></b> ' . $e->getCurrentStatus();
                $msgArr[] = '<b><i>Type:</i></b> ' . $e->getType();
                $msgArr[] = '<b><i>SubType:</i></b> ' . $e->getSubType();
                $msgArr[] = '<b><i>Summary:</i></b> ' . $e->getSummary();
            }

            if (isset($ex['mysqlError']))
            {
                $msgArr[] = '<b><i>MySQL Error:</i></b> ' . $this->mySqlErrors[$ex['mysqlError']]['error'];
                $msgArr[] = '<b><i>MySQL Query:</i></b> ' . $this->mySqlErrors[$ex['mysqlError']]['query'];
            }

            $msgArr[] = '<b><i>gmAPI Response:</i></b> ' . $gmAPIResponse;
            $msgArr[] = '<b><i>File:</i></b> ' . $e->getFile();
            $msgArr[] = '<b><i>Line:</i></b> ' . $e->getLine() . '</p>';
            $eStr .= join('
', $msgArr) . "
";
        }
        //print $eStr;
        $log = strip_tags($eStr);
        $logFile = '/app_logs/php_error_logs/NewGMAPI_LOGS_' . date('_Y-m-d') . '.log';
        //file_put_contents($logFile, $log, FILE_APPEND);

        $this->exceptions = array();
        /* temporary error logging end */


        return count($this->exceptions);
    }

    /**
     * 
     * @return array($logPath,$partner,$troublemaker)
     */
    private static function getGlobalPaths()
    {

        $cm = new ConfigurationManager();
        $cs = $cm->loadComponent();

        //get logPath
        $server = $cs->getServerConfiguration();
        $logPath = $server["ERROR_LOG_PATH"];

        //get License
        $partner = 'NOLICENSEESPECIFIED';
        $request = new Request();
        $licenseeId = $request->getLicenseeId();
        if ($licenseeId !== null)
        {
            $licConf = $cs->getConfiguration($licenseeId);
            if(is_object($licConf))
            {
                $licenseeName = $licConf->getLicenseeName();
                if ($licenseeName != null && $licenseeName != '')
                {
                    $partner = $licenseeName;
                }
            }

        }

        //get troublemaker Method
        $troublemaker = strtoupper($request->getMethodName());

        //other parameters as a string variable, yes

        $parameters = "";

        if ($request->getSkinId() != null)
        {
            $skinId = $request->getSkinId();
            $parameters .= $skinId . "; ";
        }
        if ($request->getPlayerId() != null && $request->getPp() != null)
        {
            $playerId = $request->getPlayerId();
            $pp = $request->getPp();
            $ppExploded = explode(",", $pp);
            $parameters .= $playerId . "; " . ((isset($ppExploded[1]) ? $ppExploded[1] : ''));
        }



        return array($logPath, $partner, $troublemaker, $parameters);
    }

    /**
     * Method to flush Exceptions to files, requires gmAPI response to Game Server
     * @param string $gmAPIResponse
     */
    private function flush()
    {
        //\Exception $exception, $method,  $query = null
        $globals = self::getGlobalPaths();
        $logPath = $globals[0];
        $partner = $globals[1];
        $method = $globals[2];
        $parameters = $globals[3];

        if ($this->mySqlErrorPointer != count($this->mySqlErrors))
        {
            $this->addException(new \exceptions\GMAPIGeneralException('Unhandled MYSQL Error', 0));
        }


        foreach ($this->exceptions as $ex)
        {

            $exception = $ex['exception'];

            //Log Formatting starts here
            $token = time() . str_pad((string) round(microtime() * 1000000), 6, '0', STR_PAD_LEFT);


            $message = $this->cleanItem($exception->getMessage());
            $stackTrace = $this->cleanItem($exception->getTraceAsString());
            
            if(strpos('3rd PARTY',$message)!==FALSE){

            $desc = substr($message, 0, strpos($message, '3rd PARTY'));

            $message = substr($message, strpos($message, '3rd PARTY'));
            } else {
                $desc = $message;
                $message ='';
            }

            //add requests/responses to error
            $this->internalRequest = (is_array($this->internalRequest) ? json_encode($this->internalRequest) : $this->internalRequest);
            $message = " INTERNAL REQUEST: " . $this->internalRequest . " " . $message;

            $this->internalResponse = (is_array($this->internalResponse) ? json_encode($this->internalResponse) : $this->internalResponse);
            $message .= " INTERNAL RESPONSE: " . $this->internalResponse;


            if ($this->gapRequest != 'FAKE_CHECK')
            {
                $this->gapRequest = (is_array($this->gapRequest) ? json_encode($this->gapRequest) : $this->gapRequest);
                $message = " GAP REQUEST: " . $this->gapRequest . " " . $message;
            }


            if ($this->gapResponse != 'FAKE_CHECK')
            {
                $this->gapResponse = (is_array($this->gapResponse) ? json_encode($this->gapResponse) : $this->gapResponse);
                $message .= " GAP RESPONSE: " . $this->gapResponse;
            }
            $message = $desc . " " . $message;

            $message = str_replace(PHP_EOL, '', $message);

            $logformat = $token . "; " . date("Y-m-d H:i:s") . "; " . $exception->getType() . "; " . $exception->getSubType() . "; " . $exception->getSummary() . "; " . $message . "; NEW*" . $partner . "; " . $method . "; " . $exception->getCurrentStatus() . "; " . $stackTrace . "; " . $parameters . PHP_EOL;
            file_put_contents($logPath . "gmapilog_" . date("j.n.Y") . '.log', $logformat, FILE_APPEND | LOCK_EX);


            if (isset($ex['mysqlError']))
            {
                $query = "ERROR: " . $this->mySqlErrors[$ex['mysqlError']]['error'] . " QUERY: " . $this->mySqlErrors[$ex['mysqlError']]['query'];
                $query = date("Y-m-d H:i:s") . "; " . $token . "; " . Trim(preg_replace('~[\r\n]+~', '', $query)) . PHP_EOL;
                file_put_contents($logPath . "gmapisqllog_" . date("j.n.Y") . ".log", $query, FILE_APPEND | LOCK_EX);
            }
        }

        $this->exceptions = array();
    }

    private function cleanItem($item)
    {
        $newItem = str_replace(';', '', $item);
        $cleanItem = trim(preg_replace('~[\r\n]+~', '', $newItem));
        return $cleanItem;
    }

    public function login(&$class)
    {

        if (count($this->loginStack) > 0)
        {
            $className = get_class($class);
            $prevClass = end($this->loginStack);
            $prevClassName = get_class($prevClass);
            if ($class instanceof $prevClassName && !($prevClass instanceof $className))
            {
                array_pop($this->loginStack);
                $this->loginStack[] = $class;
            } else if (!(!($class instanceof $prevClassName) && $prevClass instanceof $className))
            {
                $this->loginStack[] = $class;
            }
        } else
        {
            $this->loginStack[] = $class;
        }
        $this->showLogs();
        return true;
    }

    public function logout(&$class)
    {
        /*
          $name = get_class($class);
          $name = explode('\\', $name);
          $name = end($name);
         */
        //$key = array_search($class, $this->loginStack);


        $last = end($this->loginStack);
        $last = get_class($last);

        if ($class instanceof $last)
        {
            $this->showLogs('stop');
            array_pop($this->loginStack);
            return true;
        } else
        {
            return false;
        }
    }

    public function addMySQLError($error, $query)
    {
        $this->mySqlErrors[] = array(
            'error' => $error,
            'query' => $query
        );
    }

    public function getStack()
    {
        $names = array();
        foreach ($this->loginStack as $l)
        {
            $name = '';
            preg_match('/[^\\\]+$/', get_class($l), $name);
            $names[] = $name[0];
        }
        return "[" . join('][', $names) . ']';
    }

    private function getMicroTime()
    {
        return date("H:i:s") . substr((string) microtime(), 1, 8);
    }

    private function showLogs($login = 'start')
    {
        if ($this->showLogins)
        {
            $last = end($this->loginStack);
            $last = get_class($last);
            print $this->getMicroTime() . " " . $login . "[" . $last . "]: " . $this->getStack();
        }
    }

    //General Error Handling
    public static function registerErrorHandler($errorTypes)
    {
        set_error_handler('\component\logger\v1\Logger::errorHandler', $errorTypes);
        register_shutdown_function('\component\logger\v1\Logger::handleFatal');
    }

    public static function errorHandler($errorNumber, $errorString, $errorFile, $errorLine, $errorContext)
    {
        $error = array(
            'number' => $errorNumber,
            'string' => $errorString,
            'file' => $errorFile,
            'line' => $errorLine,
            'context' => $errorContext
        );


        switch ($errorNumber)
        {
            case E_WARNING:
                self::handleWarning($error);
                break;
            case E_NOTICE:
            default:
                self::handleNotice($error);
                break;
        }
    }

    /** Handlers */
    public static function handleNotice($error)
    {
        $errorNumber = $error['number'];
        $errorString = $error['string'];
        $errorFile = $error['file'];
        $errorLine = $error['line'];
        $errorTrace = $error['context'];

        if (!is_string($errorTrace))
        {
            $errorTrace = json_encode($errorTrace);
        }
        $syslog = "Notice " . "[$errorNumber][$errorString][file: $errorFile][line: $errorLine]";
        self::systemLog($syslog, $errorTrace);
    }

    public static function handleWarning($error)
    {
        $errorNumber = $error['number'];
        $errorString = $error['string'];
        $errorFile = $error['file'];
        $errorLine = $error['line'];
        $errorTrace = $error['context'];

        if (strpos($errorString,'failed to open stream') && strpos($error['file'], '/component/configurationmanager/v1/ConfigurationManager.php'))
            return;

        if (!is_string($errorTrace))
        {
            $errorTrace = "On line: " . $error['line'] . " in " . $error['file']; //@json_encode($errorTrace);
        }


        $syslog = "Warning " . "[$errorNumber][$errorString][file: $errorFile][line: $errorLine]";
        self::systemLog($syslog, $errorTrace);
    }

    public static function handleFatal()
    {
        $error = error_get_last();

        if ($error['type'] == 1 || $error['type'] == 4)
        {
            $errorNumber = $error['type'];
            $errorString = $error['message'];
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorTrace = "On line: $errorLine in $errorFile  ";

            if (!is_string($errorTrace))
            {
                $errorTrace = json_encode($errorTrace);
            }


            $syslog = "Fatal Error " . "[$errorNumber][$errorString][file: $errorFile][line: $errorLine]";

            self::systemLog($syslog, $errorTrace);
        }
    }

    /**
     * Make system log
     * @param type $details
     * @param type $trace
     */
    private static function systemLog($details, $trace)
    {

        $logID = time() . str_pad((string) round(microtime() * 1000000), 6, '0', STR_PAD_LEFT);
        $date = date('Y-m-d H:i:s');

        $details = trim(preg_replace('~[\r\n]+~', '', (string) $details));
        $trace = trim(preg_replace('~[\r\n]+~', '', (string) $trace));


        $globals = self::getGlobalPaths();

        $path = $globals[0] . "gmapisystemlog_" . date("j.n.Y") . ".log";
        $partner = $globals[1];

        $log = "$logID; $date; $details; NEW*$partner; $trace" . PHP_EOL;


        @file_put_contents($path, $log, FILE_APPEND | LOCK_EX);
    }

    /**
     * Custom Logger
     * @param string $name - part of the filename to Identify
     * @param string|array|anything $values - some parameters to pass to log
     */
    public static function log($name, $values, $license = null)
    {
        $list = '';
        if (is_array($values))
        {
            foreach ($values as $k => $v)
            {
                $v = (is_array($v)) ? json_encode($v) : (is_string($v)?$v:serialize($v));
                
                $list .="$k: {$v}
";
            }
        } elseif (is_string($list))
        {
            $list = $values;
        } else
        {
            $list = serialize($values);
        }

        $log = '===' . date("Y-m-d\TH:i:s") . substr((string) microtime(), 1, 8) . '===
' . $list . '
===';


        $globals = self::getGlobalPaths();
        $partner = ($license != null) ? 'NEW_' . $license : 'NEW_' . $globals[1];

        $path = $globals[0] . $partner . "_" . $name . "_" . date("j.n.Y") . ".log";

        if (self::getSelectEnvironmentConfig() == 'local' && isset($_GET['showLOG']))
        {
            print nl2br($log);
        }


        @file_put_contents($path, $log, FILE_APPEND | LOCK_EX);
    }

    public static function getSelectEnvironmentConfig()
    {

        $env = 'local';

        if (isset($_SERVER['APPLICATION_ENV']))
        {
            if ($_SERVER['APPLICATION_ENV'] == "production")
            {
                $env = 'prod';
            } elseif ($_SERVER['APPLICATION_ENV'] == "staging")
            {
                $env = 'stage';
            }
        } elseif (isset($_SERVER['SERVER_ADDR']))
        {
            if ($_SERVER['SERVER_ADDR'] == "192.168.77.24")
            {
                $env = 'dev';
            }
        }


        return $env;
    }

    public function __destruct()
    {
        //$this->logout($this);


        if (count($this->exceptions) > 0)
        {
            $this->flush();
        }
    }

    public function setGapRequest($gapRequest)
    {
        $this->gapRequest = $gapRequest;
        return $this;
    }

    public function setGapResponse($gapResponse)
    {
        $this->gapResponse = $gapResponse;
        return $this;
    }

    public function setSoapRequest($soapRequest)
    {
        $this->soapRequest = $soapRequest;
        $this->gapRequest = '<textarea>'.$this->soapRequest.'</textarea>';
        return $this;
    }

    public function setSoapResponse($soapResponse)
    {
        $this->soapResponse = $soapResponse;
        $this->gapResponse = '<textarea>'.$this->soapResponse.'</textarea>';
        return $this;
    }

    public function setInternalRequest($internalRequest)
    {
        $this->internalRequest = $internalRequest;
        return $this;
    }

    public function setInternalResponse($internalResponse)
    {
        $this->internalResponse = $internalResponse;
        return $this;
    }


}
