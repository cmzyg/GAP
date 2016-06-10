<?php

namespace model\gmapi\proxy\__CG__\model\gmapi;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class GmapiGames extends \model\gmapi\GmapiGames implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'skinId', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'gameId', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'name', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'progessiveLevels', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'category', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'type');
        }

        return array('__isInitialized__', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'skinId', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'gameId', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'name', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'progessiveLevels', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'category', '' . "\0" . 'model\\gmapi\\GmapiGames' . "\0" . 'type');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (GmapiGames $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function setSkinId($skinId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSkinId', array($skinId));

        return parent::setSkinId($skinId);
    }

    /**
     * {@inheritDoc}
     */
    public function getSkinId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getSkinId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSkinId', array());

        return parent::getSkinId();
    }

    /**
     * {@inheritDoc}
     */
    public function setGameId($gameId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGameId', array($gameId));

        return parent::setGameId($gameId);
    }

    /**
     * {@inheritDoc}
     */
    public function getGameId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getGameId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGameId', array());

        return parent::getGameId();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', array($name));

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', array());

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setProgessiveLevels($progessiveLevels)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProgessiveLevels', array($progessiveLevels));

        return parent::setProgessiveLevels($progessiveLevels);
    }

    /**
     * {@inheritDoc}
     */
    public function getProgessiveLevels()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProgessiveLevels', array());

        return parent::getProgessiveLevels();
    }

    /**
     * {@inheritDoc}
     */
    public function setCategory(\model\gmapi\GmapiGameCategory $category = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCategory', array($category));

        return parent::setCategory($category);
    }

    /**
     * {@inheritDoc}
     */
    public function getCategory()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCategory', array());

        return parent::getCategory();
    }

    /**
     * {@inheritDoc}
     */
    public function setType(\model\gmapi\GmapiGameType $type = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setType', array($type));

        return parent::setType($type);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getType', array());

        return parent::getType();
    }

}
