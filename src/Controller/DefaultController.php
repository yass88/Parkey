<?php


namespace App\Controller;


use App\Entity\Post;
use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\String_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class DefaultController extends AbstractController
{
    public function home(){
        #recupe 6 dernier articles
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy([], ['id' => 'DESC'], 6);
        # dd() = var_drump qui coupe le site (affichage)
        #dd($posts);
        # Transmettre à la vue les données
        return $this->render("default/home.html.twig", [
            'posts' => $posts
        ]);
    }

    /**
     * Formulaire pour rédiger une annonce
     * @IsGranted("ROLE_USER")
     * @Route("/nouvelle_annonce", name="post_new", methods={"GET|POST"})
     * @param Request $request
     *
     */
    public function newPost(Request $request, SluggerInterface $slugger)
    {
        $post = new Post();

        $post->setUser($this->getUser());

        $form = $this->createFormBuilder($post)

            ->add('title', String_::class, [
                'label' => true
            ])

            ->add('address', String_::class, [
                'label' => true
            ])

            ->add('price_hour', Float_::class, [
                'label' => true
            ])

            ->add('price_day', Float_::class, [
                'label' => true
            ])

            ->add('price_month', Float_::class, [
                'label' => true
            ])

            ->add('content', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => true
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Publier mon annonce'
            ])

            # Dispo / avability

            ->getForm();
        $parking = new parking();
        $parking->setParking($this->getParking());
        $form = $this->createFormBuilder($parking)

            ->add('largeur', Float_::class, [
                'label' => 'largeur'
            ])

            ->add('longueur', Float_::class, [
                'label' => 'longueur'
            ])

            ->add('hauteur', Float_::class, [
                'label' => 'hauteur'
            ])

            ->add('guard', Boolean::class, [
                'label' => 'guard'
            ])

            ->add('camera', Boolean::class, [
                'label' => 'camera'
            ])

            ->add('covered', Boolean::class, [
                'label' => 'covered'
            ])

            ->add('locked', Boolean::class, [
                'label' => 'locked'
            ])

            ->add('createdAt', \DateTime::class, [
                'label' => 'createdAt'
            ])

            ->add('updatedAt', \DateTime::class, [
                'label' => 'updatedAt'
            ])

            /*->add('categories', relation::class, [
                'label' => 'categories'
            ])
            */


            # Dispo / avability

            ->getForm();

        # Créa form
        $form->handleRequest($request);

        # Vérif données soumise si valide
        if($form->isSubmitted() && $form->isValid())
        {
            $post->setAlias(
                $slugger->slug(
                    $post->getTitle()
                )
            );

            # Upload image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            # Nom image
            $newFileName = $post->getAlias() .  '-' . uniqid() . '-' . $imageFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $imageFile->move(
                    $this->getParameter('posts_directory'),
                    $newFileName
                );
            } catch (FileException $e) {
                #TODO:: Handle catch exception
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $post->setImage($newFileName);

            # Sauvegarde des données
            $save = $this->getDoctrine()->getManager();
            $save->persist($post);
            $save->flush();

            # Notif Flash sur session
            $this->addFlash('notice',
                'Annonce crèe ! :)');

            # Redirection
            #TODO:: Redirection vers page de l'annonce
            return $this->redirectToRoute('default/home.html.twig');


        }

        # Transmission du formulaire à la vue
        return $this->render('post/new.html.twig', [
            'form' => $form->createView()
        ]);

    }

}
