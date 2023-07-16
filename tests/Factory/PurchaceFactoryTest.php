<?php

namespace App\Tests\Factory;

use App\Entity\Purchase;
use App\Factory\PurchaceFactory;
use App\Tests\Entity\TestUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class PurchaceFactoryTest extends TestCase
{
    public function testIsAllDataSetByCreate()
    {
        $testUser = new TestUser();
        $testUser->setId(1);
        $testUser->setEmail('user1@email.com');
        $testUser->setPassword('password');
        $testUser->setRoles(["ROLE_USER", "ROLE_BUY"]);

        $artworkData = [
            "ID"        => 129884,
            "title"     => "Starry Night and the Astronauts",
            "author"    => "Alma Thomas",
            "thumbnail" => [
                "lqip"     => "data:image/gif;base64,somebase64data",
                "width"    => 800,
                "height"   => 600,
                "alt_text" => "Al text"
            ]
        ];

        $jsonEncoder = new JsonEncoder();
        $encodedThumbnail = $jsonEncoder->encode($artworkData["thumbnail"], JsonEncoder::FORMAT);

        $jsonEncoderMock = $this->createMock(JsonEncoder::class);
        $jsonEncoderMock->method("encode")
            ->willReturn(
                $encodedThumbnail
            );

        $purchaceFactory = new PurchaceFactory($jsonEncoderMock);
        $purchace = $purchaceFactory->create($testUser, $artworkData);
        $this->assertInstanceOf(Purchase::class, $purchace);
        $this->assertSame($artworkData["ID"], $purchace->getItemId());
        $this->assertSame($artworkData["title"], $purchace->getTitle());
        $this->assertSame($artworkData["author"], $purchace->getAuthor());
        $this->assertSame($testUser, $purchace->getUser());
        $this->assertSame($encodedThumbnail, $purchace->getThumbnail());
        $this->assertSame(
            $jsonEncoderMock->encode($artworkData["thumbnail"], JsonEncoder::FORMAT),
            $purchace->getThumbnail()
        );
    }
}
