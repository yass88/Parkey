<?php


namespace App\Controller;


use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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

}