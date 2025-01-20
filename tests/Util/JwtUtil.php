<?php

namespace App\Tests\Util;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use App\Entity\User;

class JwtUtil
{
    private $jwtManager;
    private $userRepository;

    public function __construct(JWTManager $jwtManager, \Doctrine\ORM\EntityRepository $userRepository)
    {
        $this->jwtManager = $jwtManager;
        $this->userRepository = $userRepository;
    }

    public function generateJwtForUser(int $userId): string
    {
        $user = $this->userRepository->find($userId);
        return $this->jwtManager->create($user);
    }
}
