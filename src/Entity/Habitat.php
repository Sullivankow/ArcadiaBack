<?php

namespace App\Entity;

use App\Repository\HabitatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: HabitatRepository::class)]
class Habitat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['habitat:read', 'habitat:create', 'habitat:write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 155)]
    #[Groups(['habitat:read', 'habitat:create', 'habitat:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['habitat:read', 'habitat:create', 'habitat:write'])]
    private ?string $commentaire_habitat = null;

    #[ORM\OneToMany(targetEntity: Animal::class, mappedBy: 'habitat')]
    #[Groups(['habitat:read'])]
    private Collection $animals;

    #[ORM\ManyToMany(targetEntity: Image::class, inversedBy: 'habitats', cascade: ['persist', 'remove'])]
    #[Groups(['habitat:read', 'habitat:write'])]
    #[MaxDepth(1)]  // Limiter la profondeur de sérialisation pour éviter la récursion infinie
    #[ORM\JoinTable(name: 'habitat_image')]
    private Collection $images;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCommentaireHabitat(): ?string
    {
        return $this->commentaire_habitat;
    }

    public function setCommentaireHabitat(?string $commentaire_habitat): static
    {
        $this->commentaire_habitat = $commentaire_habitat;

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->setHabitat($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        if ($this->animals->removeElement($animal)) {
            if ($animal->getHabitat() === $this) {
                $animal->setHabitat(null);
            }
        }

        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->addHabitat($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            $image->removeHabitat($this);
        }

        return $this;
    }
}






