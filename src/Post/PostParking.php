<?php


namespace App\Post;


use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;

class PostParking
{
    // Post data
    public $title;
    public $alias;
    public $content;
    public $image;
    public $availability;
    public $address;
    public $price_hour;
    public $price_day;
    public $price_month;
    public $user;
    public $images;
    // Parking data
    public $largeur;
    public $longueur;
    public $hauteur;
    public $guard;
    public $camera;
    public $covered;
    public $locked;
    public $categorie;

    public $createdAt;
    public $updatedAt;

    /**
     * PostParking constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

}