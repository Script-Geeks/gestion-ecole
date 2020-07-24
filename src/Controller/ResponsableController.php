<?php

namespace App\Controller;

use App\Repository\ModuleRepository;
use App\Repository\FiliereRepository;
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
     * @Route("/responsable/etudiants", name="responsable_etudiant")
     */
    public function gestion_etudiant(EtudiantRepository $repo_etudiant, FiliereRepository $repo_filiere)
    {
        $etudiants = $repo_etudiant->findAll();
        return $this->render('responsable/etudiants.html.twig', [
            'etudiants' => $etudiants
        ]);
    }   

    /**
     * @Route("/responsable/modules", name="responsable_module")
     */
    public function gestion_module(ModuleRepository $repo_module)
    {
        $modules = $repo_module->findAll();
        return $this->render('responsable/modules.html.twig', [
            'modules' => $modules
        ]);
    }
}
