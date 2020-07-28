<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Module;
use App\Entity\Filiere;
use App\Form\ModuleType;
use App\Form\FiliereType;
use App\Entity\Professeur;
use App\Form\EnseignantType;
use App\Repository\UserRepository;
use App\Repository\ModuleRepository;
use Doctrine\DBAL\Driver\Connection;
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
    public function gestion_etudiant(EtudiantRepository $repo_etudiant)
    {
        $etudiants = $repo_etudiant->findAll();
        return $this->render('responsable/etudiants.html.twig', [
            'etudiants' => $etudiants
        ]);
    }   

    /**
     * @Route("/responsable/filieres", name="responsable_filiere")
     */
    public function gestion_filiere(FiliereRepository $repo_filiere)
    {
        $filieres = $repo_filiere->findAll();
        return $this->render('responsable/filieres.html.twig', [
            'filieres' => $filieres
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
     * @Route("/responsable/enseignant/modification/{id}", name="responsable_modification_enseignant")
     */
    public function ajout_enseignant(Connection $connection, $id = null, UserRepository $repo_user, ProfesseurRepository $repo_enseignant, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        if($id == null){
            $enseignant = new Professeur();
            $mail_title = 'Profil ajouté avec succès';
        }else{
            $enseignant = $repo_enseignant->find($id);
            $user_array = $connection->fetchAll("SELECT * FROM user WHERE professeur_id = $id");
            $user = $repo_user->find($user_array[0]['id']);
            $mail_title = 'Profil modifié avec succès';
        }
        
        $form = $this->createForm(EnseignantType::class, $enseignant);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($enseignant);
            
            $manager->flush();
            
            if($id == null){
                $user = new User();
            }
            
            $pwd = '';
            if($id == null){
                $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                for($i = 0; $i <= 6; $i++){
                    $pwd .= $x[rand(0, strlen($x)-1)];
                }
            }else{
                $pwd = $enseignant->password;
            }

            if($user->getPassword() !== $pwd){
                $hash = $encoder->encodePassword($user, $pwd);
                $user->setPassword($hash);
                $body = strtoupper($enseignant->getNom())." ".$enseignant->getPrenom().",\nVous pouvez vous authentifier en utilisant les informations ci-dessous.\n
                    E-mail: ".$enseignant->getEmail()."\n
                    Mot de passe: ".$pwd;
            }else{
                $body = strtoupper($enseignant->getNom())." ".$enseignant->getPrenom().",\nVotre profil a été modifié par le responsable avec succès.";
            }
            $user->setUsername($enseignant->getNom())
                 ->setEmail($enseignant->getEmail())
                 ->setProfesseur($enseignant);
            
            $manager->persist($user);
            
            $manager->flush();
            
            $message = (new \Swift_Message($mail_title))
                ->setFrom('insea.inscription@gmail.com')
                ->setTo($enseignant->getEmail())
                ->setBody(
                    $body,
                    'text/plain'
                );
            $mailer->send($message);
            
            return $this->redirectToRoute('responsable_enseignant');
        }
        
        if($id == null){
            return $this->render('responsable/enseignant_ajout.html.twig', [
                'form_enseignant' => $form->createView(),
                'pwd' => 0,
                'title' => 'L\'ajout d\'un enseignat',
                'button' => 'Ajouter'
            ]);
        }else{
            return $this->render('responsable/enseignant_ajout.html.twig', [
                'form_enseignant' => $form->createView(),
                'pwd' => 1,
                'title' => "La modification des informations de :\n".$enseignant->getNom()." ".$enseignant->getPrenom(),
                'password' => $user_array[0]['password'],
                'button' => 'Modifier'
            ]);
        }
    }

    /**
     * @Route("/responsable/enseignant/suppression/{id}", name="responsable_suppression_enseignant")
     */
    public function delete_enseignant(Connection $connection, $id, ProfesseurRepository $repo_enseignant, UserRepository $repo_user, EntityManagerInterface $manager)
    {
        $enseignant = $repo_enseignant->find($id);
        $user_array = $connection->fetchAll("SELECT * FROM user WHERE professeur_id = $id");
        $user = $repo_user->find($user_array[0]['id']);

        $manager->remove($user);
        $manager->remove($enseignant);
        $manager->flush();

        return $this->redirectToRoute('responsable_enseignant');
    }

    /**
     * @Route("/responsable/filiere/ajout", name="responsable_ajout_filiere")
     * @Route("/responsable/filiere/modification/{id}", name="responsable_modification_filiere")
     */
    public function ajout_filiere($id = null, FiliereRepository $repo_filiere, Request $request, EntityManagerInterface $manager)
    {
        if($id == null){
            $filiere = new Filiere();
        }else{
            $filiere = $repo_filiere->find($id);
        }
        $form = $this->createForm(FiliereType::class, $filiere);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($filiere);
            
            $manager->flush();

            return $this->redirectToRoute('responsable_filiere');
        }
        if($id == null){
            return $this->render('responsable/filiere_ajout.html.twig', [
                'form_filiere' => $form->createView(),
                'button' => 'Ajouter',
                'title' => 'L\'ajout d\'une filière'
            ]);
        }else{
            return $this->render('responsable/filiere_ajout.html.twig', [
                'form_filiere' => $form->createView(),
                'button' => 'Modifer',
                'title' => 'La modification d\'une filière'
            ]);
        }
    }
    
    /**
     * @Route("/responsable/modules", name="responsable_module")
     * @Route("/responsable/{id}/modules", name="responsable_filiere_modules")
     */
    public function gestion_module($id = null, FiliereRepository $repo_filiere, ModuleRepository $repo_module)
    {
        if($id == null){
            $modules = $repo_module->findAll();
             return $this->render('responsable/modules.html.twig', [
                'modules' => $modules
            ]);
        }else{
            $filiere = $repo_filiere->find($id);
            $modules = $filiere->getModules();
            return $this->render('responsable/modules.html.twig', [
                'modules' => $modules,
                'header' => $filiere->getNom()
            ]);
        }
    }

    /**
     * @Route("/responsable/modules/ajout", name="responsable_ajout_module")
     */
    public function ajout_module($id = null, ModuleRepository $repo_module, Request $request)
    {
        if($id == null){
            $module = new Module();
        }else{
            $module = $repo_module->find($id);
        }
        $form = $this->createForm(ModuleType::class, $module);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($module);
            
            $manager->flush();
        }

        if($id == null){
            return $this->render('responsable/module_ajout.html.twig', [
                'form_module' => $form->createView(),
                'button' => 'Ajouter',
                'title' => 'L\'ajout d\'un module'
            ]);
        }else{
            return $this->render('responsable/module_ajout.html.twig', [
                'form_module' => $form->createView(),
                'button' => 'Modifer',
                'title' => 'La modification d\'un module'
            ]);
        }
    }
}
