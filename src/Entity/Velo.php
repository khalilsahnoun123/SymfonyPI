<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\VeloRepository;

#[ORM\Entity(repositoryClass: VeloRepository::class)]
#[ORM\Table(name: 'velo')]
class Velo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_velo = null;

    public function getId_velo(): ?int
    {
        return $this->id_velo;
    }

    public function setId_velo(int $id_velo): self
    {
        $this->id_velo = $id_velo;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Stationvelo::class, inversedBy: 'velos')]
    #[ORM\JoinColumn(name: 'id_station', referencedColumnName: 'id_station')]
    private ?Stationvelo $stationvelo = null;

    public function getStationvelo(): ?Stationvelo
    {
        return $this->stationvelo;
    }

    public function setStationvelo(?Stationvelo $stationvelo): self
    {
        $this->stationvelo = $stationvelo;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $dispo = null;

    public function isDispo(): ?bool
    {
        return $this->dispo;
    }

    public function setDispo(bool $dispo): self
    {
        $this->dispo = $dispo;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: VeloType::class)]
    #[ORM\JoinColumn(name: 'id_type', referencedColumnName: 'id_type')] // Assuming 'id' is the primary key of VeloType
    private ?VeloType $veloType = null;
    
    public function getVeloType(): ?VeloType
    {
        return $this->veloType;
    }
    
    public function setVeloType(?VeloType $veloType): self
    {
        $this->veloType = $veloType;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reservationvelo::class, mappedBy: 'velo')]
    private Collection $reservationvelos;

    public function __construct()
    {
        $this->reservationvelos = new ArrayCollection();
    }

    /**
     * @return Collection<int, Reservationvelo>
     */
    public function getReservationvelos(): Collection
    {
        if (!$this->reservationvelos instanceof Collection) {
            $this->reservationvelos = new ArrayCollection();
        }
        return $this->reservationvelos;
    }

    public function addReservationvelo(Reservationvelo $reservationvelo): self
    {
        if (!$this->getReservationvelos()->contains($reservationvelo)) {
            $this->getReservationvelos()->add($reservationvelo);
        }
        return $this;
    }

    public function removeReservationvelo(Reservationvelo $reservationvelo): self
    {
        $this->getReservationvelos()->removeElement($reservationvelo);
        return $this;
    }

   

    

}
