<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Doctrine\UserOwned;
use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[UserOwned(property: 'user')]
#[ApiResource(security: "is_granted('ROLE_USER')")]
#[GetCollection(security: "is_granted('ROLE_USER')")]
#[Post(security: "is_granted('ROLE_USER')")]
#[Get(security: "is_granted('ROLE_ADMIN') or object.getUser() == user")]
#[Put(security: "is_granted('ROLE_ADMIN') or object.getUser() == user")]
#[Patch(security: "is_granted('ROLE_ADMIN') or object.getUser() == user")]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Assert\Country()]
    #[ORM\Column(length: 4)]
    private ?string $country = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 128)]
    private ?string $region = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 128)]
    private ?string $city = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 16)]
    private ?string $postalCode = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $streetAddress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    public function setStreetAddress(string $streetAddress): static
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }
}
