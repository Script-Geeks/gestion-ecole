<?php

namespace App\Entity;

use App\Repository\ElementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ElementRepository::class)
 */
class Element
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
    private $coefficient;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="elements")
     * @ORM\JoinColumn(
     *      name="module_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE",
     *      nullable=false
     * )
     */
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity=Professeur::class, inversedBy="elements")
     * @ORM\JoinColumn(
     *      name="professeur_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE",
     *      nullable=false
     * )
     */
    private $professeur;

    /**
     * @ORM\ManyToOne(targetEntity=Classe::class, inversedBy="elements")
     * @ORM\JoinColumn(
     *      name="classe_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE",
     *      nullable=false
     * )
     */
    private $classe;

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

    public function getCoefficient(): ?int
    {
        return $this->coefficient;
    }

    public function setCoefficient(int $coefficient): self
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getProfesseur(): ?Professeur
    {
        return $this->professeur;
    }

    public function setProfesseur(?Professeur $professeur): self
    {
        $this->professeur = $professeur;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }
}
