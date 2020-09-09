<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Category;
use App\Entity\Images;
use App\Entity\Parking;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        #User
        $user = new User();
        $user->setFirstname('Vic')
            ->setLastname('Chaour')
            ->setEmail('ch@gmail.com')
            ->setPassword('test')
            ->setRoles(['ROLE_USER'])
            ->setCreatedAt(new \DateTime())
            ->setBirthday(new \DateTime())
            ->setAvatar('test.png')
            ->setPhone('+33 00 00 00 00')
            ->setNickname('Noe')
            ->setMessage('<p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci alias consectetur culpa delectus ea eligendi fuga iste maiores neque nesciunt quia quidem reiciendis reprehenderit repudiandae saepe, sequi veritatis. Inventore, modi! </p>')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setAddress('adresse adresse')
        ;

        $manager->persist($user);

        # Category
        $category = new Category();
        $category ->setTitle('Title')
                  ->setAlias('Alias');

        # Post
        for ($i = 0; $i < 6; $i++){
            $post = new Post();
            $post->setImage('Image' . $i)
                ->setTitle('Title' . $i)
                ->setAddress('Address' . $i)
                ->setAlias('Alias'. $i)
                ->setPrice(rand(20,500))
                ->setAvailability(new \DateTime())
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime())
                ->setUser($user)
                ->setContent('<p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci deserunt fugit magni natus pariatur praesentium temporibus? Aut, dolor eos ipsum maxime natus odio quaerat quia quis quo, reiciendis repellendus soluta. </p>');

            #Parking
            $parking = new Parking();
            $parking ->setLargeur(2.5)
                ->setHauteur(3.5)
                ->setLongueur(78)
                ->setCovered('oui')
                ->setCamera('oui')
                ->setLocked('oui')
                ->setGuard('oui')
                ->setPost($post)
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

            $image = new Images();
            $image ->settitle('Title'.$i)
                ->setcreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

            $booking = new Booking();
            $booking->setStartDate(new \DateTime())
                    ->setEndDate(new \DateTime())
                    ->setPrice(15)
                    ->setCreatedAt(new \DateTime())
                    ->setAvailability(new \DateTime())
                    ->setAddress('Address'.$i);



            $manager->persist($parking);
            $manager->persist($post);
            $manager->persist($image);
            $manager->persist($booking);
            $manager->persist($category);

        }

        $manager->flush();
    }

    
}