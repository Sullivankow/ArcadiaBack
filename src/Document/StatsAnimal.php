<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]

class StatsAnimal
{

    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: "string")]
    private string $animalId;

    #[ODM\Field(type: "int")]
    private int $clickCount = 0;


public function getId() {
        return $this->id;
}


public function getAnimalId() {
        return $this->animalId;
}

public function setAnimalId(string $animalId) {
        return $this->animalId = $animalId;
}

public function getClickCount(): int {
        return $this->clickCount;
}

public function incrementClickCount():void{
        $this->clickCount++;
}


}