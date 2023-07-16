<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Token\TokenService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;

class UserService
{
    public const ROLE_BUY = "ROLE_BUY";
    public const ROLE_USER = "ROLE_USER";

    /**
     * @param TokenService $tokenService
     * @param UserRepository $userRepository
     */
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * @return array
     * @throws JWTDecodeFailureException
     */
    public function getCurrentUserCredentialsByToken(): array
    {
        $tokenData = $this->tokenService->getUserDataFromToken();
        if (empty($tokenData["email"])) {
            return [];
        }
        return ["email" => $tokenData["email"], "roles" => $tokenData["roles"]];
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getCurrentUserByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasBuyRole(User $user): bool
    {
        $roles = $user->getRoles();
        if (in_array(UserService::ROLE_BUY, $roles)) {
            return true;
        }
        return false;
    }


}
