<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Categoria;

class CategoriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
         //   ->add('tasca', EntityType::class, array('class' => Tasca::class,
         //   'choice_label' => 'nom'))
           ->add('nom', TextType::class)
         //   ->add('duracio', IntegerType::class)
            //->add('save', SubmitType::class, array('label' => 'Crear Subtasca'))
            ->add('save', SubmitType::class, array('label' => $options['submit']))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'submit' => 'Enviar',
        ]);
    }
}
