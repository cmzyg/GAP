<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 26/09/14
 * Time: 15:55
 */

namespace test;

use component\EntityManager;

require_once dirname(basename(__DIR__)).".".DIRECTORY_SEPARATOR."bootstrap.php";

class EntityManagerTest extends \PHPUnit_Framework_TestCase{

    /**
     * @test
     */

    public function testEntityManagerIsActive()
    {
        $this->assertTrue((EntityManager::createEntityManager() instanceof \Doctrine\ORM\EntityManager), "Entity Manager is not active");
    }

    /**
     * @test
     */
    public function testEntityManagerIsClosed()
    {
        $em = EntityManager::createEntityManager();
        $em->close();
        $this->assertTrue(!($em->isOpen()), "Entity Manager is still active");
    }

    /**
     * @test
     */
    public function testOnlyOneInstanceOfEntityManagerExist()
    {
        $em1 = EntityManager::createEntityManager();
        $em2 = EntityManager::createEntityManager();
        $this->assertTrue(($em1 === $em2), "Failed to use one instance of the Entity Manager");
    }

} 