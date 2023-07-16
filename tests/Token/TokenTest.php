<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Tests\Token;

use App\Service\Token\TokenService;
use App\Tests\DatabaseDependantTestCase;
use App\Tests\TestUserService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TokenTest extends DatabaseDependantTestCase
{
    /**
     * @var string
     */
    private string $baseUrl;

    /**
     * @var TestUserService
     */
    private TestUserService $testUserService;

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    public function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = self::getContainer()->getParameter('base_url');
        $this->testUserService = self::getContainer()->get(TestUserService::class);
    }

    /**
     * Test for request, when no token provided to the exernal API.
     *
     * @return void
     * @test
     */
    public function testWhenJWTTokenNotFound(): void
    {
        /* @var KernelBrowser $client */
        $client = self::getContainer()->get('test.client');
        $client->request("GET", $this->baseUrl . "artworks/id/129884");

        $response = $client->getResponse();
        $this->assertEquals('{"code":401,"message":"JWT Token not found"}', $response->getContent());
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test for request, when token expired.
     *
     * @return void
     * @test
     * @throws JWTEncodeFailureException
     */
    public function testWhenJWTTokenExpires(): void
    {
        $claims = $this->testUserService->getUserOneClaimsForTest();
        $this->testUserService->loadUserIntoDatabase($claims);

        /* @var TokenService $tokenService */
        $tokenService = self::getContainer()->get(TokenService::class);
        $token = $tokenService->createToken(
            $claims["email"],
            $claims["password"],
            expire: 1
        );
        sleep(1);
        /* @var KernelBrowser $client */
        $client = self::getContainer()->get('test.client');
        $client->setServerParameter('HTTP_Authorization', 'Bearer ' . $token);
        $client->request("GET", $this->baseUrl . "artworks/id/129884");
        $response = $client->getResponse();
        $this->assertEquals('{"code":401,"message":"Expired JWT Token"}', $response->getContent());
        $this->assertEquals(401, $response->getStatusCode());
    }
}
