<?php

declare(strict_types=1);

namespace DailyFocus\Tests\User\Domain;

use DailyFocus\User\Domain\User;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use DailyFocus\Shared\Domain\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function test_user_can_be_created(): void
    {
        $userId = Id::random();
        $username = 'testuser';
        $email = Email::fromString('test@example.com');
        $password = 'password123';

        $user = User::create($userId, $username, $email, $password);

        $this->assertEquals($userId, $user->id());
        $this->assertEquals($username, $user->username());
        $this->assertEquals($email, $user->email());
        $this->assertTrue($user->verifyPassword($password));
        $this->assertFalse($user->verifyPassword('wrongpassword'));
    }

    public function test_user_password_is_hashed(): void
    {
        $userId = Id::random();
        $username = 'testuser';
        $email = Email::fromString('test@example.com');
        $password = 'password123';

        $user = User::create($userId, $username, $email, $password);

        $this->assertNotEquals($password, $user->hashedPassword());
        $this->assertTrue(password_verify($password, $user->hashedPassword()));
    }
}
