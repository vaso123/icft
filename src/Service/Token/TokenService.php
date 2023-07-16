<?php

namespace App\Service\Token;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TokenService
{
    /**
     * @param TokenStorageInterface $tokenStorage
     * @param JWTTokenManagerInterface $JWTTokenManager
     * @param JWTEncoderInterface $JWTEncoder
     */
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly JWTTokenManagerInterface $JWTTokenManager,
        private readonly JWTEncoderInterface $JWTEncoder
    ) {
    }

    /**
     * @return bool|array
     * @throws JWTDecodeFailureException
     */
    public function getUserDataFromToken(): bool|array
    {
        $token = $this->tokenStorage->getToken();
        return $this->decodeToken($token);
    }

    /**
     * @param TokenInterface $token
     * @return array|false
     * @throws JWTDecodeFailureException
     */
    public function decodeToken(TokenInterface $token): bool|array
    {
        return $this->JWTTokenManager->decode($token);
    }

    /**
     * @param string $email
     * @param string $password
     * @param array $roles
     * @param int $expire
     * @return string
     * @throws JWTEncodeFailureException
     */
    public function createToken(
        string $email,
        string $password,
        array $roles = ["ROLE_USER"],
        int $expire = 0
    ): string {
        //If no expire gven, then set it to 10 hours
        if (empty($expire)) {
            $expire = empty($exp) ? time() + (10 * 3600) : $expire;
        }
        $claims = [
            'email'    => $email,
            'roles'    => $roles,
            'password' => $password,
            'exp'      => $expire
        ];
        return $this->JWTEncoder->encode($claims);
    }
}
