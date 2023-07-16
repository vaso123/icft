<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ChicagoArtApi\ChicagoArtApiService;
use App\Service\Purchace\PurchaceService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use OpenApi\Annotations as OA;

class PurchaceConroller extends AbstractController
{
    public function __construct(
        private readonly ChicagoArtApiService $chicagoArtApiService,
        private readonly PurchaceService $purchaceService
    ) {
    }

    /**
     * Purchace an Artwork by the given Chicago Api Artwork ID
     *
     *
     * @param Request $request
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/api/artworks/purchace/", name="app.artworks.purchace", methods={"POST"}))
     *
     * @OA\Post(
     *     path="/api/artworks/purchace/",
     *     operationId="purchaseArtworkById",
     *     tags={"Artworks"},
     *     summary="Purchase artwork by ID",
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON payload for artwork purchase",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Artwork purchased successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="user", type="string"),
     *              @OA\Property(property="artworkId", type="integer")
     *          )
     *      ),
     *      @OA\Response(
     *           response="401",
     *           description="Invalid or Expired JWT Token.",
     *           @OA\JsonContent(
     *               @OA\Property(property="code", type="integer", example=401),
     *               @OA\Property(property="message", type="string", example="Expired JWT Token")
     *           )
     *      ),
     *      @OA\Response(
     *           response="400",
     *           description="Item has sold, user have no right to buy or the POST body is malformed.",
     *           @OA\JsonContent(
     *               @OA\Property(property="code", type="integer", example=401),
     *               @OA\Property(property="message", type="string", example="Expired JWT Token")
     *           )
     *      ),
     *      @OA\Response(
     *           response="404",
     *           description="Item not found",
     *           @OA\JsonContent(
     *               @OA\Property(property="code", type="integer", example=400),
     *               @OA\Property(property="message", type="string", example="The item you requested cannot be found.")
     *           )
     *      )
     * )
     */
    public function purchaceById(Request $request): JsonResponse
    {
        $requestBody = $request->getContent();
        $decodedBody = $this->chicagoArtApiService->parsePostData($requestBody);
        if (is_array($decodedBody)) {
            return $this->json($decodedBody, $decodedBody["status"]);
        }
        $response = $this->purchaceService->purchcaArtworkById($decodedBody);
        if (!empty($response["status"])) {
            return $this->json($response, $response["status"]);
        }
        return $this->json($response);
    }

    /**
     * List purchases by a user by the ID of user.
     *
     *
     * @param string $userEmail
     * @return JsonResponse
     * @Route("/api/artworks/purchace/get/{userEmail}", name="app.artworks.get", methods={"GET"}))
     *
     * @OA\Get(
     *      path="/api/artworks/purchace/get/{userEmail}",
     *      operationId="getPurchasesByUserEmail",
     *      tags={"Artworks"},
     *      summary="Get purchases by user email",
     *      @OA\Parameter(
     *          name="userEmail",
     *          in="path",
     *          required=true,
     *          description="User's email",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="Successful response",
     *           @OA\JsonContent(
     *               @OA\Property(
     *                   property="user",
     *                   type="object",
     *                   @OA\Property(property="id", type="integer"),
     *                   @OA\Property(property="email", type="string")
     *               ),
     *               @OA\Property(
     *                   property="purchases",
     *                   type="array",
     *                   @OA\Items(
     *                       @OA\Property(property="itemId", type="integer"),
     *                       @OA\Property(property="title", type="string"),
     *                       @OA\Property(property="author", type="string"),
     *                       @OA\Property(
     *                           property="thumbnail",
     *                           type="object",
     *                           @OA\Property(property="lqip", type="string"),
     *                           @OA\Property(property="width", type="integer"),
     *                           @OA\Property(property="height", type="integer"),
     *                           @OA\Property(property="alt_text", type="string")
     *                       )
     *                   )
     *               )
     *           )
     *      ),
     *      @OA\Response(
     *           response="400",
     *           description="User not found by email, user has no buy permission",
     *           @OA\JsonContent(
     *               @OA\Property(property="code", type="integer", example=400),
     *               @OA\Property(property="message", type="string",
     *                  example="This user has no buy permission: user2@email.com")
     *           )
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="User or purchases not found"
     *      ),
     *       @OA\Response(
     *            response="401",
     *            description="Invalid or Expired JWT Token",
     *            @OA\JsonContent(
     *                @OA\Property(property="code", type="integer", example=401),
     *                @OA\Property(property="message", type="string", example="Expired JWT Token")
     *            )
     *      )
     *  )
     */
    public function getPurchasesByUserEmail(string $userEmail): JsonResponse
    {
        $response = $this->purchaceService->getPurchasesByUserEmail($userEmail);
        if (count($response) === 2 && !empty($response["status"])) {
            return $this->json($response, $response["status"]);
        }
        return $this->json($response);
    }
}
