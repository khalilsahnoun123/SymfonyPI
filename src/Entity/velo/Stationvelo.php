<?php

namespace App\Entity\velo;

use App\Repository\velo\StationveloRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


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
    #[Assert\NotBlank(message: "Le nom de la station est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le nom de la station doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de la station ne peut pas dépasser {{ limit }} caractères."
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
    #[Assert\NotBlank(message: "Le gouvernorat est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le gouvernorat doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le gouvernorat ne peut pas dépasser {{ limit }} caractères."
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
    #[Assert\NotBlank(message: "La municipalité est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "La municipalité doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La municipalité ne peut pas dépasser {{ limit }} caractères."
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
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 200,
        minMessage: "L'adresse doit contenir au moins {{ limit }} caractères.",
        maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères."
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
    #[Assert\NotNull(message: "Un administrateur doit être assigné.")]
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
