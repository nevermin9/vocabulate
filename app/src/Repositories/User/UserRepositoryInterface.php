<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserRepositoryInterface
{
    public function save(User $user): User;
    public function update(User $user, array $data): ?User;
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User; 
    public function verifyUser(User $user): ?User;
    public function updatePassword(User $user, string $passwordHash): ?User;
}
