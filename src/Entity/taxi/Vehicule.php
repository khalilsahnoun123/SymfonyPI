<?php

namespace App\Entity\taxi;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\taxi\VehiculeRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "La marque ne peut pas être vide.")]
    private ?string $marque = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le modèle ne peut pas être vide.")]
    private ?string $modele = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "L'immatriculation ne peut pas être vide.")]
    #[Assert\Length(
        max: 7,  // Limite maximale de 7 caractères pour l'immatriculation
        maxMessage: "L'immatriculation ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $immatriculation = null;

    #[ORM\Column(type: 'string', length: 255)]
#[Assert\NotBlank(message: "Le numéro de série ne peut pas être vide.")]
#[Assert\Regex(
    pattern: "/^\d+$/",  // Vérifie que le champ est constitué uniquement de chiffres
    message: "Veuillez respecter le format requis pour le numéro de série (uniquement des chiffres)."
)]
private ?string $numserie = null;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'vehicules')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: ReservationTaxi::class, mappedBy: 'vehicule')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }

    public function getMarque(): ?string { return $this->marque; }
    public function setMarque(string $marque): self { $this->marque = $marque; return $this; }

    public function getModele(): ?string { return $this->modele; }
    public function setModele(string $modele): self { $this->modele = $modele; return $this; }

    public function getImmatriculation(): ?string { return $this->immatriculation; }
    public function setImmatriculation(string $immatriculation): self { $this->immatriculation = $immatriculation; return $this; }

    public function getNumserie(): ?string { return $this->numserie; }
    public function setNumserie(string $numserie): self { $this->numserie = $numserie; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    /** @return Collection<int, ReservationTaxi> */
    public function getReservations(): Collection { return $this->reservations; }

    public function addReservation(ReservationTaxi $reservation): self {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setVehicule($this);
        }
        return $this;
    }

    public function removeReservation(ReservationTaxi $reservation): self {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getVehicule() === $this) {
                $reservation->setVehicule(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->marque . ' ' . $this->modele . ' (' . $this->immatriculation . ')';
    }
}
