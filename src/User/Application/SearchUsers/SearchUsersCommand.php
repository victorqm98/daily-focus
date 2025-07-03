<?php

declare(strict_types=1);

namespace DailyFocus\User\Application\SearchUsers;

final class SearchUsersCommand
{
    private string $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function username(): string
    {
        return $this->username;
    }
}
