<?php

namespace App\Entity\covoiturage;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\covoiturage\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
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
    private ?string $gouvernorat = null;

    public function getGouvernorat(): ?string
    {
        return $this->gouvernorat;
    }

    public function setGouvernorat(?string $gouvernorat): self
    {
        $this->gouvernorat = $gouvernorat;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $municipalite = null;

    public function getMunicipalite(): ?string
    {
        return $this->municipalite;
    }

    public function setMunicipalite(?string $municipalite): self
    {
        $this->municipalite = $municipalite;
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $roles = null;

    public function getRoles(): ?string
    {
        return $this->roles;
    }

    public function setRoles(?string $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $password = null;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $is_verified = null;

    public function is_verified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIs_verified(?bool $is_verified): self
    {
        $this->is_verified = $is_verified;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $first_name = null;

    public function getFirst_name(): ?string
    {
        return $this->first_name;
    }

    public function setFirst_name(?string $first_name): self
    {
        $this->first_name = $first_name;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $last_name = null;

    public function getLast_name(): ?string
    {
        return $this->last_name;
    }

    public function setLast_name(?string $last_name): self
    {
        $this->last_name = $last_name;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $phone_number = null;

    public function getPhone_number(): ?int
    {
        return $this->phone_number;
    }

    public function setPhone_number(?int $phone_number): self
    {
        $this->phone_number = $phone_number;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image = null;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $bloque = null;

    public function getBloque(): ?string
    {
        return $this->bloque;
    }

    public function setBloque(?string $bloque): self
    {
        $this->bloque = $bloque;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Covoiturage::class, mappedBy: 'user')]
    private Collection $covoiturages;

    #[ORM\OneToMany(targetEntity: ReservationCov::class, mappedBy: 'user')]
    private Collection $reservationCovs;

    

    public function __construct()
    {
        $this->covoiturages = new ArrayCollection();
        $this->reservationCovs = new ArrayCollection();
       
        
    }

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoiturages(): Collection
    {
        return $this->covoiturages;
    }

    public function addCovoiturage(Covoiturage $covoiturage): self
    {
        if (!$this->covoiturages->contains($covoiturage)) {
            $this->covoiturages->add($covoiturage);
        }
        return $this;
    }

    public function removeCovoiturage(Covoiturage $covoiturage): self
    {
        $this->covoiturages->removeElement($covoiturage);
        return $this;
    }

    /**
     * @return Collection<int, ReservationCov>
     */
    public function getReservationCovs(): Collection
    {
        return $this->reservationCovs;
    }

    public function addReservationCov(ReservationCov $reservationCov): self
    {
        if (!$this->reservationCovs->contains($reservationCov)) {
            $this->reservationCovs->add($reservationCov);
        }
        return $this;
    }

    public function removeReservationCov(ReservationCov $reservationCov): self
    {
        $this->reservationCovs->removeElement($reservationCov);
        return $this;
    }

  

    public function isVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(?bool $is_verified): static
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?int $phone_number): static
    {
        $this->phone_number = $phone_number;

        return $this;
    }
}
