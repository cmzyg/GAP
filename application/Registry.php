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
 * Class Registry
 * @package application
 */
class Registry {

    private  $variables = array();

    /**
     * Dynamic setter
     * @param $index
     * @param $value
     */
    public  function __set($index, $value){
        $this->variables[$index] = $value;
    }

    /**
     * Dynamic getter
     * @param $index
     * @return mixed
     */
    public function __get($index){
        return $this->variables[$index];
    }

}