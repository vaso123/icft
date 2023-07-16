<?php

namespace App\Tests\Controller;

use App\Service\Url\UrlService;
use App\Tests\DatabaseDependantTestCase;
use App\Tests\TestUserService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\LcobucciJWTEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ArtworkControllerTest extends DatabaseDependantTestCase
{
    /**
     * @var TestUserService
     */
    private TestUserService $testUserService;

    /**
     * @var JsonDecode
     */
    private JsonDecode $jsonDecode;

    /**
     * @var string
     */
    private string $baseUrl;


    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    public function setUp(): void
    {
        parent::setUp();
        /* @var KernelBrowser $client */
        $client = self::getContainer()->get('test.client');
        $this->baseUrl = self::getContainer()->getParameter('base_url');
        $this->urlService = $client->getContainer()->get(UrlService::class);
        $this->testUserService = $client->getContainer()->get(TestUserService::class);
        $this->jsonDecode = $client->getContainer()->get(JsonDecode::class);
    }

    /**
     * @return void
     * @throws JWTEncodeFailureException
     * @test
     */
    public function testGetByIdSuccess()
    {
        $claims = $this->testUserService->getUserOneClaimsForTest();
        $this->testUserService->loadUserIntoDatabase($claims);
        $client = $this->getClientForUserByClaims($claims);
        $client->request('GET', $this->baseUrl . 'artworks/id/129884');
        /* @var JsonResponse $response */
        $response = $client->getResponse();
        $content = (array)$this->jsonDecode->decode($response->getContent(), JsonEncoder::FORMAT);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($content["ID"]);
    }

    /**
     * @return void
     * @throws JWTEncodeFailureException
     * @test
     */
    public function testGetByIdItemNotFound()
    {
        $claims = $this->testUserService->getUserOneClaimsForTest();
        $this->testUserService->loadUserIntoDatabase($claims);
        $client = $this->getClientForUserByClaims($claims);
        $client->request('GET', $this->baseUrl . 'artworks/id/12');
        /* @var JsonResponse $response */
        $response = $client->getResponse();
        $expectedResponseBody = '{"status":404,"message":"The item you requested cannot be found."}';
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame($expectedResponseBody, $response->getContent());
    }

    /**
     * @return void
     * @throws JWTEncodeFailureException
     */
    public function testPaginationdSuccess()
    {
        $claims = $this->testUserService->getUserOneClaimsForTest();
        $this->testUserService->loadUserIntoDatabase($claims);
        $client = $this->getClientForUserByClaims($claims);
        $client->request('GET', $this->baseUrl . 'artworks/pagination/page/1/size/10');
        /* @var JsonResponse $response */
        $response = $client->getResponse();
        $content = (array)$this->jsonDecode->decode($response->getContent(), JsonEncoder::FORMAT);
        $this->assertArrayHasKey("pagination", $content);
        $this->assertNotEmpty($content["pagination"]);
        $this->assertArrayHasKey("data", $content);
        $this->assertIsArray($content["data"]);
        $this->assertCount(10, $content["data"]);
    }


    /**
     * @return void
     * @throws JWTEncodeFailureException
     * @todo Need to outsource from here, it's general, not related to an artwork.
     */
    public function testInvalidWhenNoUserFoundOrBadCredentials()
    {
        $claims = $this->testUserService->getUserOneClaimsForTest();
        $client = $this->getClientForUserByClaims($claims);
        $client->request('GET', $this->baseUrl . 'artworks/id/129884');
        $response = $client->getResponse();
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('{"code":401,"message":"Invalid credentials."}', $response->getContent());
    }

    /**
     * @param array $claims
     * @return KernelBrowser
     * @throws JWTEncodeFailureException
     * @todo Need to outsource from here, maybe I will use it for purchaces also.
     */
    private function getClientForUserByClaims(array $claims): KernelBrowser
    {
        /* @var KernelBrowser $client */
        $client = self::getContainer()->get('test.client');
        /* @var LcobucciJWTEncoder $encoder */
        $encoder = $client->getContainer()->get(JWTEncoderInterface::class);
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $encoder->encode($claims)));
        return $client;
    }
}
