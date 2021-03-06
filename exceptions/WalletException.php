<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/07/14
 * Time: 11:52
 */

namespace exceptions;
use exceptions\Exception as Exception;

class WalletException extends Exception{
    
    protected $subtypes = array(
        0 => "No Subtype Specified",
        1 => "Connection",
        2 => "Incorrect Format",
        3 => "HTTP",
        4 => "MS/MSsp",
    );

    protected  $customSummary = array(
        1 => "CURL/SOAP Error",
        2 => "Invalid Response",
        3 => "HTTP Code Error",
        4 => "Validation Error",
    );

    /**
     * @param string $message
     * @param int $code
     * @param null $summaryLevel
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, $summaryLevel = null, \Exception $previous = null) {
        $this->type = "WALLET";

        // make sure everything is assigned properly
        parent::__construct($message, $code,  $summaryLevel, $previous);
    }

} 