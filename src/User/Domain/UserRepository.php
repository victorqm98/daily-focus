<?php

declare(strict_types=1);

namespace DailyFocus\User\Domain;

interface UserRepository
{
    public function save(User $user): void;
    
    public function findById(string $id): ?User;
    
    public function findByEmail(string $email): ?User;
    
    public function findByUsername(string $username): ?User;
    
    public function searchByUsername(string $username): array;
}
