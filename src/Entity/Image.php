<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BLOB)]
    private $image_data;

    /**
     * @var Collection<int, habitat>
     */
    #[ORM\ManyToMany(targetEntity: habitat::class, inversedBy: 'images')]
    private Collection $habitat;

    public function __construct()
    {
        $this->habitat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageData()
    {
        return $this->image_data;
    }

    public function setImageData($image_data): static
    {
        $this->image_data = $image_data;

        return $this;
    }

    /**
     * @return Collection<int, habitat>
     */
    public function getHabitat(): Collection
    {
        return $this->habitat;
    }

    public function addHabitat(habitat $habitat): static
    {
        if (!$this->habitat->contains($habitat)) {
            $this->habitat->add($habitat);
        }

        return $this;
    }

    public function removeHabitat(habitat $habitat): static
    {
        $this->habitat->removeElement($habitat);

        return $this;
    }
}
