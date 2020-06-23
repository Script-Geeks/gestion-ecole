<?php

namespace App\Controller;

use App\Repository\EtudiantRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResponsableController extends AbstractController
{
    /**
     * @Route("/responsable", name="responsable_accueil")
     */
    public function index()
    {
        return $this->render('responsable/accueil.html.twig');
    }

    /**
     * @Route("/gestion/etudiant", name="responsable_etudiant")
     */
    public function gestion_etudiant(EtudiantRepository $repo_etudiant)
    {
        $etudiants = $repo_etudiant->findAll();
        dump($etudiants);
        return $this->render('responsable/etudiant.html.twig', [
            'etudiants' => $etudiants
        ]);
    }
}
