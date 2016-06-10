<?php
/**
 *
 * Simple MVC Framework aka Baseframework
 * @version 2.0
 *
 * Created By Zygimantas Simkus
 * Copyright 2014 Plati Tech Limited, All Rights Reserved
 */

namespace model;



class DatabaseFactory {

    /**
     * @var null|\PDO|\mysqli
     */
    private static $databaseInstance = null;

    private static $databaseName;


    private function __construct(){

    }

    /**
     * @param Database $dbObject
     * @param bool $new
     * @return \mysqli|null|\PDO
     */
    public static function createDatabase(Database $dbObject, $new = false)
    {
        $conn = null;

         if(self::$databaseInstance == null || $new == true && $dbObject->getDbName() !== self::$databaseName)
         {
             try
             {
                 $dbUtil = new DatabaseUtil($dbObject);
                 if(strtoupper($dbObject->getDbDriver()) == "MYSQLI")
                 {
                    self::$databaseInstance = $dbUtil->mysqliConnection();
                    self::$databaseName = $dbObject->getDbName();
                    return self::$databaseInstance;
                 }
                 else
                 {
                     self::$databaseInstance = $dbUtil->pdoConnection();
                     self::$databaseName = $dbObject->getDbName();
                     return self::$databaseInstance;
                 }
             }
             catch (\Exception $ex)
             {
                 return null;
             }
         }

        return self::$databaseInstance;

    }


    private function __clone()
    {

    }

}