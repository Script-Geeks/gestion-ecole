<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\UserRepository;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('security/accueil.html.twig');
    }

    /**
     * @Route("/inscription", name="security_registration")
     * @Route("/ajout_Etudiant", name="responsable_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager)
    {
        $etudiant = new Etudiant();

        $form = $this->createForm(EtudiantType::class, $etudiant);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ){

            $manager->persist($etudiant);

            $manager->flush();

            return $this->redirectToRoute('security_authentication', [
                'idEtudiant' => $etudiant->getId()
            ]);
            
        }

        return $this->render('security/registration.html.twig', [
            'form_etudiant' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/{idEtudiant}/authentification", name="security_authentication")
     */
    public function authentication(EtudiantRepository $repo_etudiant, $idEtudiant = null, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        if($idEtudiant === null){
            return $this->redirectToRoute('home');
        }else{
            $etudiant = $repo_etudiant->find($idEtudiant);
            if($etudiant->getUser() !== null){
                return $this->redirectToRoute('home');
            }else{
                $user = new User();

                $form = $this->createForm(UserType::class, $user);

                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()){

                    $hash = $encoder->encodePassword($user, $user->getPassword());
                    $user->setUsername($etudiant->getNom())
                         ->setPassword($hash)
                         ->setEtudiant($etudiant);
                    
                    $manager->persist($user);
                    
                    $manager->flush();

                    $admin = $this->getUser();
                    if($admin !== null && $admin->getResponsable() !== null){
                            return $this->redirectToRoute('responsable_accueil');
                    }
                    return $this->redirectToRoute('security_login');
                }

                return $this->render('security/authentication.html.twig', [
                    'form_authentication' => $form->createView()
                ]);
            }
        }   
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout() {}

    /**
     * @Route("/connexion_redirection", name="login_redirect")
     */
    public function login_redirect()
    {
        $user = $this->getUser();
        dump($user);
        if($user !== null){
            if($user->getResponsable() !== null && $user->getResponsable()->getId() !== null){
                return $this->redirectToRoute('responsable_accueil');
            }elseif($user->getEtudiant() !== null && $user->getEtudiant()->getId() !== null){
                return $this->redirectToRoute('etudiant_accueil');
            }elseif($user->getProfesseur() !== null && $user->getProfesseur()->getId() !== null){
                return $this->redirectToRoute('responsable_accueil');
            }
        }
    }
}
