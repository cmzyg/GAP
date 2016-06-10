<?php
namespace model\gmapi;

/**
 * GmapiWalletOptions
 *
 * @Table(name="gap_wallet_options", indexes={@Index(name="fk_wallet_options_1_idx", columns={"wallet"})})
 * @Entity(repositoryClass="model\gmapi\GmapiRepository")
 */
class GmapiWalletOptions
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
     * @Column(name="wallet_option", type="string", length=45, nullable=false)
     */
    private $walletOption;

    /**
     * @var string
     *
     * @Column(name="option_value", type="string", length=255, nullable=false)
     */
    private $optionValue;

    /**
     * @var GmapiWallet
     *
     * @ManyToOne(targetEntity="GmapiWallet")
     * @JoinColumns({
     *   @JoinColumn(name="wallet", referencedColumnName="id")
     * })
     */
    private $wallet;


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
     * Set walletOption
     *
     * @param string $walletOption
     * @return GmapiWalletOptions
     */
    public function setWalletOption($walletOption)
    {
        $this->walletOption = $walletOption;

        return $this;
    }

    /**
     * Get walletOption
     *
     * @return string 
     */
    public function getWalletOption()
    {
        return $this->walletOption;
    }

    /**
     * Set optionValue
     *
     * @param string $optionValue
     * @return GmapiWalletOptions
     */
    public function setOptionValue($optionValue)
    {
        $this->optionValue = $optionValue;

        return $this;
    }

    /**
     * Get optionValue
     *
     * @return string 
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

    /**
     * Set wallet
     *
     * @param GmapiWallet $wallet
     * @return GmapiWalletOptions
     */
    public function setWallet(GmapiWallet $wallet = null)
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Get wallet
     *
     * @return GmapiWallet
     */
    public function getWallet()
    {
        return $this->wallet;
    }
}
