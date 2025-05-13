<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService
{

    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * @return User[]
     */
    public function findAll(): array {
        return $this->userRepository->findBy([], ['username' => 'ASC']);
    }

}