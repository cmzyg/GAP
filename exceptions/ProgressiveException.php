<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/07/14
 * Time: 14:16
 */

namespace exceptions;

use exceptions\Exception as Exception;

class ProgressiveException extends Exception {

    // CLEN NEEDED!!
    protected $subtypes = array(
        0 => "No Subtype Specified",
        1 => "Create JPC",
        2 => "MS/MSsp",
        3 => "Update JPC",
        4 => "Update JPW",
        5 => "Read JP Value",
    );
    protected $customSummary = array(
        1 => "Calculate JPC Error",
        2 => array("Calculate JPC Error", "Update JPC Error", "Update JPW Error", "Read JP Error"),
        3 => "Update JPC Error",
        4 => "Update JPW Error",
        5 => "Read JP Error"
    );

    /**
     * @param string $message
     * @param int $code
     * @param null $summaryLevel
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, $summaryLevel = null, \Exception $previous = null)
    {
        $this->type = "PROGRESSIVE";

        // make sure everything is assigned properly
        parent::__construct($message, $code, $summaryLevel, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
