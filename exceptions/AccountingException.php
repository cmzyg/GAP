<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/07/14
 * Time: 10:55
 */

namespace exceptions;

use exceptions\Exception as Exception;

class AccountingException extends Exception {

    protected $status = "CONTINUED";
    protected $subtypes = array(
        0 => "No Subtype Specified",
        1 => "Create session",
        2 => "MS/MSsp",
        3 => "Update sess",
        4 => "Create prog",
        5 => "Create wager",
        6 => "Read sess",
        7 => "Read wager",
    );
    protected $customSummary = array(
        1 => "Create Session Error",
        2 => array("Create Session Error",
            "Update Session Error",
            "Create Wager Error",
            "Create Progressive Error",
            "Create Game Error",
            "Update Game Error",
            "Update Wager Error",
            "Read session Error",
            "Recon Error",
            "Read bet amount error",
        ),
        3 => "Update sess error",
        4 => "Create prog error",
        5 => "Create wager",
        6 => "Read sess error",
        7 => "Read wager error",
    );

    /**
     * @param string $message
     * @param int $code
     * @param null $summaryLevel
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, $summaryLevel = null, \Exception $previous = null)
    {
        $this->type = "ACCOUNTING";

        // make sure everything is assigned properly
        parent::__construct($message, $code, $summaryLevel, $previous);
    }

}
