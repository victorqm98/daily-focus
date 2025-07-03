<?php

declare(strict_types=1);

namespace DailyFocus\User\Application\RegisterUser;

use DailyFocus\User\Domain\User;
use DailyFocus\User\Domain\UserRepository;
use DailyFocus\Shared\Domain\ValueObjects\Email;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use InvalidArgumentException;

final class RegisterUserUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(RegisterUserCommand $command): void
    {
        // Verificar que el email no esté en uso
        if ($this->userRepository->findByEmail($command->email()) !== null) {
            throw new InvalidArgumentException('Email already exists');
        }
        
        // Verificar que el username no esté en uso
        if ($this->userRepository->findByUsername($command->username()) !== null) {
            throw new InvalidArgumentException('Username already exists');
        }

        $user = User::create(
            Id::random(),
            $command->username(),
            Email::fromString($command->email()),
            $command->password()
        );

        $this->userRepository->save($user);
    }
}
