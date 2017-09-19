<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Task entity for app.
 * 
 * @ORM\Entity(
 *     repositoryClass="AppBundle\Repository\TaskRepository"
 * )
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="efit_task")
 */
class Task
{
    const TYPE_INTEREST = 'INTEREST';
    const TYPE_DIVERSIFICATION = 'DIVERSIFICATION';

    const TYPE_RISK = 'RISK';
    const TYPE_ANCHORING = 'ANCHORING';
    const TYPE_MENTAL_BOOKKEEPING = 'MENTAL_BOOKKEEPING';
    const TYPE_FRAMING = 'FRAMING';

    const TYPE_SAVINGS_TARGET = 'SAVINGS_TARGET';
    const TYPE_SAVINGS_SUPPORTED = 'SAVINGS_SUPPORTED';
    const TYPE_SELF_COMMITMENT = 'SELF_COMMITMENT';
    const TYPE_PROCRASTINATION = 'PROCRASTINATION';

    const BLOCK_SELF_CONTROL = 'SELF_CONTROL';
    const BLOCK_FINANCIAL_KNOWLEDGE = 'FINANCIAL_KNOWLEDGE';
    const BLOCK_CONSUMER_BEHAVIOUR = 'CONSUMER_BEHAVIOUR';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @JMS\Groups({"backend", "frontend", "deserialize"})
     */
    private $id;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     * @JMS\Groups({"backend","frontend", "deserialize"})
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @JMS\Groups({"backend","frontend", "deserialize"})
     */
    private $block;

    /**
     * @var Admin
     *
     * @ORM\ManyToOne(
     *     targetEntity="Workshop"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $workshop;

    /**
     * @var Results[]|ArrayCollection
     * 
     * @ORM\OneToMany(
     *     targetEntity="Result", 
     *     mappedBy="task"
     * )
     */
    private $results;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="is_active")
     * @JMS\Groups({"backend","frontend","deserialize"})
     */
    private $isActive = 0;

    /**
     * Initializes `results` collection.
     */
    public function __construct()
    {
        $this->results = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $type = $this->getType();

        $block = self::getBlockByType($type);
        $this->setBlock($block);

        $dateTime = new \DateTime();
        $this->setCreatedAt($dateTime);
        $this->setUpdatedAt($dateTime);

        $this->results = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("createdAt")
     * @JMS\Groups({"backend", "frontend"})
     */
    public function getCreatedAtTimeStamp()
    {
        return $this->createdAt->getTimestamp();
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("updatedAt")
     * @JMS\Groups({"backend", "frontend"})
     */
    public function getUpdatedAtTimeStamp()
    {
        return $this->updatedAt->getTimestamp();
    }

    /**
     * If task is playable only in workshop.
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("isWorkshopOnly")
     * @JMS\Groups({"backend", "frontend"})
     */
    public function getIsWorkshopOnly()
    {
        return self::getIsWorkshopOnlyByType($this->getType());
    }

    /**
     * If task has submittable results.
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("isInteractive")
     * @JMS\Groups({"backend", "frontend"})
     */
    public function getIsInteractive()
    {
        return self::getIsInteractiveByType($this->getType());
    }

    /**
     * Counts amount of current tickets.
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("hasEvaluation")
     * @JMS\Groups({"backend"})
     */
    public function getHasEvaluation()
    {
        return self::getHasEvaluationByType($this->getType());
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isActive
     *
     * @param string $isActive
     *
     * @return Workshop
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Task
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set block
     *
     * @param string $block
     *
     * @return Task
     */
    public function setBlock($block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Get block
     *
     * @return string
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set workshop
     *
     * @param \AppBundle\Entity\Workshop $workshop
     *
     * @return Task
     */
    public function setWorkshop(\AppBundle\Entity\Workshop $workshop)
    {
        $this->workshop = $workshop;

        return $this;
    }

    /**
     * Get workshop
     *
     * @return \AppBundle\Entity\Workshop
     */
    public function getWorkshop()
    {
        return $this->workshop;
    }

    /**
     * Add result
     *
     * @param \AppBundle\Entity\Result $result
     *
     * @return Task
     */
    public function addResult(\AppBundle\Entity\Result $result)
    {
        $this->results[] = $result;

        return $this;
    }

    /**
     * Remove result
     *
     * @param \AppBundle\Entity\Result $result
     */
    public function removeResult(\AppBundle\Entity\Result $result)
    {
        $this->results->removeElement($result);
    }

    /**
     * Get results
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Task
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Task
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Provides all supported blocks for `Task` entity.
     * Used to determine if `User` has finished whole
     * round (= all tasks of all blocks) for `Stats`.
     *
     * @static
     * @return array
     */
    public static function getBlocks()
    {
        return [
            self::BLOCK_CONSUMER_BEHAVIOUR,
            self::BLOCK_SELF_CONTROL,
            self::BLOCK_FINANCIAL_KNOWLEDGE,
        ];
    }

    /**
     * Provides all supported types for `Task` entity.
     * Used to implicitly create corresponding `Task`
     * entities each time a new `Workshop` is created.
     *
     * @static
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_RISK,
            self::TYPE_ANCHORING,
            self::TYPE_MENTAL_BOOKKEEPING,
            self::TYPE_FRAMING,

            self::TYPE_SAVINGS_TARGET,
            self::TYPE_SAVINGS_SUPPORTED,
            self::TYPE_SELF_COMMITMENT,
            self::TYPE_PROCRASTINATION,

            self::TYPE_INTEREST,
            self::TYPE_DIVERSIFICATION,
        ];
    }

    /**
     * Provides the connected types for `block`.
     * This is used upon creation of `Result` to
     * determine if `User` has finished all tasks
     * of `block` in order to update `Stats`.
     *
     * @static
     * @param string $block
     * @return array
     */
    public static function getTasksByBlock($block)
    {
        switch($block) {
            case self::BLOCK_CONSUMER_BEHAVIOUR:
                return [
                    self::TYPE_RISK,
                    self::TYPE_ANCHORING,
                    self::TYPE_MENTAL_BOOKKEEPING,
                    self::TYPE_FRAMING,
                ];
                
            case self::BLOCK_SELF_CONTROL:
                return [
                    self::TYPE_SAVINGS_TARGET,
                    self::TYPE_SAVINGS_SUPPORTED,
                    self::TYPE_SELF_COMMITMENT,
                    self::TYPE_PROCRASTINATION,
                ];

            case self::BLOCK_FINANCIAL_KNOWLEDGE:
                return [
                    self::TYPE_INTEREST,
                    self::TYPE_DIVERSIFICATION,
                ];
                
            default:
                return [];
        }
    }

    /**
     * Provides the connected `block` property for type.
     * This is used to update `Stats` for `User` after a
     * `Result` entity is created for updating `Ticket` 
     * relation and `state` property of `User` entity.
     *
     * @static
     * @param string $type
     * @return array
     */
    public static function getBlockByType($type)
    {
        switch($type) {
            case self::TYPE_RISK:
            case self::TYPE_ANCHORING:
            case self::TYPE_MENTAL_BOOKKEEPING:
            case self::TYPE_FRAMING:
                return self::BLOCK_CONSUMER_BEHAVIOUR;

            case self::TYPE_SAVINGS_TARGET:
            case self::TYPE_SAVINGS_SUPPORTED:
            case self::TYPE_SELF_COMMITMENT:
            case self::TYPE_PROCRASTINATION:
                return self::BLOCK_SELF_CONTROL;

            case self::TYPE_INTEREST:
            case self::TYPE_DIVERSIFICATION:
                return self::BLOCK_FINANCIAL_KNOWLEDGE;

            default:
                return null;
        }
    }

    /**
     * Provides information if this task is only played while
     * `Workshop` flag `isActive` is set to true meaning that
     * `User` can only play during workshop, but not at home.
     *
     * @static
     * @param string $type
     * @return boolean
     */
    public static function getIsWorkshopOnlyByType($type)
    {
        switch($type) {
            case self::TYPE_ANCHORING:
            case self::TYPE_MENTAL_BOOKKEEPING:
            case self::TYPE_SAVINGS_TARGET:
                return true;

            default:
                return false;
        }
    }

    /**
     * Provides information if this task should be ignored
     * from updating `Stats` table (e.g. if there's actual
     * no result for text-only exercises etc.)
     *
     * @static
     * @param string $type
     * @return boolean
     */
    public static function getIsInteractiveByType($type)
    {
        switch($type) {
            case self::TYPE_SAVINGS_SUPPORTED:
                return false;

            default:
                return true;
        }
    }

    /**
     * Provides predefined setting for `hasEvaluation` flag.
     * Used to provide analysis for `Admin` in backend view.
     *
     * @static
     * @param string $type
     * @return boolean
     */
    public static function getHasEvaluationByType($type)
    {
        switch($type) {
            case self::TYPE_ANCHORING:
            case self::TYPE_MENTAL_BOOKKEEPING:
            case self::TYPE_PROCRASTINATION:
                return true;

            default:
                return false;
        }
    }
}
