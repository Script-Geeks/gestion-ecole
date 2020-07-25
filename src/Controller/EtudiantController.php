<?php

namespace App\Controller;
use App\Entity\User;
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
use Symfony\Component\Routing\Annotation\Route;

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
            'etudiant' => $etudiant ,   'age' => date_diff(date_create($etudiant->getDateNaissAt()->format('d-m-Y')), date_create('today'))->y]);
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
     * @Route("/delete/{id}", name="etudiant_delete")
     */
    public function supprimer(EntityManagerInterface $manager, User $user, Etudiant $etudiant)
    {
      $usrRepo = $manager->getRepository(User::class);

      $duser = $usrRepo->find($user->getEtudiant()->getId());
      $manager->remove($duser);
      $manager->flush();

      return $this->render('etudiant/deleted.html.twig');

    }
    
}
