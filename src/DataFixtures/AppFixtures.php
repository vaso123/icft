<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\User\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->createUsers($manager);
    }

    /**
     * @param ObjectManager $Manager
     * @return void
     */
    private function createUsers(ObjectManager $Manager): void
    {
        for ($i = 1; $i <= 2; $i++) {
            $user = new User();
            $user->setEmail("user" . $i . "@email.com");
            $password = $this->hasher->hashPassword($user, 'password');
            if ($i == 1) {
                $user->setRoles([UserService::ROLE_USER, UserService::ROLE_BUY]);
            } else {
                $user->setRoles(['ROLE_USER']);
            }
            $user->setPassword($password);
            $Manager->persist($user);
        }
        $Manager->flush();
    }
}
