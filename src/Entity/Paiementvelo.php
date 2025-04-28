<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\PaiementveloRepository;

#[ORM\Entity(repositoryClass: PaiementveloRepository::class)]
#[ORM\Table(name: 'paiementvelo')]
class Paiementvelo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $id_paiement = null;

    #[ORM\ManyToOne(targetEntity: Reservationvelo::class, inversedBy: 'paiementvelos')]
    #[ORM\JoinColumn(name: 'id_reservation_velo', referencedColumnName: 'id_reservation_velo')]
    private ?Reservationvelo $reservationvelo = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $montant = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $status = null;

    // Getters and setters

    public function getIdPaiement(): ?int
    {
        return $this->id_paiement;
    }

    public function getReservationvelo(): ?Reservationvelo
    {
        return $this->reservationvelo;
    }

    public function setReservationvelo(?Reservationvelo $reservationvelo): self
    {
        $this->reservationvelo = $reservationvelo;
        return $this;
    }

    public function isMontant(): ?bool
    {
        return $this->montant;
    }

    public function setMontant(bool $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}