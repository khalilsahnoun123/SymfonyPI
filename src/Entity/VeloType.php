<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\VeloTypeRepository;

#[ORM\Entity(repositoryClass: VeloTypeRepository::class)]
#[ORM\Table(name: 'velo_type')]
class VeloType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_type = null;

    public function getId_type(): ?int
    {
        return $this->id_type;
    }

    public function setId_type(int $id_type): self
    {
        $this->id_type = $id_type;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $type_name = null;

    public function getType_name(): ?string
    {
        return $this->type_name;
    }

    public function setType_name(string $type_name): self
    {
        $this->type_name = $type_name;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: false)]
    private ?float $price = null;

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $velo_image = null;

    public function getVelo_image(): ?string
    {
        return $this->velo_image;
    }

    public function setVelo_image(string $velo_image): self
    {
        $this->velo_image = $velo_image;
        return $this;
    }

    public function getIdType(): ?int
    {
        return $this->id_type;
    }

    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function setTypeName(string $type_name): static
    {
        $this->type_name = $type_name;

        return $this;
    }

    public function getVeloImage(): ?string
    {
        return $this->velo_image;
    }

    public function setVeloImage(string $velo_image): static
    {
        $this->velo_image = $velo_image;

        return $this;
    }

}
