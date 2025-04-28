<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\VehiculeRepository;

#[ORM\Entity(repositoryClass: VehiculesRepository::class)]
#[ORM\Table(name: 'vehicules')]
class Vehicules
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
    private ?string $type = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $nbr_max_passagers_vip = null;

    public function getNbr_max_passagers_vip(): ?int
    {
        return $this->nbr_max_passagers_vip;
    }

    public function setNbr_max_passagers_vip(int $nbr_max_passagers_vip): self
    {
        $this->nbr_max_passagers_vip = $nbr_max_passagers_vip;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $nbr_max_passagers_premium = null;

    public function getNbr_max_passagers_premium(): ?int
    {
        return $this->nbr_max_passagers_premium;
    }

    public function setNbr_max_passagers_premium(int $nbr_max_passagers_premium): self
    {
        $this->nbr_max_passagers_premium = $nbr_max_passagers_premium;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $nbr_max_passagers_economy = null;

    public function getNbr_max_passagers_economy(): ?int
    {
        return $this->nbr_max_passagers_economy;
    }

    public function setNbr_max_passagers_economy(int $nbr_max_passagers_economy): self
    {
        $this->nbr_max_passagers_economy = $nbr_max_passagers_economy;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $places_disponibles_vip = null;

    public function getPlaces_disponibles_vip(): ?int
    {
        return $this->places_disponibles_vip;
    }

    public function setPlaces_disponibles_vip(int $places_disponibles_vip): self
    {
        $this->places_disponibles_vip = $places_disponibles_vip;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $places_disponibles_premium = null;

    public function getPlaces_disponibles_premium(): ?int
    {
        return $this->places_disponibles_premium;
    }

    public function setPlaces_disponibles_premium(int $places_disponibles_premium): self
    {
        $this->places_disponibles_premium = $places_disponibles_premium;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $places_disponibles_economy = null;

    public function getPlaces_disponibles_economy(): ?int
    {
        return $this->places_disponibles_economy;
    }

    public function setPlaces_disponibles_economy(int $places_disponibles_economy): self
    {
        $this->places_disponibles_economy = $places_disponibles_economy;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $ligne_id = null;

    public function getLigne_id(): ?int
    {
        return $this->ligne_id;
    }

    public function setLigne_id(int $ligne_id): self
    {
        $this->ligne_id = $ligne_id;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'vehicule')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        if (!$this->reservations instanceof Collection) {
            $this->reservations = new ArrayCollection();
        }
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->getReservations()->contains($reservation)) {
            $this->getReservations()->add($reservation);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        $this->getReservations()->removeElement($reservation);
        return $this;
    }

    public function getNbrMaxPassagersVip(): ?int
    {
        return $this->nbr_max_passagers_vip;
    }

    public function setNbrMaxPassagersVip(int $nbr_max_passagers_vip): static
    {
        $this->nbr_max_passagers_vip = $nbr_max_passagers_vip;

        return $this;
    }

    public function getNbrMaxPassagersPremium(): ?int
    {
        return $this->nbr_max_passagers_premium;
    }

    public function setNbrMaxPassagersPremium(int $nbr_max_passagers_premium): static
    {
        $this->nbr_max_passagers_premium = $nbr_max_passagers_premium;

        return $this;
    }

    public function getNbrMaxPassagersEconomy(): ?int
    {
        return $this->nbr_max_passagers_economy;
    }

    public function setNbrMaxPassagersEconomy(int $nbr_max_passagers_economy): static
    {
        $this->nbr_max_passagers_economy = $nbr_max_passagers_economy;

        return $this;
    }

    public function getPlacesDisponiblesVip(): ?int
    {
        return $this->places_disponibles_vip;
    }

    public function setPlacesDisponiblesVip(int $places_disponibles_vip): static
    {
        $this->places_disponibles_vip = $places_disponibles_vip;

        return $this;
    }

    public function getPlacesDisponiblesPremium(): ?int
    {
        return $this->places_disponibles_premium;
    }

    public function setPlacesDisponiblesPremium(int $places_disponibles_premium): static
    {
        $this->places_disponibles_premium = $places_disponibles_premium;

        return $this;
    }

    public function getPlacesDisponiblesEconomy(): ?int
    {
        return $this->places_disponibles_economy;
    }

    public function setPlacesDisponiblesEconomy(int $places_disponibles_economy): static
    {
        $this->places_disponibles_economy = $places_disponibles_economy;

        return $this;
    }

    public function getLigneId(): ?int
    {
        return $this->ligne_id;
    }

    public function setLigneId(int $ligne_id): static
    {
        $this->ligne_id = $ligne_id;

        return $this;
    }

}
