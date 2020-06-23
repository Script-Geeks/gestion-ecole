<?php

namespace App\Form;

use App\Entity\Cycle;
use App\Entity\Niveau;
use App\Form\UserType;
use App\Entity\Filiere;
use App\Entity\Etudiant;
use App\Repository\CycleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('dateNaissAt', DateType::class)
            ->add('cne', TextType::class)
            ->add('cin', TextType::class)
            ->add('nomComplet_pere', TextType::class)
            ->add('tel_pere', TextType::class)
            ->add('cycle', EntityType::class, [
                'class' => Cycle::class,
                'choice_label' => 'nom'
            ])
            ->add('filiere', EntityType::class, [
                'class' => Filiere::class,
                'choice_label' => 'nom'
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
