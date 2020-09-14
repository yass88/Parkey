<?php

namespace App\Controller\Admin;


use App\Entity\Post;
use App\Entity\User;
use App\Form\PostForm;
use App\Form\RegistrationForm;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 * @IsGranted ("ROLE_ADMIN")
 * @package App\Controller\Admin
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/users", name="home_admin")
     */
    public function home(UserRepository $repository)
    {
        return $this->render('admin/post/user.html.twig', [
            'users' => $repository->findAll()
        ]);
    }

    /**
     * @IsGranted ("ROLE_ADMIN")
     * @Route("/post/ajout", name="post_ajout")
     * @param Request $request
     */

    public function ajoutPost(Request $request)
    {
        $post = new Post();

        $form = $this->createForm(Post::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('home_post');
        }

        return $this->render('admin/post/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     * @IsGranted ("ROLE_ADMIN")
     * @param Request $request
     * @param Post $post
     */
    public function modifPost(Post $post, Request $request)
    {
        $form = $this->createForm(PostForm::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('home_post');
        }

        return $this->render('admin/post/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Modification des données d'un utilisateur
     *@IsGranted ("ROLE_ADMIN")
     * @Route("/user/edit/{id}", name="profil_edit_user", methods={"GET|POST"})
     * @param Request $request
     * @return Response
     */
    public function edit(User $user, Request $request)
    {
        # Récupération du formulaire
        $form = $this->createForm(RegistrationForm::class, $user)
            ->handleRequest($request);

        # Traitement du Formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }
        # Affichage dans la vue
        return $this->render("admin/post/user-edit.html.twig", [
            'form' => $form->createView()
        ]);

    }

    /**
     * @IsGranted ("ROLE_ADMIN")
     * @Route("/supprimer/{id}", name="supprimer_user")
     * @param User $user
     */
    public function supprimer(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('message', 'Utilisateur supprimée avec succès');
        return $this->redirectToRoute('admin_home_admin');
    }


}