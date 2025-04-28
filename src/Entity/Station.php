<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\StationRepository;

#[ORM\Entity(repositoryClass: StationRepository::class)]
#[ORM\Table(name: 'stations')]
class Station
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
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $adresse = null;

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Ligne::class, inversedBy: 'stations')]
    #[ORM\JoinColumn(name: 'ligne_id', referencedColumnName: 'id')]
    private ?Ligne $ligne = null;

    public function getLigne(): ?Ligne
    {
        return $this->ligne;
    }

    public function setLigne(?Ligne $ligne): self
    {
        $this->ligne = $ligne;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'depart_station')]
    private Collection $depart_reservations;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'fin_station')]
    private Collection $fin_reservations;

    public function __construct()
    {
        $this->depart_reservations = new ArrayCollection();
        $this->fin_reservations = new ArrayCollection();
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getDepartReservations(): Collection
    {
        return $this->depart_reservations;
    }

    public function addDepartReservation(Reservation $reservation): self
    {
        if (!$this->depart_reservations->contains($reservation)) {
            $this->depart_reservations->add($reservation);
        }
        return $this;
    }

    public function removeDepartReservation(Reservation $reservation): self
    {
        $this->depart_reservations->removeElement($reservation);
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getFinReservations(): Collection
    {
        return $this->fin_reservations;
    }

    public function addFinReservation(Reservation $reservation): self
    {
        if (!$this->fin_reservations->contains($reservation)) {
            $this->fin_reservations->add($reservation);
        }
        return $this;
    }

    public function removeFinReservation(Reservation $reservation): self
    {
        $this->fin_reservations->removeElement($reservation);
        return $this;
    }
}