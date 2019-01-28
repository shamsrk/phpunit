<?php
/*
 * User entity file
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations\UniqueIndex as MongoDBUnique;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * User entity class
 *
 * @MongoDB\Document
 * @MongoDB\HasLifecycleCallbacks()
 * @MongoDBUnique("email")
 */
class User implements UserInterface
{
    /*
     * fillable attributes, which can mass assign into and fetch from database.
     */
    protected $fillables = [
        'id', 'name', 'email', 'username', 'phoneNumber', 'address', 'dob', 'sessionId', 'lastActiveAt'
    ];

    /**
     * Function to get all the mass assignable attributes and data
     *
     * @return array
     */
    public function get()
    {
        $data = [];
        array_map(function ($attribute) use (&$data) {
            $data[$attribute] = $this->$attribute;
        }, $this->fillables);

        return $data;
    }

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
     * @MongoDB\Field(type="string")
     */
    protected $phoneNumber;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $address;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $dob;

    /**
     * @MongoDB\Field(type="string")
     */
    private $plainPassword;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $password;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $createdAt;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $updatedAt;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $sessionId;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $lastActiveAt;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $deviceId;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = serialize(['ROLE_USER']);
    }

    /**
     * Function to get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return unserialize($this->roles);
    }

    /**
     * Function to set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = serialize(array_merge($this->getRoles(), $roles));
        return $this;
    }

    /**
     * Function to get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Function to set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Function to get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Function to set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Function to get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Function to set username
     *
     * @return User
     */
    public function setUsername($username): User
    {
        $this->username = $username;
        return $this;
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
     * Function to set address
     *
     * @param string $address
     * @return User
     */
    public function setAddress($address): User
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Function to get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Function to set phone number
     *
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber): User
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Function to get phone number
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Function to set dob
     * @param MongoDB\Date $dob
     * @return User
     */
    public function setDob($dob): User
    {
        $this->dob = $dob;
        return $this;
    }

    /**
     * Function to get dob
     *
     * @return MongoDB\Date
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Function to get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Function to set plainPassword
     *
     * @param $password
     * @return $this
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
        return $this;
    }

    /**
     * Function to set password
     *
     * @param string $password
     * @return User;
     */
    public function setPassword($password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Function to get password
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Implementing function from UserInterface
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }


    /**
     * Function to set created date time
     *
     * @param MongoDB\Date $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt): User
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Function to get created date time
     *
     * @return MongoDB\Date
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Function to set updated date time
     *
     * @param MongoDB\Date $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt): User
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Function to get updated date time
     *
     * @return MongoDB\Date
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * Auto call this function on pre persist
     *
     * @MongoDB\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Auto call this function on pre update
     *
     * @MongoDB\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Erase credential, implementing function from UserInterface
     */
    public function eraseCredentials()
    {
    }

    /**
     * Function to set lastActiveAt
     *
     * @param MongoDB\Date $lastActiveAt
     * @return User
     */
    public function setLastActiveAt($lastActiveAt): User
    {
        $this->lastActiveAt = $lastActiveAt;
        return $this;
    }

    /**
     * Function to get lastActiveAt
     *
     * @return MongoDB\Date
     */
    public function getLastActiveAt()
    {
        return $this->lastActiveAt;
    }

    /**
     * Function to set session id
     *
     * @param string $sessionId
     * @return User
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * Function to get session id
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Function to set device id
     *
     * @param string $deviceId
     * @return User
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
        return $this;
    }

    /**
     * Function to get device id
     *
     * @return string
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }
}