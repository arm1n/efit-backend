<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Admin entity for app.
 * 
 * @ORM\Entity(
 *     repositoryClass="AppBundle\Repository\AdminRepository"
 * )
 * @ORM\Table(name="efit_admin")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"username"})
 */
class Admin implements UserInterface
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
     * @ORM\Column(type="string", unique=true)
     * @Assert\Length(
     *     min=5,
     *     minMessage="Username is too short!"
     * )
     * @JMS\Groups({"backend", "frontend", "deserialize"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @JMS\Groups({"deserialize"})
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\Length(
     *     min=8,
     *     max=4096,
     *     minMessage="Password is too short!",
     *     maxMessage="Password is too long!"
     * )
     */
    private $plainPassword;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     * @JMS\Groups({"backend", "frontend"})
     */
    private $roles = [];

    /**
     * @var Workshop[]|ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="Workshop", mappedBy="owner")
     * @JMS\Groups({"backend", "frontend"})
     */
    private $workshops;

    /**
     *  @var array
     */
    public $validRoles = [
        'ROLE_SUPER_ADMIN',
        'ROLE_ADMIN',
    ];

    /**
     * Initializes `workshops` collection.
     */
    public function __construct()
    {
        $this->workshops = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->workshops = new ArrayCollection();
    }

    /**
     * @internal
     */
    private function validateRoles($roles)
    {
        $validRoles = array_filter($roles, function($role) {
            return in_array($role, $this->validRoles);
        });

        if (empty($validRoles)) {
            $validRoles = ['ROLE_ADMIN'];
        }

        return $validRoles;
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
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set plainPassword
     *
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->validateRoles($this->roles);
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = $this->validateRoles($roles);
    }

    /**
     * Original salt for password encryption.
     *
     * {@inheritdoc}
     */
    public function getSalt()
    {
        // no salt needed cause we 
        // are using bcrypt encoder
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
     * Add workshop
     *
     * @param \AppBundle\Entity\Workshop $workshop
     *
     * @return Admin
     */
    public function addWorkshop(\AppBundle\Entity\Workshop $workshop)
    {
        $this->workshops[] = $workshop;

        return $this;
    }

    /**
     * Remove workshop
     *
     * @param \AppBundle\Entity\Workshop $workshop
     */
    public function removeWorkshop(\AppBundle\Entity\Workshop $workshop)
    {
        $this->workshops->removeElement($workshop);
    }

    /**
     * Get workshops
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorkshops()
    {
        return $this->workshops;
    }
}
