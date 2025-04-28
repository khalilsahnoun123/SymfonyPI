<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\CovoiturageRepository;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
#[ORM\Table(name: 'covoiturage')]
class Covoiturage
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'covoiturages')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
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
    private ?string $point_de_depart = null;

    public function getPoint_de_depart(): ?string
    {
        return $this->point_de_depart;
    }

    public function setPoint_de_depart(string $point_de_depart): self
    {
        $this->point_de_depart = $point_de_depart;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $point_de_destination = null;

    public function getPoint_de_destination(): ?string
    {
        return $this->point_de_destination;
    }

    public function setPoint_de_destination(string $point_de_destination): self
    {
        $this->point_de_destination = $point_de_destination;
        return $this;
    }

    #[ORM\Column(type: 'float', nullable: false)]
    private ?float $prix = null;

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $nbr_place = null;

    public function getNbr_place(): ?int
    {
        return $this->nbr_place;
    }

    public function setNbr_place(int $nbr_place): self
    {
        $this->nbr_place = $nbr_place;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: ReservationCov::class, mappedBy: 'covoiturage')]
    private Collection $reservationCovs;

    public function __construct()
    {
        $this->reservationCovs = new ArrayCollection();
    }

    /**
     * @return Collection<int, ReservationCov>
     */
    public function getReservationCovs(): Collection
    {
        if (!$this->reservationCovs instanceof Collection) {
            $this->reservationCovs = new ArrayCollection();
        }
        return $this->reservationCovs;
    }

    public function addReservationCov(ReservationCov $reservationCov): self
    {
        if (!$this->getReservationCovs()->contains($reservationCov)) {
            $this->getReservationCovs()->add($reservationCov);
        }
        return $this;
    }

    public function removeReservationCov(ReservationCov $reservationCov): self
    {
        $this->getReservationCovs()->removeElement($reservationCov);
        return $this;
    }

    public function getPointDeDepart(): ?string
    {
        return $this->point_de_depart;
    }

    public function setPointDeDepart(string $point_de_depart): static
    {
        $this->point_de_depart = $point_de_depart;

        return $this;
    }

    public function getPointDeDestination(): ?string
    {
        return $this->point_de_destination;
    }

    public function setPointDeDestination(string $point_de_destination): static
    {
        $this->point_de_destination = $point_de_destination;

        return $this;
    }

    public function getNbrPlace(): ?int
    {
        return $this->nbr_place;
    }

    public function setNbrPlace(int $nbr_place): static
    {
        $this->nbr_place = $nbr_place;

        return $this;
    }

}
