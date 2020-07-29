<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\UserType;
use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Entity\Filiere;
use App\Entity\Professeur;
use App\Repository\UserRepository;
use App\Controller\SecurityController;
use App\Repository\EtudiantRepository;
use App\Repository\PerofesseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Routing\Annotation\Route;

class ProfesseurController extends AbstractController
{
    
   
    /**
     * @Route("/professeur/{id}", name="prof_accueil")
     */
    public function home(Professeur $professeur)
    {
        return $this->render('professeur/accueil.html.twig', [
            'professeur' => $professeur ]);
    }

    /**
     * @Route("/profil/{id}" ,name="professeur_profil")
     */
    public function profil(Professeur $professeur)
    {
        return
         $this->render('professeur/profil.html.twig', [
            'professeur' => $professeur ]);
    }

    
}
