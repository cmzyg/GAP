<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/10/14
 * Time: 12:09
 */

namespace test\component\configurationmanager;

use component\configurationmanager\ConfigurationManager;


require_once dirname(dirname(dirname(realpath(dirname(__FILE__))))).DIRECTORY_SEPARATOR."bootstrap.php";


class ConfigurationManagerTest extends \PHPUnit_Framework_TestCase{

    private $data = array(
        array(85),
    );

    /**
     * @dataProvider liveLicenseeData
     */
    public function testGetConfiguration($lId)
    {
        $conf = new ConfigurationManager();
        $mng = $conf->loadComponent();

        $config = $mng->getConfiguration($lId);

        $this->assertInstanceOf('\model\gmapi\GmapiLicensee', $config, "Could not load configuration for licensee: ".$lId);
    }

    /**
     * @dataProvider liveLicenseeData
     */
    public function testStoreConfig($lid)
    {
        $path = _SITE_PATH."component".DIRECTORY_SEPARATOR."configurationmanager".DIRECTORY_SEPARATOR."v1".DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR;
        $fileName = $lid.".json";
        $file = $path.$fileName;
        $conf = new ConfigurationManager();
        $mng = $conf->loadComponent();

        $config = $mng->getConfiguration($lid);

        if(file_exists($file))
        {
            $arrayFile = file_get_contents($file);
            $licensee = unserialize($arrayFile);

            $this->assertEquals($config, $licensee, "Saved Configuration is not the same as live Configuration");
        }

    }


    /**
     * @dataProvider liveLicenseeData
     */
    public function testReloadConfig($lid)
    {
        $path = _SITE_PATH."component".DIRECTORY_SEPARATOR."configurationmanager".DIRECTORY_SEPARATOR."v1".DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR;
        $fileName = $lid.".json";
        $file = $path.$fileName;
        $conf = new ConfigurationManager();
        $mng = $conf->loadComponent();

        $mng->getConfiguration($lid);

        if(file_exists($file))
        {
            $config = $mng->reloadConfiguration($lid);
            $arrayFile = file_get_contents($file);
            $licensee = unserialize($arrayFile);

            $this->assertEquals($config, $licensee, "Reloading Configuration failed! configuration was never reloaded");
        }
    }

    public function testSavedServerConfiguration()
    {
        $path = _SITE_PATH."component".DIRECTORY_SEPARATOR."configurationmanager".DIRECTORY_SEPARATOR."v1".DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR;
        $fileName = "servers.json";
        $file = $path.$fileName;
        $conf = new ConfigurationManager();
        $mng = $conf->loadComponent();

        $config = $mng->getServerConfiguration();

        if(file_exists($file))
        {
            $ObjFile = file_get_contents($file);
            $servers = unserialize($ObjFile);

            $this->assertEquals($config, $servers, "Saved Servers Configuration are not the same as live Configuration");
        }
    }


    public function testReloadServerConfig()
    {
        $path = _SITE_PATH."component".DIRECTORY_SEPARATOR."configurationmanager".DIRECTORY_SEPARATOR."v1".DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR;
        $fileName = "servers.json";
        $file = $path.$fileName;
        $conf = new ConfigurationManager();
        $mng = $conf->loadComponent();

        $mng->getServerConfiguration();

        if(file_exists($file))
        {
            $config = $mng->reloadServerConfiguration();
            $arrayFile = file_get_contents($file);
            $licensee = unserialize($arrayFile);

            $this->assertEquals($config, $licensee, "Server Reloading Configuration failed! configuration was never reloaded");
        }
    }


    protected function tearDown()
    {
        foreach($this->data as $licensee)
        {
            $lid = $licensee[0];
            $path = _SITE_PATH."component".DIRECTORY_SEPARATOR."configurationmanager".DIRECTORY_SEPARATOR."v1".DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR;
            $fileName = $lid.".json";
            $file = $path.$fileName;
            $file2 = $path."servers.json";

            if(file_exists($file))
            {
                @unlink($file);
            }

            if(file_exists($file2))
            {
                @unlink($file2);
            }
        }

    }

    public function liveLicenseeData()
    {
        return $this->data;
    }

} 