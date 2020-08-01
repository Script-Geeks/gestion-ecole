<?php

namespace App\Entity;

use App\Entity\Filiere;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ModuleRepository::class)
 */
class Module
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbr_element;

    /**
     * @ORM\OneToMany(targetEntity=Element::class, mappedBy="module")
     */
    private $elements;

    /**
     * @ORM\ManyToOne(targetEntity=Filiere::class, inversedBy="module")
     * @ORM\JoinColumn(
     *      name="filiere_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE",
     *      nullable=false
     * )
     */
    private $filiere;

    /**
     * @ORM\ManyToOne(targetEntity=Niveau::class, inversedBy="modules")
     */
    private $niveau;

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

    public function getNbrElement(): ?int
    {
        return $this->nbr_element;
    }

    public function setNbrElement(int $nbr_element): self
    {
        $this->nbr_element = $nbr_element;

        return $this;
    }

    /**
     * @return Collection|Element[]
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements[] = $element;
            $element->setModule($this);
        }

        return $this;
    }

    public function removeElement(Element $element): self
    {
        if ($this->elements->contains($element)) {
            $this->elements->removeElement($element);
            // set the owning side to null (unless already changed)
            if ($element->getModule() === $this) {
                $element->setModule(null);
            }
        }

        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }
}
