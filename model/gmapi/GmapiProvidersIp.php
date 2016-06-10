<?php
/**
 * Created by PhpStorm.
 * User: anna.rabiej
 * Date: 30/12/2014
 * Time: 15:39
 */
namespace model\gmapi;

/**
 * GmapiProviderIp
 *
 * @Table(name="gap_providers_ip", indexes={@Index(name="FK_gap_providers_ip_gap_providers", columns={"id_provider"})})
 * @Entity(repositoryClass="model\gmapi\GmapiRepository")
 */
class GmapiProvidersIp
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
     * @var GmapiProviders
     *
     * @ManyToOne(targetEntity="GmapiProviders")
     * @JoinColumns({
     *   @JoinColumn(name="id_provider", referencedColumnName="id")
     * })
     */

    private $idProvider;

    /**
     * @var string
     *
     * @Column(name="ip", type="string", length=100, nullable=false)
     */
    private $ip;

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
     * @return GmapiProviders
     */
    public function getIdProvider()
    {
        return $this->idProvider;
    }

    /**
     * @param GmapiProviders $idProvider
     */
    public function setIdProvider($idProvider)
    {
        $this->idProvider = $idProvider;
    }



    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }






}
