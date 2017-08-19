<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Workshop entity for app.
 * 
 * @ORM\Entity(
 *     repositoryClass="AppBundle\Repository\WorkshopRepository"
 * )
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="efit_workshop")
 * @UniqueEntity(
 *     fields={"code"}, 
 *     message="Workshop code already exists on the system!"
 * )
 */
class Workshop
{
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
     * @Assert\NotBlank(
     *     message="Workshop name is required!"
     * )
     * @Assert\Length(
     *     min=8, 
     *     minMessage="Workshop name is too short!"
     * )
     * @JMS\Groups({"backend","frontend","deserialize"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message="A workshop code is required!"
     * )
     * @Assert\Length(
     *     min=8, 
     *     minMessage="Workshop code is too short!"
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9]+$/",
     *     message="Workshop code allows letters and numbers only!"
     * )
     * @JMS\Groups({"backend","deserialize"})
     */
    private $code;

    /**
     * @var Admin
     *
     * @ORM\ManyToOne(
     *     targetEntity="Admin"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @var User[]|ArrayCollection
     * 
     * @ORM\OneToMany(
     *     targetEntity="User", 
     *     mappedBy="workshop"
     * )
     * @JMS\Groups({"backend"})
     */
    private $users;

    /**
     * @var Tasks[]|ArrayCollection
     * 
     * @ORM\OneToMany(
     *     targetEntity="Task", 
     *     mappedBy="workshop", 
     *     cascade={
     *         "persist", 
     *         "remove"
     *     }
     * )
     * @JMS\Groups({"backend", "frontend"})
     */
    private $tasks;

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
     * Initializes `users` and `tasks` collection.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("createdAt")
     * @JMS\Groups({"backend"})
     */
    public function getCreatedAtTimeStamp()
    {
        return $this->createdAt->getTimestamp();
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("updatedAt")
     * @JMS\Groups({"backend"})
     */
    public function getUpdatedAtTimeStamp()
    {
        return $this->updatedAt->getTimestamp();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $dateTime = new \DateTime();
        $this->setCreatedAt($dateTime);
        $this->setUpdatedAt($dateTime);

        $this->users = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Workshop
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Workshop
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
     * Get owner
     *
     * @return \AppBundle\Entity\Admin
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set owner
     * 
     * @param \AppBundle\Entity\Admin $owner
     *
     * @return Workshop
     */
    public function setOwner(Admin $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    public function jsonSerialize()
    {
        return array(
            'name' => $this->name,
            'isActive' => $this->isActive
        );
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Workshop
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
     * @return Workshop
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
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Workshop
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add task
     *
     * @param \AppBundle\Entity\Task $task
     *
     * @return Workshop
     */
    public function addTask(\AppBundle\Entity\Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \AppBundle\Entity\Task $task
     */
    public function removeTask(\AppBundle\Entity\Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}
