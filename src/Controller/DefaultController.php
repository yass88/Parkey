<?php


namespace App\Controller;


use App\Entity\Post;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
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

            ->add('title', TextType::class, [
               'label' => true
            ])

            ->add('address', TextType::class, [
                'label' => true
            ])

            ->add('price_hour', NumberType::class, [
                'label' => true
            ])

            ->add('price_day', NumberType::class, [
                'label' => true
            ])

            ->add('price_month', NumberType::class, [
                'label' => true
            ])

            ->add('content', TextType::class, [
                'label' => true
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Publier mon annonce'
            ])

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