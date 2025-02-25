<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
#[ODM\Document]
class StatsAnimal
{
    #[ODM\Id] // Assurez-vous que cette annotation est correcte
    private ?string $id = null;

    #[ODM\Field(type: "string")]
    private string $animalId;

    #[ODM\Field(type: "string")]
    private ?string $animalName = null; // Prénom de l'animal

    #[ODM\Field(type: "int")]
    private int $clickCount = 0;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAnimalId(): string
    {
        return $this->animalId;
    }

    public function setAnimalId(string $animalId): self
    {
        $this->animalId = $animalId;
        return $this;
    }

    public function getAnimalName(): ?string
    {
        return $this->animalName;
    }

    public function setAnimalName(?string $animalName): self
    {
        $this->animalName = $animalName;
        return $this;
    }

    public function getClickCount(): int
    {
        return $this->clickCount;
    }

    public function incrementClickCount(): void
    {
        $this->clickCount++;
    }
}




