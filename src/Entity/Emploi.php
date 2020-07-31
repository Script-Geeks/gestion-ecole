<?php

namespace App\Entity;

use App\Repository\EmploiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmploiRepository::class)
 */
class Emploi
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $heure_debut;

    /**
     * @ORM\Column(type="integer")
     */
    private $heure_fin;

    /**
     * @ORM\ManyToOne(targetEntity=Jours::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $jour;

    /**
     * @ORM\ManyToOne(targetEntity=Element::class, inversedBy="emplois")
     * @ORM\JoinColumn(nullable=false)
     */
    private $element;

    /**
     * @ORM\ManyToOne(targetEntity=Professeur::class, inversedBy="emplois")
     * @ORM\JoinColumn(nullable=false)
     */
    private $professeur;

    /**
     * @ORM\ManyToOne(targetEntity=Classe::class, inversedBy="emplois")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classe;

    /**
     * @ORM\ManyToOne(targetEntity=Filiere::class, inversedBy="emplois")
     * @ORM\JoinColumn(nullable=false)
     */
    private $filiere;

    /**
     * @ORM\ManyToOne(targetEntity=Niveau::class, inversedBy="emplois")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveau;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeureDebut(): ?int
    {
        return $this->heure_debut;
    }

    public function setHeureDebut(int $heure_debut): self
    {
        $this->heure_debut = $heure_debut;

        return $this;
    }

    public function getHeureFin(): ?int
    {
        return $this->heure_fin;
    }

    public function setHeureFin(int $heure_fin): self
    {
        $this->heure_fin = $heure_fin;

        return $this;
    }

    public function getJour(): ?Jours
    {
        return $this->jour;
    }

    public function setJour(?Jours $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    public function getElement(): ?Element
    {
        return $this->element;
    }

    public function setElement(?Element $element): self
    {
        $this->element = $element;

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
