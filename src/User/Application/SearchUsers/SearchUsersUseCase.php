<?php

declare(strict_types=1);

namespace DailyFocus\User\Application\SearchUsers;

use DailyFocus\User\Domain\UserRepository;

final class SearchUsersUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(SearchUsersCommand $command): array
    {
        return $this->userRepository->searchByUsername($command->username());
    }
}
