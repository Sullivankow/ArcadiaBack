<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Horaire
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $jour;

    #[MongoDB\Field(type: "string")]
    private $heureOuverture;

    #[MongoDB\Field(type: "string")]
    private $heureFermeture;

    #[MongoDB\Field(type: "string")]
    private $saison;

    // Getters et Setters

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(string $jour): self
    {
        $this->jour = $jour;
        return $this;
    }

    public function getHeureOuverture(): ?string
    {
        return $this->heureOuverture;
    }

    public function setHeureOuverture(string $heureOuverture): self
    {
        $this->heureOuverture = $heureOuverture;
        return $this;
    }

    public function getHeureFermeture(): ?string
    {
        return $this->heureFermeture;
    }

    public function setHeureFermeture(string $heureFermeture): self
    {
        $this->heureFermeture = $heureFermeture;
        return $this;
    }

    public function getSaison(): ?string
    {
        return $this->saison;
    }

    public function setSaison(string $saison): self
    {
        $this->saison = $saison;
        return $this;
    }
}
