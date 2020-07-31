<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Notes;
use App\Form\UserType;
use App\Entity\Element;
use App\Entity\Filiere;

use App\Form\NotesType;
use App\Entity\Etudiant;
use App\Entity\Professeur;

use App\Form\EtudiantType;
use App\Repository\UserRepository;
use App\Repository\NotesRepository;
use App\Repository\ModuleRepository;
use App\Repository\ElementRepository;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PerofesseurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProfesseurController extends AbstractController
{
    
   
    /**
     * @Route("/professeur/{id}", name="prof_accueil")
     */
    public function home(Professeur $professeur)
    {
        return $this->render('professeur/accueil.html.twig', [
            'professeur' => $professeur ]);
    }

    /**
     * @Route("/profil/{id}" ,name="professeur_profil")
     */
    public function profil(Professeur $professeur)
    {
        return
         $this->render('professeur/profil.html.twig', [
            'professeur' => $professeur ]);
    }

    /**
     * @Route("/elements/{id}", name="prof_elements")
     */
    public function elements(Professeur $professeur, ElementRepository $element)
    {
        $element = $this->getDoctrine()
            ->getRepository(Element::Class)
                ->findBy(array('professeur' => $professeur->getId()));


        return $this->render('professeur/elements.html.twig', [
            'element' => $element ,
            'professeur' => $professeur
        ]);
    }

    /**
     * @Route("/elements/{idEl}/etudiants/{id}", name="prof_elements_etudiants")
     */
    public function etudiants(Professeur $professeur, $idEl, ElementRepository $repo_element, EtudiantRepository $repo_etudiant, ModuleRepository $module,NotesRepository $repo_note)
    {
        $element= $repo_element->find($idEl);
        $module =  $element->getModule();
        $filiere = $module->getFiliere();
        $niveau = $module->getNiveau();
        $etudiants = $repo_etudiant->findBy(array('filiere' => $filiere->getId(),'niveau' => $niveau->getId()));
        
        $notes = $repo_note->findBy(array('element' => $element->getId() ,'professeur' =>$professeur->getId()) );


        return $this->render('professeur/etudiants.html.twig', [
            'element' => $element ,
            'etudiant' =>$etudiants,
            'professeur' => $professeur,
            'notes' => $notes,
        ]);
    }
   
    /**
     * @Route("/elements/{idEl}/{idEt}/{id}", name="prof_elements_notes")
     */
    public function notes(EntityManagerInterface $manager,Request $request,Professeur $professeur,EtudiantRepository $repo_etudiant,ElementRepository $repo_element,NotesRepository $repo_note, $idEl, $idEt)
    {
        $etudiant= $repo_etudiant->find($idEt);
        $element= $repo_element->find($idEl);
        
        $note = new Notes();
        $form = $this->createForm(NotesType::class, $note);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if ( $note->getId()=== null){
                $note_array = $repo_note->findBy(array('element' => $element->getId(), 'etudiant' => $etudiant->getId()));
                foreach ($note_array as $n) {
                    $manager->remove($n);
                }
    
            }
            $note->setNote($note->getNote());
            $note->setElement($element);
            $note->setEtudiant($etudiant);
            $note->setProfesseur($professeur);

            $manager->persist($note);
            
            $manager->flush();

            return $this->redirectToRoute('prof_elements_etudiants', [
               
                'idEl' => $idEl,
                'element' => $element,
                'id' => $professeur->getId(),
                'message' => 'Note ajoutée',
                 ]);
        }

       
            return $this->render('professeur/notes.html.twig', [
                'formNotes' => $form->createView(),

                'idEt' => $idEt,
                'idEl' => $idEl, 'element' => $element,
                'etudiant' => $etudiant,
                'professeur' => $professeur,
            ]);
        
    }
}
