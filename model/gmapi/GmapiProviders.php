<?php
/**
 * Created by PhpStorm.
 * User: anna.rabiej
 * Date: 30/12/2014
 * Time: 15:39
 */
namespace model\gmapi;

/**
 * GmapiProviders
 *
 * @Table(name="gap_providers")
 * @Entity(repositoryClass="model\gmapi\GmapiRepository")
 */
class GmapiProviders
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
     * @Column(name="provider_name", type="string", length=250, nullable=false)
     */
    private $providerName;

    /**
     * @var string
     *
     * @Column(name="provider_api_type", type="string", length=4, nullable=false)
     */
    private $providerApiType;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @param string $providerName
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
    }

    /**
     * @return string
     */
    public function getProviderApiType()
    {
        return $this->providerApiType;
    }

    /**
     * @param string $providerApiType
     */
    public function setProviderApiType($providerApiType)
    {
        $this->providerApiType = $providerApiType;
    }



}
