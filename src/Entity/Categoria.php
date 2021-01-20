<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoriaRepository::class)
 */
class Categoria
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Producte::class, mappedBy="categoria")
     */
    private $productes;

    public function __construct()
    {
        $this->productes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Producte[]
     */
    public function getProductes(): Collection
    {
        return $this->productes;
    }
   
    public function addProducte(Producte $producte): self
    {
        if (!$this->productes->contains($producte)) {
            $this->productes[] = $producte;
            $producte->setCategoria($this);
        }

        return $this;
    }

    public function removeProducte(Producte $producte): self
    {
        if ($this->productes->removeElement($producte)) {
            // set the owning side to null (unless already changed)
            if ($producte->getCategoria() === $this) {
                $producte->setCategoria(null);
            }
        }

        return $this;
    }
}
