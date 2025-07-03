<?php

declare(strict_types=1);

namespace DailyFocus\User\Domain;

use DateTimeImmutable;
use DailyFocus\Shared\Domain\ValueObjects\Email;
use DailyFocus\Shared\Domain\ValueObjects\Id;

final class User
{
    private Id $id;
    private string $username;
    private Email $email;
    private string $hashedPassword;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Id $id,
        string $username,
        Email $email,
        string $hashedPassword
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->hashedPassword = $hashedPassword;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function create(
        Id $id,
        string $username,
        Email $email,
        string $password
    ): self {
        return new self(
            $id,
            $username,
            $email,
            self::hashPassword($password)
        );
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->hashedPassword);
    }

    private static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}