<?php

namespace App\Service\Purchace;

use App\Entity\Purchase;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class PurchaseTransform
{
    /**
     * @param JsonEncoder $jsonEncoder
     */
    public function __construct(
        private readonly JsonEncoder $jsonEncoder
    ) {
    }

    /**
     * @param Collection $purchases
     * @return array
     */
    public function transformPurchasesToArray(Collection $purchases): array
    {
        $transformedPurchases = [];
        foreach ($purchases as $purchase) {
            /* var Purchase $purchase */
            $transformedPurchases[] = $this->transformPurchaseToArray($purchase);
        }
        return $transformedPurchases;
    }

    /**
     * @param Purchase $purchase
     * @return array
     */
    public function transformPurchaseToArray(Purchase $purchase): array
    {
        $purchaseData = [
            "itemId" => $purchase->getItemId(),
            "title"  => $purchase->getTitle(),
            "author" => $purchase->getAuthor()
        ];

        if (!empty($purchase->getThumbnail())) {
            $purchaseData["thumbnail"] = $this->jsonEncoder->decode($purchase->getThumbnail(), JsonEncoder::FORMAT);
        }
        return $purchaseData;
    }
}
