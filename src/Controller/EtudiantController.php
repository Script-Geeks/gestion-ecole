<?php

namespace App\Controller;
use App\Entity\User;

use App\Entity\Certificats;
use App\Form\CertificatsType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\UserType;
use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Entity\Filiere;
use App\Repository\UserRepository;
use App\Controller\SecurityController;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


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
    public function change_user_password(EtudiantRepository $repo_etudiant,EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $encoder, Etudiant $etudiant ) {

        if($etudiant ->getId()){
            
                 $etudiant = $repo_etudiant->find($etudiant ->getId());
                

                $form = $this->createForm(UserType::class, $user);

                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()){
                    if($user){
                    $hash = $encoder->encodePassword($user, $user->getPassword());
                    $user->setUsername($etudiant->getNom())
                        ->setEmail ($user->getEmail())
                         ->setPassword($hash)
                         ->setEtudiant($etudiant);
                    
                    $manager->persist($user);
                    
                    $manager->flush();

                    return $this->render('etudiant/accueil.html.twig', [
                        'etudiant' => $etudiant,
                        'message' => 'Mot de passe changé avec succés' ]);
                     } return new Response(
                    '<html><body>Lucky mot de passe incorrect, veuillez reéssayer </body></html>'  );
                 
    
                 }
    return $this->render('etudiant/updatepassword.html.twig', [
        'form_authentication' => $form->createView(),
        'etudiant' => $etudiant,

    ]);
    
    }}
       
    /**
     * @Route("/etudiant/{id}", name="etudiant_accueil")
     */
    public function home(Etudiant $etudiant)
    {
        return $this->render('etudiant/accueil.html.twig', [
            'etudiant' => $etudiant ]);
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
    public function demandecertificat(Request $request, EntityManagerInterface $manager, Etudiant $etudiant)
    {
        $demande = new Certificats();
        
         $form = $this->createForm(CertificatsType::class, $demande);
         $form->handleRequest($request);
         dump($demande);
        
         if( $form->isSubmitted() && $form->isValid()){

             $demande->setRequestedAt(new \DateTime());
             $demande->setEtudiant($etudiant);
 
             $manager->persist($demande);
             $manager->flush();
 
             return $this->redirectToRoute('etudiant_accueil', [
                'id'=> $etudiant->getId(),
                'demande' => $demande,

                ]);
         }
 
         return $this->render('etudiant/demande.html.twig' , [
             'formDemande' => $form->createView(),
             'etudiant' => $etudiant,         
             'demande' => $demande,

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
    
}
