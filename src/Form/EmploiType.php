<?php

namespace App\Form;

use App\Entity\Jours;
use App\Entity\Classe;
use App\Entity\Emploi;
use App\Entity\Element;
use App\Entity\Professeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class EmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('heure_debut', NumberType::class)
            ->add('heure_fin', NumberType::class)
            ->add('jour', EntityType::class, [
                'class' => Jours::class,
                'choice_label' => 'nom'
            ])
            ->add('element', EntityType::class, [
                'class' => Element::class,
                'choice_label' => 'nom'
            ])
            ->add('professeur', EntityType::class, [
                'class' => Professeur::class,
                'choice_label' => 'nom'
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'nom'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Emploi::class,
        ]);
    }
}
