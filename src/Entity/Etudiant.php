<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EtudiantRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EtudiantRepository::class)
 * @UniqueEntity(
 *  fields={"cin"},
 *  message="Ce code est déjà pris !"
 * )
 * @UniqueEntity(
 *  fields={"cne"},
 *  message="Ce code est déjà pris !"
 * )
 * @UniqueEntity(
 *  fields={"tel_pere"},
 *  message="Ce numero est déjà pris !"
 * )
 */
class Etudiant
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
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateNaissAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cne;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomComplet_pere;

    /**
     * @ORM\Column(type="integer")
     */
    private $tel_pere;

    /**
     * @ORM\ManyToOne(targetEntity=Cycle::class, inversedBy="etudiants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cycle;

    /**
     * @ORM\ManyToOne(targetEntity=Groupe::class, inversedBy="etudiants")
     */
    private $groupe;

    /**
     * @ORM\ManyToOne(targetEntity=Filiere::class, inversedBy="etudiants")
     */
    private $filiere;

    /**
     * @ORM\ManyToOne(targetEntity=Niveau::class, inversedBy="etudiants")
     */
    private $niveau;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="etudiant", cascade={"persist", "remove"})
     */
    private $user;


    /**
     * @ORM\Column(type="string")
     */
    private $imageFilename;

    
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissAt(): ?\DateTimeInterface
    {
        return $this->dateNaissAt;
    }

    public function setDateNaissAt(\DateTimeInterface $dateNaissAt): self
    {
        $this->dateNaissAt = $dateNaissAt;

        return $this;
    }

    public function getCne(): ?string
    {
        return $this->cne;
    }

    public function setCne(string $cne): self
    {
        $this->cne = $cne;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getNomCompletPere(): ?string
    {
        return $this->nomComplet_pere;
    }

    public function setNomCompletPere(string $nomComplet_pere): self
    {
        $this->nomComplet_pere = $nomComplet_pere;

        return $this;
    }

    public function getTelPere(): ?int
    {
        return $this->tel_pere;
    }

    public function setTelPere(int $tel_pere): self
    {
        $this->tel_pere = $tel_pere;

        return $this;
    }

    public function getCycle(): ?Cycle
    {
        return $this->cycle;
    }

    public function setCycle(?Cycle $cycle): self
    {
        $this->cycle = $cycle;

        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

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
    
    public function getImageFilename()
    {
        return $this->imageFilename;
    }

    public function setImageFilename($imageFilename)
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newEtudiant = null === $user ? null : $this;
        if ($user->getEtudiant() !== $newEtudiant) {
            $user->setEtudiant($newEtudiant);
        }

        return $this;
    }
    
}
