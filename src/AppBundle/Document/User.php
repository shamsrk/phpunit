<?php
/**
 * Created by PhpStorm.
 * User: shams
 * Date: 24/12/18
 * Time: 2:43 PM
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations\UniqueIndex as MongoDBUnique;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
/**
 * @MongoDB\Document
 * @MongoDB\HasLifecycleCallbacks()
 * @MongoDBUnique("email")
 */
class User implements UserInterface
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string", options={"unique"=true})
     * @MongoDBUnique
     */
    protected $email;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDBUnique
     */
    protected $username;

    /**
     *@MongoDB\Field(type="string")
     */
    protected $phoneNumber;

    /**
     *@MongoDB\Field(type="string")
     */
    protected $address;

    /**
     *@MongoDB\Field(type="date")
     */
    protected $dob;

    /**
     *@MongoDB\Field(type="string")
     */
    protected $password;

    /**
     *@MongoDB\Field(type="date")
     */
    protected $createdAt;

    /**
     *@MongoDB\Field(type="date")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * 
     * @return User
     */
    public function setName($name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * Function to get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * @param string $address
     * 
     * @return User
     */
    public function setAddress($address): User
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $phoneNumber
     * 
     * @return User
     */
    public function setPhoneNumber($phoneNumber): User
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param MongoDB\Date $dob
     * 
     * @return User
     */
    public function setDob($dob): User
    {
        $this->dob = $dob;
        return $this;
    }

    /**
     * @return MongoDB\Date
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param string $password
     *
     * @return User;
     */
    public function setPassword($password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     *  @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *  @return string|null The salt
     */
    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }


    /**
     * @param MongoDB\Date $createdAt
     * 
     * @return User
     */
    public function setCreatedAt($createdAt): User
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return MongoDB\Date
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param MongoDB\Date $updatedAt
     * 
     * @return User
     */
    public function setUpdatedAt($updatedAt): User
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return MongoDB\Date
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * @MongoDB\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @MongoDB\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Function to get role
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
    }
}