<?php

namespace App\Tests\Database;

use App\Repository\UserRepository;
use App\Service\User\UserService;
use App\Tests\DatabaseDependantTestCase;
use App\Tests\TestUserService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class DatabaseTest extends DatabaseDependantTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $client = self::getContainer()->get('test.client');
        $this->testUserService = self::getContainer()->get(TestUserService::class);
        $this->userService = $client->getContainer()->get(UserService::class);
    }


    /**
     * @test
     * @return void
     */
    public function testCanCreateUserEntityInDatabase(): void
    {
        $claims = $this->testUserService->getUserOneClaimsForTest();
        $this->testUserService->loadUserIntoDatabase($claims);
        $email = $claims["email"];
        /* @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $storedUser = $userRepository->findOneBy(["email" => $email]);
        $this->assertEquals($email, $storedUser->getEmail());
    }

    /**
     * @test
     * @return void
     */
    public function testEmailFildIsUnique(): void
    {
        $claims = $this->testUserService->getUserOneClaimsForTest();
        $this->testUserService->loadUserIntoDatabase($claims);
        $this->expectException(UniqueConstraintViolationException::class);
        $this->testUserService->loadUserIntoDatabase($claims);
    }
}
