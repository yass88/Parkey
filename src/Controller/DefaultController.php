<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Parking;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostForm;
use App\Post\PostParking;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="search_parking", methods={"GET|POST"})
     * @param Request $request
     */

    public function home(Request $request)
    {

        #recupe 6 dernier articles
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy([], ['id' => 'DESC'], 6);

        # Formulaire de recheche
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('search_parking'))
            ->setMethod('GET')
            ->add('address', TextType::class, [
                'label' => 'Adresse rechercher'
            ])
            ->add('availability', DateType::class, [
                'label' => 'Date d\'arriver',
                'years' => range(date('Y'),date('Y')+5)
            ])
            ->add('availability_end', DateType::class, [
                'label' => 'Date de départ',
                'years' => range(date('Y'),date('Y')+5),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher'
            ])
            ->getForm();


        $form->handleRequest($request);
        # si le formulaire est envoyer
        if ($form->isSubmitted()) {
            # récupération de données
            $search = $form->getData();
            $address = $search['address'];
            $availability = $search['availability'];
            $availability_end = $search['availability_end'];

            $search = $this->getDoctrine()->getRepository(Post::class);
            $result = $search->research($address, $availability, $availability_end);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            # Récupération du formulaire
            $form = $this->createForm(Post::class, $posts)
                ->handleRequest($request);

            return $this->render("default/recherche.html.twig", [
               'result' => $result,
                'posts' => $posts
            ]);

        }

        #dd($posts);
        # Transmettre à la vue les données
        return $this->render("default/home.html.twig", [
            'posts' => $posts,
            'form' => $form->createView()
        ]);
    }


    /**
     * Formulaire pour rédiger une annonce
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newPost(Request $request, SluggerInterface $slugger)
    {
        $postParking = new PostParking();

        $form = $this->createFormBuilder($postParking)
            // Post

            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('address', TextType::class, [
                'label' => 'adresse'
            ])
            ->add('price_hour', NumberType::class, [
                'label' => 'Location à l\'heure'
            ])
            ->add('price_day', NumberType::class, [
                'label' => 'Location au jour'
            ])
            ->add('price_month', NumberType::class, [
                'label' => 'Location au mois'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description de votre annonce'
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'dropify'
                ]
            ])
            ->add('availability', DateType::class, [
                'label' => 'Disponiilité',
                'years' => range(date('Y'),date('Y')+2)
            ])

            ->add('availability_end', DateType::class, [
                'label' => 'Disponiilité fin',
                'required' => false,
                'years' => range(date('Y'),date('Y')+2)
            ])


            //Parking
            ->add('categorie', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'placeholder' => 'Type de véhicule'
            ])

            ->add('largeur', NumberType::class, [
                'label' => 'largeur'
            ])

            ->add('longueur', NumberType::class, [
                'label' => 'longueur'
            ])
            ->add('hauteur', NumberType::class, [
                'label' => 'hauteur'
            ])
            ->add('guard', CheckboxType::class, [
                'label' => 'guard',

            ])
            ->add('camera', CheckboxType::class, [
                'label' => 'camera'
            ])
            ->add('covered', CheckboxType::class, [
                'label' => 'covered'
            ])
            ->add('locked', CheckboxType::class, [
                'label' => 'locked'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier mon annonce'
            ])
            ->getForm();

        # Créa form
        $form->handleRequest($request);

        # Vérif données soumise si valide
        if ($form->isSubmitted() && $form->isValid()) {

            #dd($postParking); # TODO a supprimer pour continuer
            $parking = new Parking();
            $parking->setCategory($postParking->categorie);
            $parking->setlargeur($postParking->largeur);
            $parking->setLongueur($postParking->longueur);
            $parking->setHauteur($postParking->hauteur);
            $parking->setGuard($postParking->guard);
            $parking->setCamera($postParking->camera);
            $parking->setCovered($postParking->covered);
            $parking->setLocked($postParking->locked);
            $parking->setCreatedAt($postParking->createdAt);
            $parking->setUpdatedAt($postParking->updatedAt);


            $post = new Post();
            $post->setTitle($postParking->title);
            $post->setAlias($slugger->slug($postParking->title));
            $post->setParkings($parking);
            $post->setContent($postParking->content);
            $post->setImage($postParking->image);
            $post->setAvailability($postParking->availability);
            $post->setAvailabilityEnd($postParking->availability_end);
            $post->setAddress($postParking->address);
            $post->setPriceDay($postParking->price_day);
            $post->setPriceHour($postParking->price_hour);
            $post->setPriceMonth($postParking->price_month);
            $post->setCreatedAt($postParking->createdAt);
            $post->setUpdatedAt($postParking->updatedAt);
            $post->setUser($this->getUser());

            # Upload image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            # Nom image
            $newFileName = $post->getAlias() . '-' . uniqid() . '-' . $imageFile->guessExtension();

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

            $em = $this->getDoctrine()->getManager();
            $em->persist($parking);
            $em->persist($post);
            $em->flush();

            # Notif Flash sur session
            $this->addFlash('notice',
                'Annonce crèe ! :)');

            # Redirection
            #TODO:: Redirection vers page de l'annonce
            return $this->redirectToRoute('home');

        }

        # Transmission du formulaire à la vue
        return $this->render('post/new.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Affiche les articles d'une catégorie
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/annonce/{alias}",
     *     name="default_annonce",
     *     methods={"GET"})
     * @param $alias
     * @param Post $post
     * @return Response
     */
    public function annonce(Post $post, $alias)
    {

        $repo = $this->getDoctrine()->getRepository(Post::class);
        $postrepo = $repo->searchByAddress($post->getAddress());


        # Transmettre à la vue les données
        return $this->render('default/annonce.html.twig', [
            'alias' => $alias,
            'post' => $post,
            'postrepo' => $postrepo
        ]);
    }
    /**
    * Modification des données d'un utilisateur
     * @IsGranted("ROLE_USER", message="Vous n'avez pas les permissions nécessaires.")
     * @Route("/post/edit/{id}", name="user_post_edit", methods={"GET|POST"})
     * @param Request $request
     * @param Post $post
     * @param $id
     * @return Response
     */
    public function editPost(Request $request, Post $post, $id)
    {

        # Récupération du formulaire
        $form = $this->createForm(PostForm::class, $post)
            ->handleRequest($request);

        # Traitement du Formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        # Affichage dans la vue
        return $this->render("user/post-edit.html.twig", [
            'form' => $form->createView()
        ]);
    }







}



