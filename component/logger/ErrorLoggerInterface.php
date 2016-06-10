<?php

/**
 *
 * @author rafal
 */

namespace component\logger;

interface ErrorLoggerInterface {

    /**

     * Method to store all occured Errors 
     * @param \Exception $ex
     * @return boolean - Returns true on success
     */
    public function addException(\Exception $ex);

    /**
     * Called when component is starting to do something
     * @param type $name
     * @return boolean - success or not
     */
    public function login(&$class);

    /**
     * Called when component finished its task
     * @param type $name
     * @return boolean - success or not
     * 
     */
    public function logout(&$class);

    /**
     * 
     * @param type $error - MySQL Error that occurred
     * @param type $query - Query that caused an Error to occurr
     */
    public function addMySQLError($error, $query);
}
