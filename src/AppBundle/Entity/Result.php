<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Result entity for app.
 * 
 * @ORM\Entity(
 *     repositoryClass="AppBundle\Repository\ResultRepository"
 * )
 * @ORM\Table(name="efit_result")
 * @ORM\HasLifecycleCallbacks()
 */
class Result
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @JMS\Groups({"backend", "frontend"})
     */
    private $id;

    /**
     * @ORM\Column(type="json_array")
     * @JMS\Groups({"backend", "frontend", "deserialize"})
     */
    private $json;

    /**
     * @var User
     *
     * @ORM\ManyToOne(
     *     targetEntity="User"
     * )
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Groups({"backend", "frontend"})
     * @JMS\MaxDepth(1)
     */
    private $user;

    /**
     * @var Task
     *
     * @ORM\ManyToOne(
     *     targetEntity="Task"
     * )
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Groups({"backend", "frontend", "deserialize"})
     */
    private $task;

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
     * @var int
     * 
     * @ORM\Column(type="integer")
     * @JMS\Groups({"backend", "frontend", "deserialize"})
     */
    private $ticketCount = 1;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="in_block")
     */
    private $inBlock = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="is_pending")
     * @JMS\Groups({"backend", "frontend", "deserialize"})
     */
    private $isPending = false;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $dateTime = new \DateTime();
        $this->setCreatedAt($dateTime);
        $this->setUpdatedAt($dateTime);
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Ticket
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set task
     *
     * @param \AppBundle\Entity\Task $task
     *
     * @return Result
     */
    public function setTask(\AppBundle\Entity\Task $task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \AppBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set json
     *
     * @param array $json
     *
     * @return Result
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Get json
     *
     * @return array
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Result
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
     * @return Result
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
     * Set inBlock
     *
     * @param boolean $inBlock
     *
     * @return Result
     */
    public function setInBlock($inBlock)
    {
        $this->inBlock = $inBlock;

        return $this;
    }

    /**
     * Get inBlock
     *
     * @return boolean
     */
    public function getInBlock()
    {
        return $this->inBlock;
    }

    /**
     * Set isPending
     *
     * @param boolean $isPending
     *
     * @return Result
     */
    public function setIsPending($isPending)
    {
        $this->isPending = $isPending;

        return $this;
    }

    /**
     * Get isPending
     *
     * @return boolean
     */
    public function getIsPending()
    {
        return $this->isPending;
    }

    /**
     * Set ticketCount
     *
     * @param integer $ticketCount
     *
     * @return Result
     */
    public function setTicketCount($ticketCount)
    {
        $this->ticketCount = $ticketCount;

        return $this;
    }

    /**
     * Get ticketCount
     *
     * @return integer
     */
    public function getTicketCount()
    {
        return $this->ticketCount;
    }
}
