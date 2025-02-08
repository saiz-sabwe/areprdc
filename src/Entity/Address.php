<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $municipality = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $neighborhood = null;

    #[ORM\Column(length: 50)]
    private ?string $avenue = null;

    #[ORM\Column(length: 10)]
    private ?string $number = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Federation $federation = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getMunicipality(): ?string
    {
        return $this->municipality;
    }

    public function setMunicipality(?string $municipality): static
    {
        $this->municipality = $municipality;

        return $this;
    }

    public function getNeighborhood(): ?string
    {
        return $this->neighborhood;
    }

    public function setNeighborhood(?string $neighborhood): static
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }

    public function getAvenue(): ?string
    {
        return $this->avenue;
    }

    public function setAvenue(string $avenue): static
    {
        $this->avenue = $avenue;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getFederation(): ?Federation
    {
        return $this->federation;
    }

    public function setFederation(?Federation $federation): static
    {
        $this->federation = $federation;

        return $this;
    }
}
