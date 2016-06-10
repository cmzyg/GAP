<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 05/01/15
 * Time: 16:58
 */

namespace component\soap;


use component\logger\Logger;
use exceptions\GMAPIGeneralException;
use exceptions\ValidationException;

class ErrorObserver {

    private static $container;

    public static function attach($item)
    {
        if(!is_array(ErrorObserver::$container))
        {
            ErrorObserver::$container = array($item);
        }
        else
        {
            ErrorObserver::$container[] = $item;
        }
    }

    public static function notify($item, $errorType)
    {
        foreach(ErrorObserver::$container as $obj)
        {
            if($obj instanceof $item)
            {
                ErrorObserver::writeError($item, $errorType);
            }
        }
    }


    protected static function writeError($item, $errorType)
    {
        switch($errorType)
        {
            case "validation":
                Logger::getLogger()->addException(new ValidationException($item->getFault(), 1, 3));
                break;
            case "token":
                Logger::getLogger()->addException(new ValidationException($item->getFault(), 1, 4));
                break;
            case "queryresult":
                Logger::getLogger()->addException(new GMAPIGeneralException($item->getFault(), 2));
                break;
        }
    }


} 