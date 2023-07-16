<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TestUserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param array $claims
     * @return User
     */
    public function getUserEntityByCredentialsForTest(array $claims): User
    {
        $user = new User();
        $user->setEmail($claims["email"]);
        $user->setPassword($claims["password"]);
        $user->setRoles($claims["roles"]);
        return $user;
    }

    /**
     * @return array
     */
    public function getUserOneClaimsForTest(): array
    {
        return [
            "email"    => "user1@email.com",
            "roles"    => ["ROLE_USER", "ROLE_BUY"],
            "password" => '$2y$13$O03RX4UQj93p5Nd9hyT1i.nudYqriGpFNZCefvdstutrQdm/eh1km'
        ];
    }

    /**
     * @return array
     */
    public function getUserTwoClaimsForTest(): array
    {
        return [
            "email"    => "use21@email.com",
            "roles"    => ["ROLE_USER"],
            "password" => '$2y$13$O03RX4UQj93p5Nd9hyT1i.nudYqriGpFNZCefvdstutrQdm/eh1km'
        ];
    }

    /**
     * @param array $claims
     * @return void
     */
    public function loadUserIntoDatabase(array $claims): void
    {
        $user = $this->getUserEntityByCredentialsForTest($claims);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
