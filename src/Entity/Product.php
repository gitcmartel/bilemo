<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $constructor = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $dimensions = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $weight = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $os = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $connectivity = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $camera = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $app = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $storage = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ram = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $battery = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gps = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $accessories = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $processor = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $design = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getConstructor(): ?string
    {
        return $this->constructor;
    }

    public function setConstructor(string $constructor): static
    {
        $this->constructor = $constructor;

        return $this;
    }

    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }

    public function setDimensions(?string $dimensions): static
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(?string $os): static
    {
        $this->os = $os;

        return $this;
    }

    public function getConnectivity(): ?string
    {
        return $this->connectivity;
    }

    public function setConnectivity(?string $connectivity): static
    {
        $this->connectivity = $connectivity;

        return $this;
    }

    public function getCamera(): ?string
    {
        return $this->camera;
    }

    public function setCamera(?string $camera): static
    {
        $this->camera = $camera;

        return $this;
    }

    public function getApp(): ?string
    {
        return $this->app;
    }

    public function setApp(?string $app): static
    {
        $this->app = $app;

        return $this;
    }

    public function getStorage(): ?string
    {
        return $this->storage;
    }

    public function setStorage(?string $storage): static
    {
        $this->storage = $storage;

        return $this;
    }

    public function getRam(): ?string
    {
        return $this->ram;
    }

    public function setRam(?string $ram): static
    {
        $this->ram = $ram;

        return $this;
    }

    public function getBattery(): ?string
    {
        return $this->battery;
    }

    public function setBattery(?string $battery): static
    {
        $this->battery = $battery;

        return $this;
    }

    public function getGps(): ?string
    {
        return $this->gps;
    }

    public function setGps(?string $gps): static
    {
        $this->gps = $gps;

        return $this;
    }

    public function getAccessories(): ?string
    {
        return $this->accessories;
    }

    public function setAccessories(?string $accessories): static
    {
        $this->accessories = $accessories;

        return $this;
    }

    public function getProcessor(): ?string
    {
        return $this->processor;
    }

    public function setProcessor(?string $processor): static
    {
        $this->processor = $processor;

        return $this;
    }

    public function getDesign(): ?string
    {
        return $this->design;
    }

    public function setDesign(?string $design): static
    {
        $this->design = $design;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
