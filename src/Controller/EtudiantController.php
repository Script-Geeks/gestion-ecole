<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Filiere;
use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Entity\Certificats;
use App\Form\CertificatsType;
use App\Repository\UserRepository;
use App\Repository\JoursRepository;
use App\Repository\NotesRepository;
use App\Repository\EmploiRepository;
use Doctrine\DBAL\Driver\Connection;
use App\Controller\SecurityController;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CertificatsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EtudiantController extends AbstractController
{

    /**
     * @Route("/deleted", name="out")
     */
    public function out()
    {
        return $this->render('etudiant/deleted.html.twig');
    }

     /**
     * @Route("/compte/{id}", name="etudiant_compte")
     */
    public function compte(Etudiant $etudiant)
    {
        return $this->render('etudiant/compte.html.twig', [
            'etudiant' => $etudiant,
             ]);
    }

    /**
    * @Route("/updatepassword/{id}", name="etudiant_updatepwd"))
    */
    public function change_user_password( $id, EtudiantRepository $repo_etudiant,EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $encoder, UserRepository $repo_user, \Swift_Mailer $mailer)
    {
        $etudiant = $repo_etudiant->find($id);

        dump($etudiant);
        if($_POST !== null && count($_POST) === 1){
            $pwd = $_POST['password'];
            $user = $repo_user->findOneBy(array('etudiant' => $etudiant->getId()));
            $user->setPassword($pwd);
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $body = strtoupper($etudiant->getNom())." ".$etudiant->getPrenom().",\nVous pouvez vous authentifier en utilisant les informations ci-dessous.\n
                    E-mail: ".$etudiant->getEmail()."\n
                    Mot de passe: ".$pwd;
            $message = (new \Swift_Message('Changement du mot de passe'))
                    ->setFrom('insea.inscription@gmail.com')
                    ->setTo($etudiant->getEmail())
                    ->setBody(
                        $body,
                        'text/plain'
                    );
            $mailer->send($message);

            return $this->render('etudiant/accueil.html.twig', [
                'etudiant' => $etudiant,
                'message' => 'Mot de passe changé avec succés' ]);
        }
        return $this->render('etudiant/updatepassword.html.twig', [
            'etudiant' => $etudiant
        ]);
    }

    /**
     * @Route("/etudiant/{id}", name="etudiant_accueil")
     */
    public function home(Etudiant $etudiant)
    {
        return $this->render('etudiant/accueil.html.twig', [
            'etudiant' => $etudiant ]);
    }

    /**
     * @Route("/demande/document/{type}/{id}", name="etudiant_document")
     */
    public function doc(Etudiant $etudiant, $type, CertificatsRepository $repo_certificat, NotesRepository $repo_note)
    {
        $certificat = $repo_certificat->findOneBy(array('etudiant' => $etudiant->getId(), 'type' => $type));
        $note = $repo_note->findBy(array('etudiant' => $etudiant->getId()));

        if ($certificat->getType()=='Certificat de scolarité'){
        return $this->render('etudiant/doc.html.twig', [
            'etudiant' => $etudiant ,
            'demandes' => $certificat,
            'type' => $type
            ]);
        }
        else{
            return $this->render('etudiant/doc_releve.html.twig', [
                'notes' => $note ,
                'demandes' => $certificat,
                'etudiant' => $etudiant ,
            ]);
        }
    }
    
    /**
     * @Route("/demande/documents/{id}", name="etudiant_documents")
     */
    public function documents(Etudiant $etudiant, CertificatsRepository $certificat)
    {
        $certificat = $this->getDoctrine()
            ->getRepository(Certificats::Class)
                ->findBy(array('etudiant' => $etudiant->getId()));


        return $this->render('etudiant/documents.html.twig', [
            'etudiant' => $etudiant ,
            'demandes' => $certificat
        ]);
    }
    
    /**
     * @Route("/demande/document/{type}/impression/{id}", name="etudiant_impression")
     */
    public function impression(Etudiant $etudiant, $type, CertificatsRepository $repo_certificat, NotesRepository $repo_note)
    {
        $note = $repo_note->findBy(array('etudiant' => $etudiant->getId()));

        $certificat = $repo_certificat->findOneBy(array('type' => $type, 'etudiant' => $etudiant->getId()));

        dump($certificat);
        $options = new Options();
        $options->setIsRemoteEnabled(true);

        $options->set('defaultFont', 'Courier');
        $dompdf = new Dompdf($options);


        $dompdf->set_option('isHtml5ParserEnabled', true);
        if($this->getUser()->getResponsable() !== null){
            $certificat->setIssuedAt(new \DateTime());
        }
        $certificat->getIssuedAt();
        if ($certificat->getType()=='Certificat de scolarité'){

        $doc = $this->renderview('etudiant/impression.html.twig', [
            'etudiant' => $etudiant ,
            'demande' => $certificat,
            'issuedAt' => $certificat->getIssuedAt()
            ]);
        }
        else{
            $doc = $this->renderview('etudiant/impression_releve.html.twig', [
                'notes' => $note ,
                'etudiant' => $etudiant ,
                'demande' => $certificat,
                'issuedAt' => $certificat->getIssuedAt()]);
        }   
        $dompdf->loadHtml($doc);

        $dompdf->render();

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');


        // Render the HTML as PDF
        $dompdf->loadHtml( 'doc');

        $dompdf->stream($type.'_'.$etudiant->getNom().'_'.$etudiant->getPrenom());
    }

    /**
     * @Route("/profil/{id}" ,name="etudiant_profil")
     */
    public function profil(Etudiant $etudiant)
    {
        return $this->render('etudiant/profil.html.twig', [
            'etudiant' => $etudiant , 'age' => date_diff(date_create($etudiant->getDateNaissAt()->format('d-m-Y')), date_create('today'))->y]);
    }

    /**
     * @Route("/profil/update/{id}" ,name="etudiant_update")
     */
    public function update(Request $request, EntityManagerInterface $manager, Etudiant $etudiant)
    {
        if($etudiant ->getId()){

            $form = $this->createForm(EtudiantType::class, $etudiant);
            $form->handleRequest($request);
            dump($etudiant);
            $imageFile = $form->get('image')->getData();

            if( $form->isSubmitted() && $form->isValid() ){
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = md5($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $imageFile->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'imageFilename' property to store the PDF file name
                    // instead of its contents
                    $etudiant->setImageFilename($newFilename);
                }

                $manager->persist($etudiant);

                $manager->flush();

                return $this->redirectToRoute('etudiant_profil', 
                ['id'=> $etudiant->getId() , ] 
                );

            }

            return $this->render('etudiant/modification.html.twig', [
                'form_etudiant' => $form->createView() , 'etudiant' => $etudiant
            ]);
        }
    }
    
    /**
     * @Route("/demande/{id}", name="etudiant_certificat")
     */
    public function demandecertificat(Request $request, EntityManagerInterface $manager, Etudiant $etudiant, CertificatsRepository $certificat)
    {
        $demande = new Certificats();

        $form = $this->createForm(CertificatsType::class, $demande);
        $form->handleRequest($request);
        dump($demande);

        if( $form->isSubmitted() && $form->isValid()){
    
            $certificat_array = $certificat->findBy(array('etudiant' => $etudiant->getId(), 'type' => $demande->getType()));
            foreach ($certificat_array as $c) {
                $manager->remove($c);
            }

            $demande->setRequestedAt(new \DateTime())
                    ->setEtudiant($etudiant)
                    ->setAccepted(0);

            $manager->persist($demande);
            $manager->flush();

            return $this->redirectToRoute('etudiant_accueil', [
                'id'=> $etudiant->getId(),
                'msg' =>   "Votre demande est envoyé à l\'admin. Une réponse vous sera envoyée dans les meilleurs delais"
            ]);
        }

        return $this->render('etudiant/demande.html.twig' , [
            'formDemande' => $form->createView(),
            'etudiant' => $etudiant
        ]);
    }

     /**
     * @Route("/delete/{id}", name="etudiant_delete")
     */
    public function supprimer(EntityManagerInterface $manager, Etudiant $etudiant)
    {
        $manager->remove($etudiant);
        $manager->flush();

    return $this->render('etudiant/deleted.html.twig');

    }
     
    /**
     * @Route("/etudiant/emploi/{id}", name="etudiant_seances")
     */
    public function etudiant_emploi($id, JoursRepository $repo_jour, EmploiRepository $repo_emploi, EtudiantRepository $repo_etudiant)
    {
        $etudiant = $repo_etudiant->find($id);
        $jours = $repo_jour->findAll();
        $seances = $repo_emploi->findBy(array('filiere' => $etudiant->getFiliere(), 'niveau' => $etudiant->getNiveau()));
        return $this->render('responsable/seances.html.twig', [
            'seances' => $seances,
            'jours' => $jours,
            'filiere' => $etudiant->getFiliere(),
            'niveau' => $etudiant->getNiveau()
        ]);
    }
}
