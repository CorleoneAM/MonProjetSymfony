<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Book;
use Faker\Factory;
class AppFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
       
        $namescate=['Programmation','Roman','Histoire','Chimie','Art','Langue'];
        $photos=['python-5eac4abd98b4c.jpeg','java-5eac4affb943e.jpeg','81j3tUFj2jL-60be8b21550a5.jpeg','cvt_Le-grand-livre-de-lArt_9619-5eb3f9b6e9fe8.jpeg','Magic art-5eb5511ed2109.jpeg','peinture-5eb3f9d288101.jpeg'];
        $factor=Factory::create();
        for($i=0;$i<6;$i++)
        {
            $categ=new Category();
            $categ->setName($namescate[$i]);
            $photo = ($photos[random_int(0,5)]);
            $manager->persist($categ);
              
                for($j=0;$j<10;$j++)
                {
                    $book=new Book();
                    $book->setTitle("Titre".$j);
                    $book->setPrice(100*$j);
                    $book->setAuthor($factor->address);
                    $book->setCategory($categ);
                    $book->setImage($photo);
                    $manager->persist($book);
                }

        }
        $manager->flush();
    }
}