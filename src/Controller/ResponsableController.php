<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Professeur;
use App\Form\EnseignantType;
use App\Repository\ModuleRepository;
use App\Repository\FiliereRepository;
use App\Repository\EtudiantRepository;
use App\Repository\ProfesseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @Route("/responsable/enseignants", name="responsable_enseignant")
     */
    public function gestion_enseignant(ProfesseurRepository $repo_enseignant)
    {
        $enseignants = $repo_enseignant->findAll();
        return $this->render('responsable/enseignants.html.twig', [
            'enseignants' => $enseignants
        ]);
    }

    /**
     * @Route("/responsable/enseignant/ajout", name="responsable_ajout_enseignant")
     */
    public function ajout_enseignant(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $enseignant = new Professeur();

        $form = $this->createForm(EnseignantType::class, $enseignant);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($enseignant);
            
            $manager->flush();

            $user = new User();

            $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $pwd = '';
            for($i = 0; $i <= 6; $i++){
                $pwd .= $x[rand(0, strlen($x))];
            }

            $hash = $encoder->encodePassword($user, $pwd);
            $user->setUsername($enseignant->getNom())
                 ->setEmail($enseignant->getEmail())
                 ->setPassword($hash)
                 ->setProfesseur($enseignant);
            
            $message = (new \Swift_Message('Bienvenue avec nous dans l\'INSEA'))
                ->setFrom('insea.inscription@gmail.com')
                ->setTo($enseignant->getEmail())
                ->setBody(
                    "Vous avez été ajouté en tant que professeur, vous pouvez vous authentifier en utilisant les informations ci-dessous.\n
                    E-mail: ".$enseignant->getEmail()."\n
                    Mot de passe: ".$pwd,
                    'text/plain'
                );
            $mailer->send($message);
            
            return $this->redirectToRoute('responsable_enseignant');
        }

        return $this->render('responsable/enseignant_ajout.html.twig', [
            'form_enseignant' => $form->createView()
        ]);
    }
}
