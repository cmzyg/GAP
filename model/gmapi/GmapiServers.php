<?php

namespace model\gmapi;

/**
 * GmapiServers
 *
 * @Table(name="gap_servers")
 * @Entity(repositoryClass="model\gmapi\GmapiRepository")
 */
class GmapiServers
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
     * @Column(name="server_name", type="string", length=45, nullable=false)
     */
    private $serverName;


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
     * Set serverName
     *
     * @param string $serverName
     * @return GmapiServers
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;

        return $this;
    }

    /**
     * Get serverName
     *
     * @return string 
     */
    public function getServerName()
    {
        return $this->serverName;
    }
}
