<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\LigneRepository;

#[ORM\Entity(repositoryClass: LigneRepository::class)]
#[ORM\Table(name: 'ligne')]
class Ligne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $prix_vip = null;

    public function getPrix_vip(): ?float
    {
        return $this->prix_vip;
    }

    public function setPrix_vip(?float $prix_vip): self
    {
        $this->prix_vip = $prix_vip;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $prix_premium = null;

    public function getPrix_premium(): ?float
    {
        return $this->prix_premium;
    }

    public function setPrix_premium(?float $prix_premium): self
    {
        $this->prix_premium = $prix_premium;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $prix_economique = null;

    public function getPrix_economique(): ?float
    {
        return $this->prix_economique;
    }

    public function setPrix_economique(?float $prix_economique): self
    {
        $this->prix_economique = $prix_economique;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $region = null;

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Station::class, mappedBy: 'ligne')]
    private Collection $stations;

    public function __construct()
    {
        $this->stations = new ArrayCollection();
    }

    /**
     * @return Collection<int, Station>
     */
    public function getStations(): Collection
    {
        if (!$this->stations instanceof Collection) {
            $this->stations = new ArrayCollection();
        }
        return $this->stations;
    }

    public function addStation(Station $station): self
    {
        if (!$this->getStations()->contains($station)) {
            $this->getStations()->add($station);
        }
        return $this;
    }

    public function removeStation(Station $station): self
    {
        $this->getStations()->removeElement($station);
        return $this;
    }

    public function getPrixVip(): ?string
    {
        return $this->prix_vip;
    }

    public function setPrixVip(?string $prix_vip): static
    {
        $this->prix_vip = $prix_vip;

        return $this;
    }

    public function getPrixPremium(): ?string
    {
        return $this->prix_premium;
    }

    public function setPrixPremium(?string $prix_premium): static
    {
        $this->prix_premium = $prix_premium;

        return $this;
    }

    public function getPrixEconomique(): ?string
    {
        return $this->prix_economique;
    }

    public function setPrixEconomique(?string $prix_economique): static
    {
        $this->prix_economique = $prix_economique;

        return $this;
    }

}
