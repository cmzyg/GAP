<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/07/14
 * Time: 14:25
 */

namespace exceptions;

use exceptions\Exception as Exception;

class SaveScreenException extends Exception {

    protected $subtypes = array(
        0 => "No Subtype Specified",
        1 => "Read Screen",
        2 => "MS/MSsp",
        3 => "Save Screen",
    );
    protected $customSummary = array(
        1 => "Read Screen Error",
        2 => array("Read Screen Error", "Save Screen Error"),
        3 => "Save Screen Error",
    );

    /**
     * @param string $message
     * @param int $code
     * @param null $summaryLevel
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, $summaryLevel = null, \Exception $previous = null)
    {
        $this->type = "SAVESCREEN";

        // make sure everything is assigned properly
        parent::__construct($message, $code, $summaryLevel, $previous);
    }

}
