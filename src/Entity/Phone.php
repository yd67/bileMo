<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PhoneRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"phoneGroup"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"phoneGroup"})
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"phoneGroup"})
     */
    private $model;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"phoneGroup"})
     */
    private $color;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"phoneGroup"})
     */
    private $storage;

    /**
     * @ORM\Column(type="float")
     * @Groups({"phoneGroup"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"phoneGroup"})
     */
    private $imeiCode;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"phoneGroup"})
     */
    private $description;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getStorage(): ?int
    {
        return $this->storage;
    }

    public function setStorage(?int $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImeiCode(): ?string
    {
        return $this->imeiCode;
    }

    public function setImeiCode(string $imeiCode): self
    {
        $this->imeiCode = $imeiCode;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

}
