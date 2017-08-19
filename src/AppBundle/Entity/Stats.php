<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Stats entity for app.
 * 
 * @ORM\Entity(
 *     repositoryClass="AppBundle\Repository\StatsRepository"
 * )
 * @ORM\Table(name="efit_stats")
 */
class Stats
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @JMS\Groups({"frontend"})
     */
    private $id;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     * @JMS\Groups({"frontend"})
     */
    private $tasks;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     * @JMS\Groups({"frontend"})
     */
    private $blocks;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Groups({"frontend"})
     */
    private $rounds = 0;

    /**
     * @var User
     *
     * @ORM\OneToOne(
     *     targetEntity="User",
     *     inversedBy="stats"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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
     * Set tasks
     *
     * @param array $tasks
     *
     * @return Stats
     */
    public function setTasks($tasks)
    {
        $this->tasks = $tasks;

        return $this;
    }

    /**
     * Get tasks
     *
     * @return array
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set blocks
     *
     * @param array $blocks
     *
     * @return Stats
     */
    public function setBlocks($blocks)
    {
        $this->blocks = $blocks;

        return $this;
    }

    /**
     * Get blocks
     *
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Set rounds
     *
     * @param array $rounds
     *
     * @return Stats
     */
    public function setRounds($rounds)
    {
        $this->rounds = $rounds;

        return $this;
    }

    /**
     * Get rounds
     *
     * @return array
     */
    public function getRounds()
    {
        return $this->rounds;
    }
}
