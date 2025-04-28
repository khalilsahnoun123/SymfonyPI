<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationTaxiRepository;

#[ORM\Entity(repositoryClass: ReservationTaxiRepository::class)]
#[ORM\Table(name: 'reservationtaxi')]
class ReservationTaxi
{
    public const STATUS_PENDING = 'en attente';
    public const STATUS_CONFIRMED = 'confirmé';
    public const STATUS_REFUSED = 'refusé';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Vehicule::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'id_vehicule', referencedColumnName: 'id')]
    private ?Vehicule $vehicule = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = self::STATUS_PENDING;

    // ... (les autres getters et setters)

    public function setStatus(string $status): self
    {
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_REFUSED
        ];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException(sprintf(
                'Statut invalide. Les statuts valides sont : %s',
                implode(', ', $validStatuses)
            ));
        }

        $this->status = $status;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getVehicule(): ?Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(?Vehicule $vehicule): static
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}