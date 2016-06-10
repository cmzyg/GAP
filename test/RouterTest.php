<?php

/**
 * @author Samuel
 * @copyright 2014
 */

namespace test;

use application\Router;
use controller\indexController;

require_once dirname(basename(__DIR__)).DIRECTORY_SEPARATOR."bootstrap.php";

class RouterTest extends \PHPUnit_Framework_TestCase{

    public function testEmpty()
    {
        $stack = array();
        $this->assertEmpty($stack);

        return $stack;
    }

    public function testController()
    {
        $router = new Router(new \stdClass());
        $router->loadController("index", _SITE_PATH."controller/");

        $this->assertTrue($router->clClass instanceof indexController, "Failed to create requested controller");
    }

} 