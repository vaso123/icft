<?php

namespace App\Controller;

use App\Service\Artwork\ArtworkTransform;
use App\Service\ChicagoArtApi\ChicagoArtApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="ICF Tech Hungary - Backend Trial Task",
 *     version="1.0.1beta",
 *     description="This API connect to the Chicago Art API, and stores users purchases locally.",
 *     @OA\Contact(
 *         email="vasoczki.ferenc@gmail.com"
 *     ),
 * )
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="bearerAuth",
 *     name="Authorization",
 *     description="JWT Authorization header using the Bearer scheme",
 *     bearerFormat = "JWT",
 * )
 */
class ArtworkController extends AbstractController
{
    /**
     * @param ChicagoArtApiService $chicagoArtApiService
     * @param ArtworkTransform $artworkTransform
     */
    public function __construct(
        private readonly ChicagoArtApiService $chicagoArtApiService,
        private readonly ArtworkTransform $artworkTransform
    ) {
    }

    /**
     * Get details about an artwork by the given Chicago API artwork ID.
     *
     * @param int $id The ID of the artwork.
     * @return JsonResponse JsonResponse The JSON response containing the artwork data, or the details of response.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/api/artworks/id/{id<\d+>}", name="app.artworks.id", methods={"GET"})
     *
     * @OA\Get(
     *     path="/api/artworks/id/{id}",
     *     operationId="getArtworkById",
     *     tags={"Artworks"},
     *     summary="Get artwork by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the artwork",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="ID", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string"),
     *             @OA\Property(
     *                  property="thumbnail",
     *                  type="object",
     *                  nullable=true,
     *                  @OA\Property(property="lqip", type="string"),
     *                  @OA\Property(property="width", type="integer"),
     *                  @OA\Property(property="height", type="integer"),
     *                  @OA\Property(property="alt_text", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Invalid or Expired JWT Token",
     *          @OA\JsonContent(
     *              @OA\Property(property="code", type="integer", example=401),
     *              @OA\Property(property="message", type="string", example="Expired JWT Token")
     *          )
     *     ),
     *      @OA\Response(
     *          response="404",
     *          description="The item you requested cannot be found",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="integer", example=404),
     *              @OA\Property(property="message", type="string", example="The item you requested cannot be found.")
     *          )
     *      )
     * )
     */
    public function getById(int $id): JsonResponse
    {
        $response = $this->chicagoArtApiService->getById($id);
        $status = $this->getStatus($response);
        if ($status === 200) {
            $transformedData = $this->artworkTransform->transfomArtwork($response);
            return $this->json($transformedData);
        }
        return $this->json($response, $status);
    }

    /**
     * Get details of artworks starts at [page] with limit [size].
     *
     * @param int $page
     * @param int $limit
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/api/artworks/pagination/page/{page<\d+>}/size/{limit<\d+>}",
     *     name="app.artworks.pagination", methods={"GET"})
     *
     * @OA\Get(
     *      path="/api/artworks/pagination/page/{page}/size/{limit}",
     *      operationId="getPaginatedArtworks",
     *      tags={"Artworks"},
     *      summary="Get paginated artworks",
     *      @OA\Parameter(
     *          name="page",
     *          in="path",
     *          required=true,
     *          description="Page number",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="path",
     *          required=true,
     *          description="Number of items per page",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Successful response.In real life there will be as many elements
     *                          as you go for the size parameter. ",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="pagination",
     *                  type="object",
     *                  @OA\Property(property="total", type="integer", example=119795),
     *                  @OA\Property(property="limit", type="integer", example=2),
     *                  @OA\Property(property="offset", type="integer", example=38),
     *                  @OA\Property(property="total_pages", type="integer", example=59898),
     *                  @OA\Property(property="current_page", type="integer", example=20),
     *                  @OA\Property(property="prev_url", type="string", example="https://api.artic.edu/api/v1/artworks?page=19&limit=2&fields=id%2Ctitle%2Cartist_title%2Cthumbnail"),
     *                  @OA\Property(property="next_url", type="string", example="https://api.artic.edu/api/v1/artworks?page=21&limit=2&fields=id%2Ctitle%2Cartist_title%2Cthumbnail")
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="ID", type="integer", example=57050),
     *                      @OA\Property(property="title", type="string", example="The Landing Place"),
     *                      @OA\Property(property="author", type="string", example="Hubert Robert"),
     *                      @OA\Property(property="thumbnail", type="object", nullable=true,
     *                          @OA\Property(property="lqip", type="string",
     *                              example="data:image/gif;base64,R0lGODlhBA..."),
     *                          @OA\Property(property="width", type="integer", example=9000),
     *                          @OA\Property(property="height", type="integer", example=10340),
     *                          @OA\Property(property="alt_text", type="string",
     *                              example="A work made of oil on canvas.")
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *           response="401",
     *           description="Invalid or Expired JWT Token",
     *           @OA\JsonContent(
     *               @OA\Property(property="code", type="integer", example=401),
     *               @OA\Property(property="message", type="string", example="Expired JWT Token")
     *           )
     *      )
     *  )
     */
    public function getPaginated(int $page, int $limit): JsonResponse
    {
        $response = $this->chicagoArtApiService->getByPage($page, $limit);
        $status = $this->getStatus($response);
        if ($status === 200) {
            $transformedData = $this->artworkTransform->transformArtworks($response["data"]);
            $response["data"] = $transformedData;
        }
        return $this->json($response, $this->getStatus($response));
    }

    /**
     * @param array $response
     * @return int
     */
    private function getStatus(array $response): int
    {
        if (!empty($response['status'])) {
            return (int)$response['status'];
        }
        return 200;
    }
}
