<?php

namespace model\gmapi;

/**
 * GmapiExtraConfiguration
 *
 * @Table(name="gap_extra_configuration", indexes={@Index(name="fk_extra_configuration_1_idx", columns={"license_id"})})
 * @Entity(repositoryClass="model\gmapi\GmapiRepository")
 */
class GmapiExtraConfiguration
{
    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="configuration_name", type="string", length=45, nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $configurationName;

    /**
     * @var string
     *
     * @Column(name="configuration", type="text", nullable=false)
     */
    private $configuration;

    /**
     * @var GmapiLicensee
     *
     * @Id
     * @GeneratedValue(strategy="NONE")
     * @OneToOne(targetEntity="GmapiLicensee")
     * @JoinColumns({
     *   @JoinColumn(name="license_id", referencedColumnName="id_licensee")
     * })
     */
    private $license;


    /**
     * Set id
     *
     * @param integer $id
     * @return GmapiExtraConfiguration
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * @return GmapiExtraConfiguration
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
     * Set configuration
     *
     * @param string $configuration
     * @return GmapiExtraConfiguration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Get configuration
     *
     * @return string 
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set license
     *
     * @param GmapiLicensee $license
     * @return GmapiExtraConfiguration
     */
    public function setLicense(GmapiLicensee $license)
    {
        $this->license = $license;

        return $this;
    }

    /**
     * Get license
     *
     * @return GmapiLicensee
     */
    public function getLicense()
    {
        return $this->license;
    }
}
