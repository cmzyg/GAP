<?php

namespace model\gmapi;

/**
 * GmapiGames
 *
 * @Table(name="gmapi_games", indexes={@Index(name="fk_gmapi_games_1_idx", columns={"category"}), @Index(name="fk_gmapi_games_2_idx", columns={"type"})})
 * @Entity(repositoryClass="model\gmapi\GmapiRepository")
 */
class GmapiGames
{
    /**
     * @var integer
     *
     * @Column(name="skin_id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $skinId;

    /**
     * @var integer
     *
     * @Column(name="game_id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $gameId;

    /**
     * @var string
     *
     * @Column(name="name", type="string", length=140, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @Column(name="progessive_levels", type="string", length=45, nullable=true)
     */
    private $progessiveLevels;

    /**
     * @var GmapiGameCategory
     *
     * @ManyToOne(targetEntity="GmapiGameCategory")
     * @JoinColumns({
     *   @JoinColumn(name="category", referencedColumnName="id")
     * })
     */
    private $category;

    /**
     * @var GmapiGameType
     *
     * @ManyToOne(targetEntity="GmapiGameType")
     * @JoinColumns({
     *   @JoinColumn(name="type", referencedColumnName="id")
     * })
     */
    private $type;


    /**
     * Set skinId
     *
     * @param integer $skinId
     * @return GmapiGames
     */
    public function setSkinId($skinId)
    {
        $this->skinId = $skinId;

        return $this;
    }

    /**
     * Get skinId
     *
     * @return integer 
     */
    public function getSkinId()
    {
        return $this->skinId;
    }

    /**
     * Set gameId
     *
     * @param integer $gameId
     * @return GmapiGames
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * Get gameId
     *
     * @return integer 
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return GmapiGames
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set progessiveLevels
     *
     * @param string $progessiveLevels
     * @return GmapiGames
     */
    public function setProgessiveLevels($progessiveLevels)
    {
        $this->progessiveLevels = $progessiveLevels;

        return $this;
    }

    /**
     * Get progessiveLevels
     *
     * @return string 
     */
    public function getProgessiveLevels()
    {
        return $this->progessiveLevels;
    }

    /**
     * Set category
     *
     * @param GmapiGameCategory $category
     * @return GmapiGames
     */
    public function setCategory(GmapiGameCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return GmapiGameCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set type
     *
     * @param GmapiGameType $type
     * @return GmapiGames
     */
    public function setType(GmapiGameType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return GmapiGameType
     */
    public function getType()
    {
        return $this->type;
    }
}
