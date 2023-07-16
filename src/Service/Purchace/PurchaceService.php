<?php

namespace App\Service\Purchace;

use App\Factory\PurchaceFactory;
use App\Repository\PurchaseRepository;
use App\Service\Artwork\ArtworkTransform;
use App\Service\ChicagoArtApi\ChicagoArtApiService;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PurchaceService
{
    /**
     * @param PurchaseRepository $purchaseRepository
     * @param PurchaseTransform $purchaseTransform
     * @param ArtworkTransform $artworkTransform
     * @param UserService $userService
     * @param EntityManagerInterface $entityManager
     * @param ChicagoArtApiService $chicagoArtApiService
     * @param PurchaceFactory $purchaceFactory
     */
    public function __construct(
        private readonly PurchaseRepository $purchaseRepository,
        private readonly PurchaseTransform $purchaseTransform,
        private readonly ArtworkTransform $artworkTransform,
        private readonly UserService $userService,
        private readonly EntityManagerInterface $entityManager,
        private readonly ChicagoArtApiService $chicagoArtApiService,
        private readonly PurchaceFactory $purchaceFactory
    ) {
    }

    /**
     * @param $id
     * @return bool
     */
    public function isArtworkAvailable(
        $id
    ): bool {
        $purchase = $this->purchaseRepository->findOneBy(["itemId" => $id]);
        if (empty($purchase)) {
            return true;
        }
        return false;
    }


    /**
     * @param int $id
     * @return array
     * @throws ClientExceptionInterface
     * @throws JWTDecodeFailureException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function purchcaArtworkById(int $id): array
    {
        $artworkData = $this->chicagoArtApiService->getById($id);
        if (empty($artworkData["id"])) {
            return $artworkData;
        }

        if (!$this->isArtworkAvailable($id)) {
            return ["status" => 400, "message" => "Item is not available for purchace. It has already sold."];
        }
        return $this->doPurchaceArtwork($artworkData);
    }

    /**
     * @param string $userEmail
     * @return array
     */
    public function getPurchasesByUserEmail(string $userEmail): array
    {
        $user = $this->userService->getCurrentUserByEmail($userEmail);
        if (empty($user)) {
            return ["status" => 400, "message" => "User not found by email: " . $userEmail];
        }
        if (!$this->userService->hasBuyRole($user)) {
            return ["status" => 400, "message" => "This user has no buy permission: " . $userEmail];
        }
        $purchases = $user->getPurchases();
        $transformedPurchases = $this->purchaseTransform->transformPurchasesToArray($purchases);
        return ["user" => ["id" => $user->getId(), "email" => $user->getEmail()], "purchases" => $transformedPurchases];
    }


    /**
     * @param array $artworkData
     * @return array
     * @throws JWTDecodeFailureException
     */
    private function doPurchaceArtwork(
        array $artworkData
    ): array {
        $userCredentals = $this->userService->getCurrentUserCredentialsByToken();
        if (empty($userCredentals["email"])) {
            return ["status" => 404, "message" => "User not found by token. Maybe token expired."];
        }
        $user = $this->userService->getCurrentUserByEmail($userCredentals["email"]);
        if (!$this->userService->hasBuyRole($user)) {
            return ["status" => 400, "message" => "This user does not have the right to buy."];
        }
        $transformedArtworkData = $this->artworkTransform->transformArtworks([$artworkData]);
        $purchace = $this->purchaceFactory->create($user, $transformedArtworkData[0]);
        $this->entityManager->persist($purchace);
        $this->entityManager->flush();

        return ["user" => $user->getEmail(), "artworkId" => $purchace->getId()];
    }
}
