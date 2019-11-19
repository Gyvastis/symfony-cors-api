<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=32, unique=true, nullable=true)
     */
    private $apiKey;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $restrictToOrigin;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $apiHitCount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastApiHitAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string|null
     */
    public function getRestrictToOrigin(): ?string
    {
        return $this->restrictToOrigin;
    }

    /**
     * @param string $restrictToOrigin
     */
    public function setRestrictToOrigin(?string $restrictToOrigin): void
    {
        $this->restrictToOrigin = $restrictToOrigin;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getApiHitCount(): ?int
    {
        return $this->apiHitCount;
    }

    public function setApiHitCount(?int $apiHitCount): self
    {
        $this->apiHitCount = $apiHitCount;

        return $this;
    }

    public function getLastApiHitAt(): ?DateTimeInterface
    {
        return $this->lastApiHitAt;
    }

    public function setLastApiHitAt(?DateTimeInterface $lastApiHitAt): self
    {
        $this->lastApiHitAt = $lastApiHitAt;

        return $this;
    }
}
