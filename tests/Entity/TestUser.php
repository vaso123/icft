<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table() (name="user")
 */
class TestUser extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
}
