<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class Avis
{
    #[ODM\Id(strategy: "AUTO")]
    private ?string $id = null;

    #[ODM\Field(type: "string")]
    private string $auteur;

    #[ODM\Field(type: "string")]
    private string $contenu;

    #[ODM\Field(type: "date")]
    private \DateTime $date;

    #[ODM\Field(type: "bool")]
    private bool $valide;

    #[ODM\Field(type: "int")]
    private int $note;

    // Getters et Setters

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function isValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        if ($note < 1 || $note > 5) {
            throw new \InvalidArgumentException('La note doit être comprise entre 1 et 5.');
        }

        $this->note = $note;

        return $this;
    }
}



