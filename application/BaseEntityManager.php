<?php
/**
 *
 * Simple MVC Framework aka Baseframework
 * @version 2.0
 *
 * Created By Zygimantas Simkus
 * Copyright 2014 Plati Tech Limited, All Rights Reserved
 */

namespace application;

/**
 * Interface BaseEntityManager
 * @package application
 */
interface BaseEntityManager {

    /**
     * Reconfigures the entity manager to a new entity, proxy directory and namespace
     * @return void
     */
    public function reconfigure();

    /**
     * Creates a new entity manager or returns an existing instance
     * @return mixed
     */
    public static function createEntityManager();

} 