<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["animal:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(["animal:read", "animal:write", "habitat:read"])]  // Spécifie les groupes de sérialisation
    private ?string $prenom = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(["animal:read", "animal:write"])]
    private ?string $etat = null;

    /**
     * @var Collection<int, RapportVeterinaire>
     */
    #[ORM\OneToMany(targetEntity: RapportVeterinaire::class, mappedBy: 'animal')]
    #[Groups(["animal:read"])]  // Ne pas sérialiser les rapports dans "write"
    #[MaxDepth(1)]  // Limite la profondeur de la sérialisation pour éviter les boucles infinies
    private Collection $rapportVeterinaires;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[Groups(["animal:read", "animal:write"])]  // Sérialisation de l'habitat dans les groupes "animal:read" et "animal:write"
    #[MaxDepth(1)]  // Limite la profondeur de la sérialisation
    private ?Habitat $habitat = null;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[Groups(["animal:read", "animal:write"])]  // Sérialisation de la race dans les groupes "animal:read" et "animal:write"
    #[MaxDepth(1)] // Limite la profondeur de la sérialisation
    private ?Race $race = null;

    public function __construct()
    {
        $this->rapportVeterinaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, RapportVeterinaire>
     */
    public function getRapportVeterinaires(): Collection
    {
        return $this->rapportVeterinaires;
    }

    public function addRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): static
    {
        if (!$this->rapportVeterinaires->contains($rapportVeterinaire)) {
            $this->rapportVeterinaires->add($rapportVeterinaire);
            $rapportVeterinaire->setAnimal($this);
        }

        return $this;
    }

    public function removeRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): static
    {
        if ($this->rapportVeterinaires->removeElement($rapportVeterinaire)) {
            // set the owning side to null (unless already changed)
            if ($rapportVeterinaire->getAnimal() === $this) {
                $rapportVeterinaire->setAnimal(null);
            }
        }

        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): static
    {
        $this->race = $race;

        return $this;
    }
}
