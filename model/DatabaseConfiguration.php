<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 17/10/14
 * Time: 14:46
 */

namespace model;


class DatabaseConfiguration {

    private $host;
    private $port;
    private $dbUsername;
    private $dbPassword;
    private $dbName;
    private $charSet;

    private $env = array(
        "local" => array(
            "host"          => "212.56.158.25",
            "port"          =>  "3309",
            "username"      => "devel",
            "password"      => "devel4riva",
            "database"      => "irsbo",
            "charset"       => "latin1",
        ),
        "dev" => array(
            "host"          => "masterdb-api.isoftbet.com",
            "port"          =>  "3309",
            "username"      => "devel",
            "password"      => "devel4riva",
            "database"      => "irsbo",
            "charset"       => "latin1",
        ),

        "stage" => array(
            "host"          => "masterdb-api.isoftbet.com",
            "port"          =>  "3307",
            "username"      => "gmapi",
            "password"      => "kf30slfd4",
            "database"      => "irsbo",
            "charset"       => "latin1",
        ),

        "prod" => array(
            "host"          => "masterdb-api.isoftbet.com",
            "port"          =>  "3307",
            "username"      => "gmapi",
            "password"      => "kf30slfd4",
            "database"      => "irsbo",
            "charset"       => "latin1",
        ),
    );


    public function __construct()
    {
        $config = $this->getSelectEnvironmentConfig();
        $this->dbName = $config['database'];
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->dbUsername = $config['username'];
        $this->dbPassword = $config['password'];
        $this->charSet = $config['charset'];
    }


    /**
     * @return mixed
     */
    public function getSelectEnvironmentConfig()
    {

        $config = $this->env['local'];

       if(isset($_SERVER['APPLICATION_ENV']))
       {
           if($_SERVER['APPLICATION_ENV'] == "production")
           {
               $config = $this->env['prod'];
           }
           elseif($_SERVER['APPLICATION_ENV'] == "staging")
           {
               $config = $this->env['stage'];
           }
       }
       elseif(isset($_SERVER['SERVER_ADDR']))
       {
           if($_SERVER['SERVER_ADDR'] == "192.168.77.24")
           {
               $config = $this->env['dev'];
           }
       }


        return $config;

    }



    /**
     * @return mixed
     */
    public function getCharSet()
    {
        return $this->charSet;
    }

    /**
     * @return mixed
     */
    public function getDbUsername()
    {
        return $this->dbUsername;
    }

    /**
     * @return mixed
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @return mixed
     */
    public function getDbPassword()
    {
        return $this->dbPassword;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }



} 