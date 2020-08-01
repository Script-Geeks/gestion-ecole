<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Etudiant;
use App\Entity\Certificat;
use App\Form\EtudiantType;
use App\Repository\UserRepository;
use App\Repository\CycleRepository;
use App\Repository\NiveauRepository;
use App\Repository\FiliereRepository;
use App\Repository\EtudiantRepository;
use App\Repository\ProfesseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResponsableRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
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
    public function registration(Request $request, FiliereRepository $repo_filiere, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $filieres = $repo_filiere->findAll();
        if($filieres == null){
            return $this->render('security/accueil.html.twig', [
                'error' => 1
            ]);
        }

        $etudiant = new Etudiant();
        $user = new User();

        $form = $this->createForm(EtudiantType::class, $etudiant);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ){
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = md5($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }
                $etudiant->setImageFilename($newFilename);
            }
            $etudiant->setAccepted(0)
                     ->setPayed(0);
            $manager->persist($etudiant);

            $pwd = '';
            $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for($i = 0; $i < 6; $i++){
                $pwd .= $x[rand(0, strlen($x)-1)];
            }

            $hash = $encoder->encodePassword($user, $pwd);
            $user->setPassword($hash);
            $body = strtoupper($etudiant->getNom())." ".$etudiant->getPrenom().",\nVotre demande est en cours de traitement. Vous pouvez vous authentifier en utilisant les informations ci-dessous.\n
                E-mail: ".$etudiant->getEmail()."\n
                Mot de passe: ".$pwd;

            $user->setUsername($etudiant->getNom())
                 ->setEmail($etudiant->getEmail())
                 ->setEtudiant($etudiant);
            
            $manager->persist($user);
            
            $manager->flush();

            $message = (new \Swift_Message('Profil ajouté avec succès'))
                ->setFrom('insea.inscription@gmail.com')
                ->setTo($etudiant->getEmail())
                ->setBody(
                    $body,
                    'text/plain'
                );
            $mailer->send($message);
                
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form_etudiant' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(Request $request, UserRepository $repo_user, AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $email = $utils->getLastUsername();
        $user = $repo_user->findBy(array('email'=>$email));
        return $this->render('security/login.html.twig', [
            'error' => $error,
            'email' => $email
        ]);
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
        if($user === null){
            return $this->redirectToRoute('home');
        }else{
            if($user->getResponsable() !== null && $user->getResponsable()->getId() !== null){
                return $this->redirectToRoute('responsable_accueil');
            }elseif($user->getEtudiant() !== null && $user->getEtudiant()->getId() !== null){
                return $this->redirectToRoute('etudiant_accueil', [
                    'id' => $user->getEtudiant()->getId()
                ]);
            }elseif($user->getProfesseur() !== null && $user->getProfesseur()->getId() !== null){
                return $this->redirectToRoute('prof_accueil', [
                    'id' => $user->getProfesseur()->getId()
                ]);
            }
        }
    }

    /**
     * @Route("/recherche/compte", name="recherche_compte")
     */
    public function recherche_compte( EntityManagerInterface $manager, \Swift_Mailer $mailer, UserRepository $repo_user, EtudiantRepository $repo_etudiant, ProfesseurRepository $repo_professeur, ResponsableRepository $repo_responsable)
    {
        if($_POST !== null && count($_POST) == 2){
            $code = '';
            $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for($i = 0; $i < 8; $i++){
                $code .= $x[rand(0, strlen($x)-1)];
            }
            if($_POST["type_user"] == 'responsable'){
                $user_type = $repo_responsable->findOneBy(array('email' => $_POST["email"]));
            }elseif($_POST["type_user"] == 'professeur'){
                $user_type = $repo_professeur->findOneBy(array('email' => $_POST["email"]));
            }elseif($_POST["type_user"] == 'etudiant'){
                $user_type = $repo_etudiant->findOneBy(array('email' => $_POST["email"]));           
            }
            if($user_type === null){
                if( !isset($user) || $user == null){
                    return $this->render('security/recherche_compte.html.twig', [
                        'error' => 1
                        ]);
                }
            }
            $user = $repo_user->findOneBy(array($_POST["type_user"] => $user_type->getId()));
            $user->setConfirmation($code);
            $manager->persist($user);
            $manager->flush();

            $body = strtoupper($user_type->getNom())." ".$user_type->getPrenom().",\nVous trouverez ci-dessous votre code de confirmation pour réinitialiser le mot de passe.\n
                Code de confirmation: ".$code;
            $message = (new \Swift_Message('Réinitialisation du mot de passe'))
                ->setFrom('insea.inscription@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $body,
                    'text/plain'
                );
            $mailer->send($message);

            return $this->redirectToRoute('reinitialisation', [
                'id' => $user->getId()
            ]);
        }else{
            return $this->render('security/recherche_compte.html.twig');
        }
    }

    /**
     * @Route("/reinitialisation/{id}", name="reinitialisation")
     */
    public function reinitialisation($id, UserRepository $repo_user, EntityManagerInterface $manager, \Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder)
    {
        $user = $repo_user->find($id);
        if($_POST !== null && count($_POST) === 2){
            if($user->getConfirmation() === $_POST["code_confirmation"]){
                $user->setPassword($encoder->encodePassword($user, $_POST['password']))
                     ->setConfirmation(null);

                $manager->persist($user);
                $manager->flush();
                $body = "Votre compte a été réinitialiser avec succés. Vous pouvez vous authentifier en utilisant les informations ci-dessous\n
                    E-mail: ".$user->getEmail()."\n
                    Mot de passe: ".$_POST['password'];
                $message = (new \Swift_Message('Réinitialisation du mot de passe'))
                    ->setFrom('insea.inscription@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $body,
                        'text/plain'
                    );
                $mailer->send($message);

                return $this->redirectToRoute('security_login');
            }else{
                return $this->render('security/reinitialisation.html.twig', [
                    'user' => $user,
                    'error' => 1
                ]);
            }
        }
        return $this->render('security/reinitialisation.html.twig', [
            'user' => $user
        ]);
    }
}
