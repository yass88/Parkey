<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationForm extends AbstractType
{
    /**
     * Création du Formulaire
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
        ;

        /*
         * Permet de supprimer le champ email et password a la modification
         * https://symfony.com/doc/current/form/dynamic_form_modification.html
         */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $user = $event->getData();
            $form = $event->getForm();

            // checks if the User object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if ($user && null !== $user->getId()) {
                $form->remove('email');
                $form->remove('password');
            }

        });
    }

    /**
     * Configuration des options du formulaire
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

    /**
     * Ajouter un préfixe au balise HTML
     * @return string
     */
    public function getBlockPrefix()
    {
        return "user";
    }

}