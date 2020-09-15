<?php


namespace App\Form;



use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostForm extends AbstractType
{
    /**
     * Création du Formulaire
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
          

            ->add('availability', DateType::class, ['label' => 'Disponibilité', 'years' => range(date('Y'),date('Y')+2) ])

            ->add('price_hour', NumberType::class, [
                'label' => 'Location à l\'heure'
            ])
            ->add('price_day', NumberType::class, [
                'label' => 'Location au jour'
            ])
            ->add('price_month', NumberType::class, [
                'label' => 'Location au mois'
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * Configuration des options du formulaire
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class
        ]);
    }

    /**
     * Ajouter un préfixe au balise HTML
     * @return string
     */
    public function getBlockPrefix()
    {
        return "product";
    }

}