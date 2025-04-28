<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ReservationveloRepository;

#[ORM\Entity(repositoryClass: ReservationveloRepository::class)]
#[ORM\Table(name: 'reservationvelo')]
class Reservationvelo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_reservation_velo = null;

    public function getId_reservation_velo(): ?int
    {
        return $this->id_reservation_velo;
    }

    public function setId_reservation_velo(int $id_reservation_velo): self
    {
        $this->id_reservation_velo = $id_reservation_velo;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservationvelos')]
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

    #[ORM\ManyToOne(targetEntity: Velo::class, inversedBy: 'reservationvelos')]
    #[ORM\JoinColumn(name: 'id_velo', referencedColumnName: 'id_velo')]
    private ?Velo $velo = null;

    public function getVelo(): ?Velo
    {
        return $this->velo;
    }

    public function setVelo(?Velo $velo): self
    {
        $this->velo = $velo;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_debut = null;

    public function getDate_debut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDate_debut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_fin = null;

    public function getDate_fin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDate_fin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    
#[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
private ?float $price = null;

public function getPrice(): ?float
{
    return $this->price;
}

public function setPrice(float $price): self
{
    $this->price = $price;
    return $this;
}
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $payment_status = null;

    public function getPayment_status(): ?string
    {
        return $this->payment_status;
    }

    public function setPayment_status(string $payment_status): self
    {
        $this->payment_status = $payment_status;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $payment_ref = null;

    public function getPayment_ref(): ?string
    {
        return $this->payment_ref;
    }

    public function setPayment_ref(string $payment_ref): self
    {
        $this->payment_ref = $payment_ref;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $payment_date = null;

    public function getPayment_date(): ?\DateTimeInterface
    {
        return $this->payment_date;
    }

    public function setPayment_date(?\DateTimeInterface $payment_date): self
    {
        $this->payment_date = $payment_date;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Paiementvelo::class, mappedBy: 'reservationvelo')]
    private Collection $paiementvelos;

    public function __construct()
    {
        $this->paiementvelos = new ArrayCollection();
    }

    /**
     * @return Collection<int, Paiementvelo>
     */
    public function getPaiementvelos(): Collection
    {
        if (!$this->paiementvelos instanceof Collection) {
            $this->paiementvelos = new ArrayCollection();
        }
        return $this->paiementvelos;
    }

    public function addPaiementvelo(Paiementvelo $paiementvelo): self
    {
        if (!$this->getPaiementvelos()->contains($paiementvelo)) {
            $this->getPaiementvelos()->add($paiementvelo);
        }
        return $this;
    }

    public function removePaiementvelo(Paiementvelo $paiementvelo): self
    {
        $this->getPaiementvelos()->removeElement($paiementvelo);
        return $this;
    }

    public function getIdReservationVelo(): ?int
    {
        return $this->id_reservation_velo;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(string $payment_status): static
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    public function getPaymentRef(): ?string
    {
        return $this->payment_ref;
    }

    public function setPaymentRef(string $payment_ref): static
    {
        $this->payment_ref = $payment_ref;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->payment_date;
    }

    public function setPaymentDate(?\DateTimeInterface $payment_date): static
    {
        $this->payment_date = $payment_date;

        return $this;
    }



}
