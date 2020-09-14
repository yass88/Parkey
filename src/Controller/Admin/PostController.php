<?php


namespace App\Controller\Admin;


use App\Entity\Post;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/post")
 * @IsGranted ("ROLE_ADMIN")
 * @package App\Controller\Admin
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="home_post")
     * @IsGranted ("ROLE_ADMIN")
     * @param PostRepository $postRepo
     */
    public function home(PostRepository $postRepo)
    {
        return $this->render('admin/post/home.html.twig', [
            'posts' => $postRepo->findAll()
        ]);
    }

    /**
     * @Route("/activer/{id}", name="activer")
     * @IsGranted ("ROLE_ADMIN")
     * @param Post $post
     */
    public function activer(Post $post)
    {
        $post->setActive(($post->getActive())?false:true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return new Response("true");
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     * @IsGranted ("ROLE_ADMIN")
     * @param Post $post
     */
    public function supprimer(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        $this->addFlash('message', 'Annonce supprimée avec succès');
        return $this->redirectToRoute('home_post');
    }

}