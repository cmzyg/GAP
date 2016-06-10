<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 02/09/14
 * Time: 12:49
 */

namespace exceptions;

use exceptions\Exception as Exception;

class GMAPIGeneralException extends Exception {

    protected $status = "CONTINUED";
    protected $subtypes = array(
        0 => "No Subtype Specified",
        1 => "Database Failure",
        2 => "Database result",
        3 => "Config Cache File"
    );
    protected $customSummary = array(
        1 => "Database Connection/Query Failed",
        2 => "Expected Query result not found",
        3 => "Cache file for configuration not found"
    );

    /**
     * @param string $message
     * @param int $code
     * @param null $summaryLevel
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, $summaryLevel = null, \Exception $previous = null)
    {
        $this->type = "GMAPI GENERAL ERROR";

        // make sure everything is assigned properly
        parent::__construct($message, $code, $summaryLevel, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
