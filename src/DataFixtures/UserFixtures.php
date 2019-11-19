<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('shibalba1@gmail.com');
        $user->setApiKey('1fcf4727dadb122e4aa0085a47501bcb');
        $user->setRoles(['USER_ROLE']);

        $manager->persist($user);
        $manager->flush();
    }
}
