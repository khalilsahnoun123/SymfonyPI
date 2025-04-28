<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\StationveloRepository;

#[ORM\Entity(repositoryClass: StationveloRepository::class)]
#[ORM\Table(name: 'stationvelo')]
class Stationvelo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_station = null;

    public function getId_station(): ?int
    {
        return $this->id_station;
    }

    public function setId_station(int $id_station): self
    {
        $this->id_station = $id_station;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'The station name cannot be blank.')]
    #[Assert\Length(
        max: 20,
        maxMessage: 'The station name cannot exceed {{ limit }} characters.'
    )]
    private ?string $name_station = null;

    public function getName_station(): ?string
    {
        return $this->name_station;
    }

    public function setName_station(string $name_station): self
    {
        $this->name_station = $name_station;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'The governorate cannot be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The governorate cannot exceed {{ limit }} characters.'
    )]
    private ?string $gouvernera = null;

    public function getGouvernera(): ?string
    {
        return $this->gouvernera;
    }

    public function setGouvernera(string $gouvernera): self
    {
        $this->gouvernera = $gouvernera;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'The municipality cannot be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The municipality cannot exceed {{ limit }} characters.'
    )]
    private ?string $municapilite = null;

    public function getMunicapilite(): ?string
    {
        return $this->municapilite;
    }

    public function setMunicapilite(string $municapilite): self
    {
        $this->municapilite = $municapilite;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'The address cannot be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The address cannot exceed {{ limit }} characters.'
    )]
    private ?string $adresse = null;

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'stationvelos')]
    #[ORM\JoinColumn(name: 'id_admin', referencedColumnName: 'id')]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
   
    private ?string $station_image = null;

    public function getStation_image(): ?string
    {
        return $this->station_image;
    }

    public function setStation_image(string $station_image): self
    {
        $this->station_image = $station_image;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Velo::class, mappedBy: 'stationvelo')]
    private Collection $velos;

    public function __construct()
    {
        $this->velos = new ArrayCollection();
    }

    /**
     * @return Collection<int, Velo>
     */
    public function getVelos(): Collection
    {
        if (!$this->velos instanceof Collection) {
            $this->velos = new ArrayCollection();
        }
        return $this->velos;
    }

    public function addVelo(Velo $velo): self
    {
        if (!$this->getVelos()->contains($velo)) {
            $this->getVelos()->add($velo);
        }
        return $this;
    }

    public function removeVelo(Velo $velo): self
    {
        $this->getVelos()->removeElement($velo);
        return $this;
    }

    public function getIdStation(): ?int
    {
        return $this->id_station;
    }

    public function getNameStation(): ?string
    {
        return $this->name_station;
    }

    public function setNameStation(string $name_station): static
    {
        $this->name_station = $name_station;

        return $this;
    }

    public function getStationImage(): ?string
    {
        return $this->station_image;
    }

    public function setStationImage(string $station_image): static
    {
        $this->station_image = $station_image;

        return $this;
    }

}
