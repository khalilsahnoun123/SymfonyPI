<?php
// src/Entity/Reclamation.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Reclamation
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $utilisateurId;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(type: "string", length: 100)]
    private string $status;

    // src/Entity/Reclamation.php

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reponse = null;

    #[ORM\ManyToOne(targetEntity: TypeReclamation::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(nullable: false)]
    private TypeReclamation $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $attachment = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $note = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaireSatisfaction = null;


    // --- Getters and Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateurId(): ?int
    {
        return $this->utilisateurId;
    }

    public function setUtilisateurId(int $utilisateurId): self
    {
        $this->utilisateurId = $utilisateurId;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
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

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): self
    {
        $this->reponse = $reponse;
        return $this;
    }

    public function getType(): ?TypeReclamation
    {
        return $this->type;
    }

    public function setType(TypeReclamation $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getAttachment(): ?string
    {
    return $this->attachment;
    }

    public function setAttachment(?string $attachment): self
    {
        $this->attachment = $attachment;
        return $this;
    }
    
    public function getNote(): ?int 
    { 
        return $this->note;
    }

    public function setNote(?int $note): self
     { 
        $this->note = $note; return $this; 
    }

    public function getCommentaireSatisfaction(): ?string 
    { 
        return $this->commentaireSatisfaction; 
    }

    public function setCommentaireSatisfaction(?string $commentaireSatisfaction): self 
    {
        $this->commentaireSatisfaction = $commentaireSatisfaction; return $this;
    }


}
