<?php

namespace App\Entity;

use App\Repository\MaterialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
class Material
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'material', targetEntity: Ingredient::class)]
    private Collection $material;

    public function __construct()
    {
        $this->material = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Ingredient>
     */
    public function getMaterial(): Collection
    {
        return $this->material;
    }

    public function addMaterial(Ingredient $material): self
    {
        if (!$this->material->contains($material)) {
            $this->material->add($material);
            $material->setMaterial($this);
        }

        return $this;
    }

    public function removeMaterial(Ingredient $material): self
    {
        if ($this->material->removeElement($material)) {
            // set the owning side to null (unless already changed)
            if ($material->getMaterial() === $this) {
                $material->setMaterial(null);
            }
        }

        return $this;
    }
}
