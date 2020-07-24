<?php

namespace App\Controller;

use App\Repository\EtudiantRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EtudiantController extends AbstractController
{
    /**
     * @Route("/etudiant/{id}", name="etudiant_accueil")
     */
    public function profil($id = null, EtudiantRepository $repo)
    {
        if($id === null || $id < 1)
        {
            return $this->render('etudiant/accueil.html.twig');
        }
        else
        {
            $etudiant = $repo->find($id);

            return $this->render('etudiant/etudiant.html.twig', [
                'etudiant' => $etudiant
            ]);
        }
    }
}
