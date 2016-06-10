<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 08/01/15
 * Time: 10:28
 */

namespace component\communication;


class ErrorList {

    public static $sessionExist = false;

    public static $insufficientFund = false;

    public static $duplicateRoundId = false;

    public static $validationError = false;

    public static $invalidRoundId = false;

    public static $userNotFound = false;

    public static $userBlocked = false;

    public static $failedRequest = false;

    public static $cancelNotPossible = false;

    /**
     * Resets all error list
     */
    public static function resetErrors()
    {
        ErrorList::$sessionExist = false;
        ErrorList::$insufficientFund = false;
        ErrorList::$duplicateRoundId = false;
        ErrorList::$validationError = false;
        ErrorList::$invalidRoundId = false;
        ErrorList::$userNotFound = false;
        ErrorList::$userBlocked = false;
        ErrorList::$failedRequest = false;
        ErrorList::$cancelNotPossible = false;
    }
} 