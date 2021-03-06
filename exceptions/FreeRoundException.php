<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 18/08/14
 * Time: 14:29
 */

namespace exceptions;

use exceptions\Exception as Exception;

class FreeRoundException extends Exception {

    protected $status = "CONTINUED";
    protected $subtypes = array(
        0 => "No Subtype Specified",
        1 => "MS/MSsp",
        2 => "BackOffice Configuration",
    );
    protected $customSummary = array(
        1 => array("Check provider problem", "Read FR problem", "Update FR problem"),
        2 => array("Duplicate user", "No package for user")
    );

    /**
     * @param string $message
     * @param int $code
     * @param null $summaryLevel
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, $summaryLevel = null, \Exception $previous = null)
    {
        $this->type = "FREEROUND";

        // make sure everything is assigned properly
        parent::__construct($message, $code, $summaryLevel, $previous);
    }

}
