<?php

/**
 * Description of AbstractAccountingsEntity
 *
 * @author rafal
 */

namespace component\accounting\v1\entities;

use exceptions\AccountingException;
use exceptions\Exception;
use model\GMAPIDataRepository;

abstract class AbstractAccountingEntity {

    /**
     *
     * @var GMAPIDataRepository
     */
    protected $repo;

    /**
     *
     * @var null|\component\request\Request|\component\request\BeforeLoginRequest|\component\request\PlaceBetRequest|\component\request\PlaceAndSettleBetRequest|\component\request\SettleBetRequest
     */
    protected $request;
    protected $table = NULL;
    protected $id = NULL;
    protected $arrFields;
    protected $arrUpdated;

    public function __construct($request, $id = NULL)
    {
        $this->request = $request;
        $dbName = $this->request->getLicenseeObject()->getDbPrefix() . '_accounting';
        $this->repo = new GMAPIDataRepository($dbName);
        
        
        if ($this->request->getProviderName() != '')
        {
            GMAPIDataRepository::$dbProviderPrefix = $this->request->getProviderName() . '_';
            $this->table = $this->request->getProviderName() . '_' . $this->table;
        }

        if ($id != NULL)
        {
            $result = $this->repo->fetchAccountings($this->table, $id);
            if (is_array($result))
            {
                foreach ($result as $k => $v)
                {
                    $k = $this->toCamelCase($k);
                    $this->{$k} = $v;
                }
            } else
            {
                throw new \Exception("Fetch in Constructor Failed");
            }
        }
        return $this;
    }

    private function toCamelCase($str)
    {
        $strCamel = preg_replace_callback('/_+([a-z])/', create_function('$c', 'return strtoupper($c[1]);'), strtolower($str));
        $strCamelwithDollar = preg_replace('/(\w+)/i', '\\1', $strCamel);
        return $strCamelwithDollar;
    }

    private function toUnderscoreCase($string)
    {
        return strtolower(trim(preg_replace(array('/([A-Z][a-z])/', '/\$/'), array('_\\1', ''), $string), '_'));
    }

    public function insert()
    {
        $output = true;

        if (count($this->arrFields) > 0)
        {
            $fields = array();
            foreach (array_unique($this->arrFields) as $f)
            {
                $fields[$this->toUnderscoreCase($f)] = $this->{$f};
            }

            $output = $this->repo->insertAccountings($this->table, $fields);
        }

       if($output === false) throw new \Exception("Unhandled Accounting Exception");
    }

    public function fetch(array $conditions)
    {
        $result = $this->repo->fetchAccountingsBy($this->table, $conditions);
        if (is_array($result))
        {
            foreach ($result as $k => $v)
            {
                $k = $this->toCamelCase($k);
                $this->{$k} = $v;
            }
        }

        if($result === false) throw new \Exception("Unhandled Accounting Exception");
    }

    public function update()
    {
        $output = true;

        if (count($this->arrFields) > 0)
        {
            $fields = array();
            foreach (array_unique($this->arrFields) as $f)
            {
                $fields[$this->toUnderscoreCase($f)] = $this->{$f};
            }

            $output = $this->repo->updateAccountings($this->table, $fields, array('id' => $this->id));
        }

        if($output === false) throw new \Exception("Unhandled Accounting Exception");
    }

    public function delete()
    {
        
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return GMAPIDataRepository
     */
    public function getRepo()
    {
        return $this->repo;
    }



}
