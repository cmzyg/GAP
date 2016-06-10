<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 05/01/15
 * Time: 10:06
 */

namespace exceptions;


class SoapException extends Exception{

    protected $status = "CRASHED";
    protected $subtypes = array(
        0 => "No Subtype Specified",
        1 => "Invalid Licensee",
        2 => "No WSDL File",

    );
    protected $customSummary = array(
        1 => "No Licensee with Id",
        2 => "No WSDL file found for provider",

    );

    /**
     * @param string $message
     * @param int $code
     * @param null $summaryLevel
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, $summaryLevel = null, \Exception $previous = null)
    {
        $this->type = "SOAP";

        // make sure everything is assigned properly
        parent::__construct($message, $code, $summaryLevel, $previous);
    }

} 