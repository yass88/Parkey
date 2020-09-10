<?php


namespace App\Controller;


use App\Entity\User;

use App\Form\RegistrationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            ->add('avatar', FileType::class, ['label' => 'Votre avatar',
                'attr' => [
                'class' => 'dropify'
            ]])
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



            #Uploader image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('avatar')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded

            $imageFile = $form->get('avatar')->getData();
            $newFilename = $user->getNickname().'-'.uniqid().'.'.$imageFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $imageFile->move(
                    $this->getParameter('users_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                #TODO Handle catch exception
            }
            $user->setAvatar($newFilename);

            # TODO Enregistrer dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            # TODO Notification Flash
            $this->addFlash('notice', 'Félicitaion Vous pouvez vous connecté');
            # TODO Redirection
            return $this->redirectToRoute('user_profil');

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
        return $this->render('user/profil.html.twig', [
            'user' => $user
        ]);
    }
    /**
     * Modification des données d'un utilisateur
     * @Route("/profil/edit", name="user_profil_edit", methods={"GET|POST"})
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        # Récupération des données du user connecté
        $user = $this->getUser();

        # Récupération du formulaire
        $form = $this->createForm(RegistrationForm::class, $user)
            ->handleRequest($request);

        # Traitement du Formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        # Affichage dans la vue
        return $this->render("user/profil-edit.html.twig", [
            'form' => $form->createView()
        ]);
    }




}