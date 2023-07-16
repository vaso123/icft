<?php

namespace App\Factory;

use App\Entity\Purchase;
use App\Entity\User;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class PurchaceFactory
{
    public function __construct(
        private readonly JsonEncoder $jsonEncoder
    ) {
    }

    /**
     * @param User $user
     * @param array $artworkData
     * @return Purchase
     */
    public function create(User $user, array $artworkData): Purchase
    {
        $purchace = new Purchase();
        $purchace->setUser($user);
        $purchace->setItemId($artworkData["ID"]);
        $purchace->setAuthor($artworkData["author"]);
        $purchace->setTitle($artworkData["title"]);
        if (!empty($artworkData["thumbnail"])) {
            $purchace->setThumbnail($this->jsonEncoder->encode($artworkData["thumbnail"], JsonEncoder::FORMAT));
        }
        return $purchace;
    }
}
