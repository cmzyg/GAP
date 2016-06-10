<?php

namespace model\gmapi;

/**
 * GmapiServerConfiguration
 *
 * @Table(name="gap_server_configuration", indexes={@Index(name="fk_server_configuration_1_idx", columns={"server"})})
 * @Entity(repositoryClass="model\gmapi\GmapiRepository")
 */
class GmapiServerConfiguration
{
    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="configuration_name", type="string", length=45, nullable=false)
     */
    private $configurationName;

    /**
     * @var string
     *
     * @Column(name="configuration_value", type="string", length=45, nullable=false)
     */
    private $configurationValue;

    /**
     * @var GmapiServers
     *
     * @ManyToOne(targetEntity="GmapiServers")
     * @JoinColumns({
     *   @JoinColumn(name="server", referencedColumnName="id")
     * })
     */
    private $server;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set configurationName
     *
     * @param string $configurationName
     * @return GmapiServerConfiguration
     */
    public function setConfigurationName($configurationName)
    {
        $this->configurationName = $configurationName;

        return $this;
    }

    /**
     * Get configurationName
     *
     * @return string 
     */
    public function getConfigurationName()
    {
        return $this->configurationName;
    }

    /**
     * Set configurationValue
     *
     * @param string $configurationValue
     * @return GmapiServerConfiguration
     */
    public function setConfigurationValue($configurationValue)
    {
        $this->configurationValue = $configurationValue;

        return $this;
    }

    /**
     * Get configurationValue
     *
     * @return string 
     */
    public function getConfigurationValue()
    {
        return $this->configurationValue;
    }

    /**
     * Set server
     *
     * @param GmapiServers $server
     * @return GmapiServerConfiguration
     */
    public function setServer(GmapiServers $server = null)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get server
     *
     * @return GmapiServers
     */
    public function getServer()
    {
        return $this->server;
    }
}
