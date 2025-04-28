<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ReservationRepository;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservations')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $reservation_id = null;

    public function getReservation_id(): ?int
    {
        return $this->reservation_id;
    }

    public function setReservation_id(int $reservation_id): self
    {
        $this->reservation_id = $reservation_id;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $travel_date = null;

    public function getTravel_date(): ?\DateTimeInterface
    {
        return $this->travel_date;
    }

    public function setTravel_date(\DateTimeInterface $travel_date): self
    {
        $this->travel_date = $travel_date;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $number_of_seats = null;

    public function getNumber_of_seats(): ?int
    {
        return $this->number_of_seats;
    }

    public function setNumber_of_seats(int $number_of_seats): self
    {
        $this->number_of_seats = $number_of_seats;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $ticket_category = null;

    public function getTicket_category(): ?string
    {
        return $this->ticket_category;
    }

    public function setTicket_category(string $ticket_category): self
    {
        $this->ticket_category = $ticket_category;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $total_price = null;

    public function getTotal_price(): ?float
    {
        return $this->total_price;
    }

    public function setTotal_price(?float $total_price): self
    {
        $this->total_price = $total_price;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Vehicules::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'vehicule_id', referencedColumnName: 'id')]
    private ?Vehicules $vehicule = null;

    public function getVehicule(): ?Vehicules
    {
        return $this->vehicule;
    }

    public function setVehicule(?Vehicules $vehicule): self
    {
        $this->vehicule = $vehicule;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Station::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'depart_station_id', referencedColumnName: 'id')]
    private ?Station $depart_station = null;

    public function getDepart_station(): ?Station
    {
        return $this->depart_station;
    }

    public function setDepart_station(?Station $depart_station): self
    {
        $this->depart_station = $depart_station;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Station::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'fin_station_id', referencedColumnName: 'id')]
    private ?Station $fin_station = null;

    public function getFin_station(): ?Station
    {
        return $this->fin_station;
    }

    public function setFin_station(?Station $fin_station): self
    {
        $this->fin_station = $fin_station;
        return $this;
    }

    public function getReservationId(): ?int
    {
        return $this->reservation_id;
    }

    public function getTravelDate(): ?\DateTimeInterface
    {
        return $this->travel_date;
    }

    public function setTravelDate(\DateTimeInterface $travel_date): static
    {
        $this->travel_date = $travel_date;

        return $this;
    }

    public function getNumberOfSeats(): ?int
    {
        return $this->number_of_seats;
    }

    public function setNumberOfSeats(int $number_of_seats): static
    {
        $this->number_of_seats = $number_of_seats;

        return $this;
    }

    public function getTicketCategory(): ?string
    {
        return $this->ticket_category;
    }

    public function setTicketCategory(string $ticket_category): static
    {
        $this->ticket_category = $ticket_category;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->total_price;
    }

    public function setTotalPrice(?string $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getDepartStation(): ?Station
    {
        return $this->depart_station;
    }

    public function setDepartStation(?Station $depart_station): static
    {
        $this->depart_station = $depart_station;

        return $this;
    }

    public function getFinStation(): ?Station
    {
        return $this->fin_station;
    }

    public function setFinStation(?Station $fin_station): static
    {
        $this->fin_station = $fin_station;

        return $this;
    }
}