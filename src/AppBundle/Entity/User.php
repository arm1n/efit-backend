<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User entity for app.
 * 
 * @ORM\Entity(
 *     repositoryClass="AppBundle\Repository\UserRepository"
 * )
 * @ORM\Table(name="efit_user")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"username"},
 *     message="Username already exists on the system!"
 * )
 */
class User implements UserInterface
{
    const STATE_BEGINNER = 1;
    const STATE_AMATEUR = 2;
    const STATE_ADVANCED = 3;
    const STATE_EXPERT = 4;

    const GROUP_A = 1;
    const GROUP_B = 2;

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
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\Regex(
     *     pattern="/^[a-z][a-z](?:0[1-9]|[12]\d|3[01])[A-Z]\d$/",
     *     message="Personal code allows a specific pattern only!"
     * )
     * @JMS\Groups({"backend", "frontend"})
     */
    private $username;

    /**
     * @var Workshop
     *
     * @ORM\ManyToOne(targetEntity="Workshop", inversedBy="users")
     * @ORM\JoinColumn(name="workshop_id", referencedColumnName="id", nullable=false)
     * @JMS\Groups({"backend", "frontend"})
     * @JMS\MaxDepth(3)
     */
    private $workshop;

    /**
     * @var Tickets[]|ArrayCollection
     * 
     * @ORM\OneToMany(
     *     targetEntity="Ticket", 
     *     mappedBy="user",
     *     cascade={
     *         "persist",
     *         "remove"
     *     }
     * )
     */
    private $tickets;

    /**
     * @var Results[]|ArrayCollection
     * 
     * @ORM\OneToMany(
     *     targetEntity="Result", 
     *     mappedBy="user",
     *     cascade={
     *         "persist",
     *         "remove"
     *     }
     * )
     */
    private $results;

    /**
     * @var Stats
     *
     * @ORM\OneToOne(
     *     targetEntity="Stats", 
     *     mappedBy="user",
     *     cascade={
     *         "persist",
     *         "remove"
     *     }
     * )
     */
    private $stats;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @JMS\Groups({"frontend"})
     */
    private $state = self::STATE_BEGINNER;

    /**
     * Initializes `tickets` and `results` collection.
     */
    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->results = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->tickets = new ArrayCollection();
        $this->results = new ArrayCollection();
    }

    /**
     * Gets random group based on `id` and
     * a quite primitive modulo calculation.
     * 
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("group")
     * @JMS\Groups({"frontend"})
     */
    public function getRandomGroup()
    {
        if ($this->id % 2 === 0) {
            return self::GROUP_A;
        }

        return self::GROUP_B;
    }

    /**
     * Counts amount of current tickets.
     *
     * @JMS\VirtualProperty()
     * @JMS\Groups({"frontend"})
     * @JMS\SerializedName("pending")
     */
    public function getPendingResults()
    {
        $where = Criteria::expr()->eq("isPending", true);
        $criteria = Criteria::create()->where($where);
        
        return $this->getResults()->matching($criteria);
    }

    /**
     * Counts amount of current tickets.
     *
     * @JMS\VirtualProperty()
     * @JMS\Groups({"frontend"})
     * @JMS\SerializedName("tickets")
     */
    public function countTickets()
    {
        return $this->getTickets()->count();
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
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * Get roles
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("roles")
     * @JMS\Groups({"frontend"})
     *
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * Original salt for password encryption.
     *
     * {@inheritdoc}
     */
    public function getSalt()
    {
        // no salt needed cause we 
        // are using no passwords!
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * Set workshop
     *
     * @param \AppBundle\Entity\Workshop $workshop
     *
     * @return User
     */
    public function setWorkshop(\AppBundle\Entity\Workshop $workshop = null)
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
     * Set state
     *
     * @param integer $state
     *
     * @return User
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Add ticket
     *
     * @param \AppBundle\Entity\Ticket $ticket
     *
     * @return User
     */
    public function addTicket(\AppBundle\Entity\Ticket $ticket)
    {
        $this->tickets[] = $ticket;

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \AppBundle\Entity\Ticket $ticket
     */
    public function removeTicket(\AppBundle\Entity\Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * Add result
     *
     * @param \AppBundle\Entity\Result $result
     *
     * @return User
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
     * Set stats
     *
     * @param \AppBundle\Entity\Stats $stats
     *
     * @return User
     */
    public function setStats(\AppBundle\Entity\Stats $stats = null)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * Get stats
     *
     * @return \AppBundle\Entity\Stats
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * Provides all supported states for `User` entity.
     *
     * @static
     * @return array
     */
    public static function getStates()
    {
        return [
            self::STATE_BEGINNER,
            self::STATE_AMATEUR,
            self::STATE_ADVANCED,
            self::STATE_EXPERT
        ];
    }
}
