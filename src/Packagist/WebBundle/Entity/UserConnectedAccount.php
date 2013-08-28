<?php

namespace Packagist\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="user_connected_account",
 *      indexes={@ORM\Index(name="username_provider_idx",columns={"username", "provider"})}
 * )
 */
class UserConnectedAccount {
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Packagist\WebBundle\Entity\User", inversedBy="accounts")
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $provider;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $token;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
}