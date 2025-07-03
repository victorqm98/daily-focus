<?php

declare(strict_types=1);

namespace DailyFocus\User\Application\LoginUser;

use DailyFocus\User\Domain\User;
use DailyFocus\User\Domain\UserRepository;
use DailyFocus\Shared\Domain\ValueObjects\Email;
use InvalidArgumentException;

final class LoginUserUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(LoginUserCommand $command): User
    {
        $email = Email::fromString($command->email());
        $user = $this->userRepository->findByEmail($email->value());

        if ($user === null) {
            throw new InvalidArgumentException('Invalid credentials');
        }

        if (!$user->verifyPassword($command->password())) {
            throw new InvalidArgumentException('Invalid credentials');
        }

        return $user;
    }
}
