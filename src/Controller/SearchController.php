<?php


namespace App\Controller;




use App\Entity\Category;
use App\Entity\Parking;
use App\Entity\Post;
use App\Form\PostForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchController extends AbstractController
{
    /**
     * Fonction recherche pour page recherches avancer
     * @Route("/recherche", name="search_parking", methods={"GET|POST"})
     * @param Request $request
     */

    public function researchs(Request $request)
    {

        $results = [];
        # Formulaire de recherche avancer
        $form = $this->createFormBuilder()
            ->setMethod('GET')
            # PostRepository
            ->add('address', TextType::class, [
                'label' => 'Adresse de de la location'
            ])
            ->add('availability', DateType::class, [
                'label' => 'Date d\'arriver',
                'years' => range(date('Y'),date('Y')+5)
            ])
            ->add('availability_end', DateType::class, [
                'label' => 'Date de départ',
                'years' => range(date('Y'),date('Y')+5)

            ])
//            ->add('title')
//
//            # ParkingRepository
//            ->add('title', EntityType::class, [
//                'class' => Category::class,
//                'choice_label' => 'title',
//                'label' => 'Type de véhicule'
//
//            ])
//            ->add('largeur', TextType::class, [
//                'required' => false
//            ])
//            ->add('longueur', TextType::class, [
//                'required' => false
//            ])
//            ->add('hauteur', TextType::class, [
//                'required' => false
//            ])
//            ->add('guard', CheckboxType::class, [
//                'label' => 'guard',
//                'required' => false
//            ])
//            ->add('camera', CheckboxType::class, [
//                'label' => 'camera',
//                'required' => false
//            ])
//            ->add('covered', CheckboxType::class, [
//                'label' => 'covered',
//                'required' => false
//            ])
//            ->add('locked' , CheckboxType::class, [
//                'label' => 'locked',
//                'required' => false
//            ])


            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher'
            ])
            ->getForm();


        $form->handleRequest($request);

        # Soumition de la recherche
        if ($form->isSubmitted()) {

            # récupération des données
            $search = $form->getData();
            # post
            $address = $search['address'];

            $availability = $search['availability'];
            $availability_end = $search['availability_end'];
            # parking
//            $title = $search['title'];
//            $largeur = $search['largeur'];
//            $longueur = $search['longueur'];
//            $hauteur = $search['hauteur'];
//            $guard = $search['guard'];
//            $camera = $search['camera'];
//            $covered = $search['covered'];
//            $locked = $search['locked'];

            # Appel de PostRepository
            $postEntity = $this->getDoctrine()->getRepository(Post::class);
            $results = $postEntity->research($address, $availability, $availability_end);


            return $this->render("default/recherche.html.twig", [
                'form' => $form->createView(),
                'results' => $results,
            ]);



        }
        #dd($posts);
        # Transmettre à la vue les données
        return $this->render("default/recherche.html.twig", [
            'form' => $form->createView(),
            'results' => $results,
        ]);
    }


}
