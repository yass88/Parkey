<?php


namespace App\Controller;


use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    /**
     *  Page inscription
     * @Route("/inscription", name="user_register", methods={"GET|POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        # Création d'un nouveau Utilisateur
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        #$user->setFirstname('yassine');
        #$user->setLastname('mazouz');

        # Création d'un nouveau formulaire
        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('nickname', TextType::class, ['label' => 'Pseudo'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('birthday', BirthdayType::class, [
                'placeholder' => 'Select a value',
            ])
            ->add('phone', TextType::class, ['label' => 'Votre numéro de télephone'])
            ->add('address', TextType::class, ['label' => 'Votre adresse'])
            ->add('avatar', TextType::class, ['label' => 'Votre avatar'])
            ->add('message', TextType::class, ['label' => 'Votre message'])
            ->add('submit', SubmitType::class, ['label' => "Je m'inscrit !", 'attr' => ['class' => 'btn-block btn-dark']])
            ->getForm();

        # Traitement de la Request.permet a SF de récuperer les données soumise
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            # TODO Encoder le mot de passe
            $user->setPassword(
                $encoder->encodePassword($user, $user->getPassword())
            );

            # TODO Enregistrer dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            # TODO Notification Flash
            $this->addFlash('notice', 'Félicitaion Vous pouvez vous connecté');
            # TODO Redirection
            return $this->redirectToRoute('home');

        }
        #Transmission d'un formulaire à la vue
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/profil", name="user_profil", methods={"GET|POST"} )
     */

    public function profil()
    {
        $user = $this->getUser();
        return $this->render('user/profil.html.twig');
    }


}