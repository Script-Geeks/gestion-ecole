<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Classe;
use App\Entity\Emploi;
use App\Entity\Module;
use App\Entity\Element;
use App\Entity\Filiere;
use App\Form\ClassType;
use App\Form\EmploiType;
use App\Form\ModuleType;
use App\Form\ElementType;
use App\Form\FiliereType;
use App\Entity\Professeur;
use App\Entity\Responsable;
use App\Form\EnseignantType;
use App\Form\ResponsableType;
use App\Repository\UserRepository;
use App\Repository\JoursRepository;
use App\Repository\ClasseRepository;
use App\Repository\EmploiRepository;
use App\Repository\ModuleRepository;
use App\Repository\NiveauRepository;
use Doctrine\DBAL\Driver\Connection;
use App\Repository\ElementRepository;
use App\Repository\FiliereRepository;
use App\Repository\EtudiantRepository;
use App\Repository\ProfesseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CertificatsRepository;
use App\Repository\ResponsableRepository;
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
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('responsable/accueil.html.twig');
    }

    /**
     * @Route("responsable/profil", name="responsable_profil")
     */
    public function responsable_profil(ResponsableRepository $repo_responsable)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $responsable = $repo_responsable->find($this->getUser()->getResponsable()->getId());
        return $this->render('responsable/profil.html.twig', [
            'responsable' => $responsable
        ]);
    }
 
    /**
     * @Route("/responsable/ajout", name="responsable_ajout")
     * @Route("/responsable/modification/{id}", name="responsable_modification")
     */
    public function ajout_responsable(Connection $connection, $id = null, UserRepository $repo_user, ResponsableRepository $repo_responsable, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        if($id == null){
            $responsable = new Responsable();
            $user = new User();
            $mail_title = 'Profil ajouté avec succès';
        }else{
            $responsable = $repo_responsable->find($id);
            $user_array = $connection->fetchAll("SELECT * FROM user WHERE responsable_id = $id");
            $user = $repo_user->find($user_array[0]['id']);
            $mail_title = 'Profil modifié avec succès';
        }
        $form = $this->createForm(ResponsableType::class, $responsable);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($responsable);

            $manager->flush();

            $pwd = '';
            if($id == null){
                $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                for($i = 0; $i < 6; $i++){
                    $pwd .= $x[rand(0, strlen($x)-1)];
                }
            }else{
                $pwd = $responsable->password;
            }

            if($user->getPassword() !== $pwd){
                $hash = $encoder->encodePassword($user, $pwd);
                $user->setPassword($hash);
                $body = strtoupper($responsable->getNom())." ".$responsable->getPrenom().",\nVous pouvez vous authentifier en utilisant les informations ci-dessous.\n
                    E-mail: ".$responsable->getEmail()."\n
                    Mot de passe: ".$pwd;
            }else{
                $body = strtoupper($responsable->getNom())." ".$responsable->getPrenom().",\nVotre profil a été modifié avec succès.";
            }
            $user->setUsername($responsable->getNom())
                 ->setEmail($responsable->getEmail())
                 ->setResponsable($responsable);
            
            $manager->persist($user);
            
            $manager->flush();

            $user_now = $this->getUser();
            $message = (new \Swift_Message($mail_title))
                ->setFrom('insea.inscription@gmail.com')
                ->setTo($responsable->getEmail())
                ->setBody(
                    $body,
                    'text/plain'
                );
            $mailer->send($message);
            
            if($id !== null){
                return $this->redirectToRoute('responsable_profil');
            }else{
                return $this->render('responsable/accueil.html.twig', [
                    'success' => 1
                ]);
            }
        }

        if($id == null){
            return $this->render('responsable/responsable_ajout.html.twig', [
                'form_responsable' => $form->createView(),
                'pwd' => 0,
                'title' => 'L\'ajout d\'un enseignat',
                'button' => 'Ajouter'
            ]);
        }else{
            return $this->render('responsable/responsable_ajout.html.twig', [
                'form_responsable' => $form->createView(),
                'pwd' => 1,
                'title' => "La modification des informations de :\n",
                'nomComplet' => $responsable->getNom()." ".$responsable->getPrenom(),
                'password' => $user_array[0]['password'],
                'button' => 'Modifier'
            ]);
        }
    }

    /**
     * @Route("/responsable/niveau/{id}/filiere/{idFil}/etudiants", name="responsable_etudiant")
     */
    public function gestion_etudiant($id = null, $idFil = null, FiliereRepository $repo_filiere, NiveauRepository $repo_niveau, EtudiantRepository $repo_etudiant)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $niveau = $repo_niveau->find($id);
        $filiere = $repo_filiere->find($idFil);
        $etudiants = $repo_etudiant->findBy(array('accepted'=>1, 'niveau'=>$niveau, 'filiere'=>$filiere));
        return $this->render('responsable/etudiants.html.twig', [
            'etudiants' => $etudiants,
            'niveau' => $niveau,
            'filiere' => $filiere
        ]);
    }   

    /**
     * @Route("/responsable/etudiant/suppression/{id}", name="responsable_suppression_etudiant")
     */
    public function delete_etudiant(Connection $connection, $id, EtudiantRepository $repo_etudiant, UserRepository $repo_user, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }
        $etudiant = $repo_etudiant->find($id);
        $idfil = $etudiant->getFiliere()->getId();
        $idniv = $etudiant->getNiveau()->getId();

        $user_array = $connection->fetchAll("SELECT * FROM user WHERE etudiant_id = $id");
        $user = $repo_user->find($user_array[0]['id']);

        $manager->remove($user);
        $manager->remove($etudiant);
        $manager->flush();

        return $this->redirectToRoute('responsable_etudiant', ['id' => $idniv, 'idFil' => $idfil]);
    }

    /**
     * @Route("/responsable/niveau/{type}", name="responsable_niveau")
     */
    public function gestion_niveau($type = null, NiveauRepository $repo_niveau)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $niveaux = $repo_niveau->findAll();
        if($type == 'etudiants'){
            $header = 'Les étudiants';
        }elseif($type == 'modules'){
            $header = 'Les modules';
        }
        return $this->render('responsable/niveaux.html.twig', [
            'niveaux' => $niveaux,
            'header' => $header,
            'type' => $type
        ]);
    }

    /**
     * @Route("responsable/{type}/filiere/{id}/niveau", name="responsable_niveau_filiere")
     */
    public function niveau_filiere($type = null, $id = null, NiveauRepository $repo_niveau, FiliereRepository $repo_filiere)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $filieres = $repo_filiere->findAll();
        if($type == 'etudiants'){
            $path = 'responsable_etudiant';
            $header = 'Les étudiants';
        }elseif($type == 'modules'){
            $path = 'responsable_module';
            $header = 'Les modules';
        }
        $niveau = $repo_niveau->find($id);
        return $this->render('responsable/niveau_filiere.html.twig', [
            'filieres' => $filieres,
            'path' => $path,
            'niveau' => $niveau,
            'header' => $header
        ]);
    }
    
    /**
     * @Route("/responsable/releve/notes/{idFil}/{idNiv}", name="responsable_releves")
     */
    public function releve_note($idFil, $idNiv, FiliereRepository $repo_filiere, NiveauRepository $repo_niveau, EtudiantRepository $repo_etudiant)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $etudiants = $repo_etudiant->findBy(array('niveau'=>$idNiv, 'filiere'=>$idFil));
        $niveau = $repo_niveau->find($idNiv);
        $filiere = $repo_filiere->find($idFil);
        return $this->render('responsable/releve_notes.html.twig', [
            'etudiants' => $etudiants,
            'filiere' => $filiere,
            'niveau' => $niveau
        ]);
    }

    /**
     * @Route("/responsable/filieres", name="responsable_filiere")
     * @Route("/responsable/filieres/d/{demandes}", name="responsable_filiere_demandes")
     * @Route("/responsable/filieres/p/{paiment}", name="responsable_filiere_paiment")
     * @Route("/responsable/filieres/s/{scolarite}", name="responsable_filiere_scolarite")
     * @Route("/responsable/filieres/e/{emploi}", name="responsable_emploi")
     * @Route("/responsable/filieres/n/{notes}", name="responsable_releve_notes")
     */
    public function gestion_filiere( $notes = null, $emploi = null,$scolarite = null, $paiment = null, $demandes = null, NiveauRepository $repo_niveau, FiliereRepository $repo_filiere)
    {
        if($emploi !== null){
            if($this->getUser() === null ){
                return $this->redirectToRoute('home');
            }
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $filieres = $repo_filiere->findAll();
        $niveaux = $repo_niveau->findAll();
        return $this->render('responsable/filieres.html.twig', [
            'filieres' => $filieres,
            'niveaux' => $niveaux,
            'demandes' => $demandes,
            'paiment' => $paiment,
            'scolarite' => $scolarite,
            'emploi' => $emploi,
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/responsable/demandes/scolarite/{idFil}/{idNiv}/etudiant", name="responsable_demandes_etudiant")
     */
    public function demandes_scolarite( $idFil, $idNiv, NiveauRepository $repo_niveau, FiliereRepository $repo_filiere, CertificatsRepository $repo_certificats)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $filiere = $repo_filiere->find($idFil);
        $niveau = $repo_niveau->find($idNiv);
        $etudiants_certificats = $repo_certificats->findBy(array('accepted'=> 0));
        dump($etudiants_certificats);
        return $this->render('responsable/scolarite_demande.html.twig', [
            'etudiants_certificats' => $etudiants_certificats,
            'filiere' => $filiere,
            'niveau' => $niveau
            ]);
    }

    /**
     * @Route("/responsable/demandes/etudiant/{id}/{idFil}/{idNiv}/accepter", name="responsable_demandes_accepter")
     */
    public function accepter_demandes_scolarite( $id, $idFil, $idNiv, CertificatsRepository $repo_certificats, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $certificat_array = $repo_certificats->findBy(array('etudiant' => $id, 'accepted' => 0));
        $certificat = $repo_certificats->find($certificat_array[0]->getId());
        $certificat->setAccepted(1)
                   ->setIssuedAt(new \DateTime());
        $manager->persist($certificat);
        $manager->flush();

        return $this->redirectToRoute('responsable_demandes_etudiant', ['id' => $id, 'idNiv'=> $idNiv, 'idFil'=> $idFil]);
    }

    /**
     * @Route("responsable/{idFil}/{idNiv}/suppression/{id}/demande/scolarite", name="responsable_suppression_demande_scolarite")
     */
    public function suppression_demande_scolarite( $id, $idFil, $idNiv, CertificatsRepository $repo_certificat, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $certificat = $repo_certificat->find($id);
        $manager->remove($certificat);
        $manager->flush();
        return $this->redirectToRoute('responsable_demandes_etudiant', ['id' => $id, 'idNiv'=> $idNiv, 'idFil'=> $idFil]);
    }

    /**
     * @Route("/responsable/demandes/{idFil}/{idNiv}/inscription", name="responsable_demandes_inscription")
     */
    public function demandes_inscription( $idFil, $idNiv, FiliereRepository $repo_filiere, NiveauRepository $repo_niveau, EtudiantRepository $repo_etudiant)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $filiere = $repo_filiere->find($idFil);
        $niveau = $repo_niveau->find($idNiv);
        $etudiants_non_acceptes = $repo_etudiant->findBy(array('accepted'=> 0, 'filiere'=> $idFil, 'niveau'=> $idNiv));
        return $this->render('responsable/demandes.html.twig', [
            'etudiants_non_acceptes' => $etudiants_non_acceptes,
            'filiere' => $filiere,
            'niveau' => $niveau
            ]);
    }
        
    /**
     * @Route("/responsable/etudiant/{id}/{idFil}/{idNiv}/accepter", name="responsable_etudiant_accepter")
     */
    public function accepter_etudiant( $id, $idFil, $idNiv, EtudiantRepository $repo_etudiant, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $etudiant = $repo_etudiant->find($id);
        $etudiant->setAccepted(1);
        $manager->persist($etudiant);
        $manager->flush();

        return $this->redirectToRoute('responsable_demandes_inscription', ['idNiv'=> $idNiv, 'idFil'=> $idFil]);
    }

    /**
     * @Route("/responsable/paiment/{idFil}/{idNiv}/inscription", name="responsable_paiment_etudiant")
     */
    public function demandes_paiment( $idFil, $idNiv, FiliereRepository $repo_filiere, NiveauRepository $repo_niveau, EtudiantRepository $repo_etudiant)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $filiere = $repo_filiere->find($idFil);
        $niveau = $repo_niveau->find($idNiv);
        $etudiants_non_payes = $repo_etudiant->findBy(array('payed'=> 0, 'filiere'=> $idFil, 'niveau'=> $idNiv));
        return $this->render('responsable/paiment.html.twig', [
            'etudiants_non_payes' => $etudiants_non_payes,
            'filiere' => $filiere,
            'niveau' => $niveau
            ]);
    }

    /**
     * @Route("/responsable/etudiant/{id}/{idFil}/{idNiv}/payer", name="responsable_etudiant_payer")
     */
    public function payer_etudiant( $id, $idFil, $idNiv, EtudiantRepository $repo_etudiant, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $etudiant = $repo_etudiant->find($id);
        $etudiant->setPayed(1);
        $manager->persist($etudiant);
        $manager->flush();

        return $this->redirectToRoute('responsable_paiment_etudiant', ['idNiv'=> $idNiv, 'idFil'=> $idFil]);
    }
    
    /**
     * @Route("/responsable/enseignants", name="responsable_enseignant")
     */
    public function gestion_enseignant(ProfesseurRepository $repo_enseignant)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
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
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($id !== null){
                if($this->getUser()->getResponsable() === null && $this->getUser()->getProfesseur() === null  ){
                    return $this->redirectToRoute('home');
                }
            }else{
                if($this->getUser()->getResponsable() === null){
                    return $this->redirectToRoute('home');
                }
            }
        }
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
                for($i = 0; $i < 6; $i++){
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

            $user_now = $this->getUser();
            $message = (new \Swift_Message($mail_title))
            ->setFrom('insea.inscription@gmail.com')
            ->setTo($enseignant->getEmail())
            ->setBody(
                $body,
                'text/plain'
            );
            $mailer->send($message);
            
            if($user_now->getResponsable() != null ){
                return $this->redirectToRoute('responsable_enseignant');
            }else{
                return $this->redirectToRoute('prof_accueil', ['id'=> $user->getProfesseur()->getId()]);
            }
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
                'title' => "La modification des informations de :\n",
                'nomComplet' => $enseignant->getNom()." ".$enseignant->getPrenom(),
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
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null && $this->getUser()->getProfesseur() === null  ){
                return $this->redirectToRoute('home');
            }   
        }
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
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
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
     * @Route("/responsable/filiere/suppression/{id}", name="responsable_suppression_filiere")
     */
    public function delete_filiere($id, FiliereRepository $repo_filiere, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $filiere = $repo_filiere->find($id);

        $manager->remove($filiere);
        $manager->flush();

        return $this->redirectToRoute('responsable_filiere');
    }
    
    /**
     * @Route("/responsable/niveau/{id}/filiere/{idFil}/modules", name="responsable_module")
     * @Route("/responsable/filiere/{idFil}/modules", name="responsable_filiere_modules")
     */
    public function gestion_module($id = null, $idFil = null, NiveauRepository $repo_niveau, FiliereRepository $repo_filiere, ModuleRepository $repo_module)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        if($id !== null){
            $niveau = $repo_niveau->find($id);
            $filiere = $repo_filiere->find($idFil);
            $modules = $repo_module->findBy(array('niveau'=>$niveau, 'filiere'=>$filiere));
             return $this->render('responsable/modules.html.twig', [
                'modules' => $modules,
                'niveau' => $niveau,
                'filiere' => $filiere
            ]);
        }else{
            $filiere = $repo_filiere->find($id);
            $modules = $filiere->getModule();
            return $this->render('responsable/modules.html.twig', [
                'modules' => $modules,
                'header' => $filiere->getNom(),
                'niveau' => $niveau,
                'filiere' => $filiere
            ]);
        }
    }

    /**
     * @Route("/responsable/modules/ajout", name="responsable_ajout_module")
     * @Route("/responsable/modules/modification/{id}", name="responsable_modification_module")
     */
    public function ajout_module($id = null, FiliereRepository $repo_filiere, NiveauRepository $repo_niveau, ModuleRepository $repo_module, Request $request, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $niveaux = $repo_niveau->findAll();
        $filieres = $repo_filiere->findAll();
        if($niveaux == null || $filieres == null){
            return $this->render('responsable/accueil.html.twig', [
                'error' => 1
            ]);
        }

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

            return $this->redirectToRoute('responsable_module', ['idFil' => $module->getFiliere()->getId(), 'id' => $module->getNiveau()->getId()]);
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

    /**
     * @Route("/responsable/module/{idniv}/{idfil}/{id}/suppression", name="responsable_module_suppression")
     */
    public function delete_module($id = null, $idniv, $idfil, ModuleRepository $repo_module, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $module = $repo_module->find($id);

        $manager->remove($module);
        $manager->flush();

        return $this->redirectToRoute('responsable_module', ['id' => $idniv, 'idFil' => $idfil]);
    }
    
    /**
     * @Route("/responsable/classes", name="responsable_classe")
     */
    public function gestion_classe(ClasseRepository $repo_classe)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $classes = $repo_classe->findAll();
        return $this->render('responsable/classes.html.twig', [
            'classes' => $classes
        ]);
    } 

    /**
     * @Route("/responsable/classe/ajout", name="responsable_ajout_classe")
     * @Route("/responsable/classe/modification/{id}", name="responsable_modification_classe")
     */
    public function ajout_classe($id = null, ClasseRepository $repo_classe, Request $request, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        if($id == null){
            $classe = new Classe();
        }else{
            $classe = $repo_classe->find($id);
        }

        $form = $this->createForm(ClassType::class, $classe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($classe);
            
            $manager->flush();

            return $this->redirectToRoute('responsable_classe');
        }

        if($id == null){
            return $this->render('responsable/classe_ajout.html.twig', [
                'form_classe' => $form->createView(),
                'button' => 'Ajouter',
                'title' => 'L\'ajout d\'un classe'
            ]);
        }else{
            return $this->render('responsable/classe_ajout.html.twig', [
                'form_classe' => $form->createView(),
                'button' => 'Modifer',
                'title' => 'La modification d\'un classe'
            ]);
        }
    }
    
    /**
     * @Route("/responsable/classe/{id}/suppression", name="responsable_classe_suppression")
     */
    public function delete_classe($id = null, ClasseRepository $repo_classe, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $classe = $repo_classe->find($id);
       
        $manager->remove($classe);
        $manager->flush();

        return $this->redirectToRoute('responsable_classe');
    }

    /**
     * @Route("/responsable/elements", name="responsable_element")
     * @Route("/responsable/module/{id}/elements", name="responsable_module_elements")
     */
    public function gestion_element($id = null, ModuleRepository $repo_module, ElementRepository $repo_element)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        if($id == null){
            $elements = $repo_element->findAll();
            return $this->render('responsable/elements.html.twig', [
                'elements' => $elements
            ]);
        }else{
            $module = $repo_module->find($id);
            $elements = $module->getElements();
            return $this->render('responsable/elements.html.twig', [
                'elements' => $elements,
                'header' => $module->getNom()
            ]);
        }
    } 

    /**
     * @Route("/responsable/element/ajout", name="responsable_ajout_element")
     * @Route("/responsable/element/modification/{id}", name="responsable_modification_element")
     */
    public function ajout_element($id = null, ModuleRepository $repo_module, ClasseRepository $repo_classe, ProfesseurRepository $repo_professeur, ElementRepository $repo_element, Request $request, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $classes = $repo_classe->findAll();
        $modules = $repo_module->findAll();
        $professeurs = $repo_professeur->findAll();
        if($classes == null || $modules == null || $professeurs == null){
            return $this->render('responsable/accueil.html.twig', [
                'error' => 1
            ]);
        }

        if($id == null){
            $element = new Element();
        }else{
            $element = $repo_element->find($id);
        }

        $form = $this->createForm(ElementType::class, $element);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($element);
            
            $manager->flush();

            return $this->redirectToRoute('responsable_element');
        }

        if($id == null){
            return $this->render('responsable/element_ajout.html.twig', [
                'form_element' => $form->createView(),
                'button' => 'Ajouter',
                'title' => 'L\'ajout d\'un element'
            ]);
        }else{
            return $this->render('responsable/element_ajout.html.twig', [
                'form_element' => $form->createView(),
                'button' => 'Modifer',
                'title' => 'La modification d\'un element'
            ]);
        }
    }

    /**
     * @Route("/responsable/element/{id}/suppression", name="responsable_element_suppression")
     */
    public function delete_element($id = null, ElementRepository $repo_element, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $element = $repo_element->find($id);
        $id_module = $element->getModule()->getId();

        $manager->remove($element);
        $manager->flush();

        return $this->redirectToRoute('responsable_module_elements', ['id' => $id_module]);
    }

    /**
     * @Route("/responsable/seance/ajout", name="responsable_seance_ajout")
     * @Route("/responsable/seance/modification/{id}", name="responsable_seance_modification")
     */
    public function ajout_seance($id = null, ElementRepository $repo_element, ClasseRepository $repo_classe, ProfesseurRepository $repo_professeur, EmploiRepository $repo_emploi, Request $request, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $classes = $repo_classe->findAll();
        $elements = $repo_element->findAll();
        $professeurs = $repo_professeur->findAll();
        if($classes == null || $elements == null || $professeurs == null){
            return $this->render('responsable/accueil.html.twig', [
                'error' => 1
            ]);
        }

        if($id == null){
            $seance = new Emploi();
        }else{
            $seance = $repo_emploi->find($id);
        }

        $form = $this->createForm(EmploiType::class, $seance);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // 'element' => $seance->getElement(), 'professeur' => $seance->getProfesseur(), 
            if($seance->getId() == null){
                $seances_array = $repo_emploi->findOneBy(array('filiere' => $seance->getElement()->getModule()->getFiliere(), 'jour' => $seance->getJour()->getId(), 'heure_debut' => $seance->getHeureDebut(), 'heure_fin' => $seance->getHeureFin()));
                $seances_prof = $repo_emploi->findOneBy(array('professeur' => $seance->getProfesseur(), 'jour' => $seance->getJour()->getId(), 'heure_debut' => $seance->getHeureDebut(), 'heure_fin' => $seance->getHeureFin()));
                dump($seances_prof);
                dump($seances_array);
                if($seances_array !== null || $seances_prof !== null){
                    return $this->redirectToRoute('responsable_seance_ajout', [
                        'error' => 1
                    ]);
                }
            }

            $seance->setFiliere($seance->getElement()->getModule()->getFiliere())
                   ->setNiveau($seance->getElement()->getModule()->getNiveau());

            $manager->persist($seance);
            
            $manager->flush();

            return $this->redirectToRoute('responsable_seance', ['id' => $seance->getId()]);
        }

        if($id == null){
            return $this->render('responsable/seance_ajout.html.twig', [
                'form_seance' => $form->createView(),
                'button' => 'Ajouter',
                'title' => 'L\'ajout d\'une séance'
            ]);
        }else{
            return $this->render('responsable/seance_ajout.html.twig', [
                'form_seance' => $form->createView(),
                'button' => 'Modifer',
                'title' => 'La modification d\'une séance'
            ]);
        }
    }

    /**
     * @Route("responsable/seance/suppression/{id}", name="responsable_seance_suppression")
     */
    public function seance_supprimer( $id, EmploiRepository $repo_emploi, EntityManagerInterface $manager)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }else{
            if($this->getUser()->getResponsable() === null){
                return $this->redirectToRoute('home');
            }
        }
        $seance = $repo_emploi->find($id);
        $idFil = $seance->getElement()->getModule()->getNiveau()->getId(); 
        $idNiv = $seance->getElement()->getModule()->getFiliere()->getId();
        $manager->remove($seance);
        $manager->flush();

        return $this->redirectToRoute('responsable_seances', [
            'idFil' => $idFil,
            'idNiv' => $idNiv
        ]);
    }

    /**
     * @Route("/responsable/seance/{id}", name="responsable_seance")
     */
    public function affichage_seance($id, EmploiRepository $repo_emploi)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }
        $seance = $repo_emploi->find($id);
        return $this->render('responsable/seance.html.twig', [
            'seance' => $seance
        ]);
    }
    
    /**
     * @Route("/responsable/emploi/{idFil}/{idNiv}", name="responsable_seances")
     */
    public function affichage_emploi($idFil, $idNiv, JoursRepository $repo_jour, EmploiRepository $repo_emploi, FiliereRepository $repo_filiere, NiveauRepository $repo_niveau)
    {
        if($this->getUser() === null){
            return $this->redirectToRoute('home');
        }
        $filiere = $repo_filiere->find($idFil);
        $niveau = $repo_niveau->find($idNiv);
        $jours = $repo_jour->findAll();
        $seances = $repo_emploi->findBy(array('filiere' => $filiere->getId(), 'niveau' => $niveau->getId()));
        return $this->render('responsable/seances.html.twig', [
            'seances' => $seances,
            'jours' => $jours,
            'filiere' => $filiere,
            'niveau' => $niveau
        ]);
    }
}
