<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder=$passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
       for($i=1;$i<5;$i++)
        {
         $user = new User();
         $user->setEmail('user'.$i.'@gmail.com')
              ->setFirstName('user'.$i)
              ->setPassword($this->passwordEncoder->encodePassword($user,'user'.$i))
              ->setRoles(['ROLE_USER']);

         $manager->persist($user);
        }
        for($i=5;$i<10;$i++)
        {
         $user = new User();
         $user->setEmail('user'.$i.'@gmail.com')
              ->setFirstName('user'.$i)
              ->setPassword($this->passwordEncoder->encodePassword($user,'user'.$i))
              ->setRoles(['ROLE_ADMIN']);

         $manager->persist($user);
        }

        $manager->flush();
    }
}
?>