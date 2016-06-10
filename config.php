<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 11/07/14
 * Time: 12:22
 */

class config {

    private $myconfig;

    public function readMyConfig()
    {
        return $this->myconfig;
    }

    public function serializeMe()
    {
        serialize($this);
    }

} 