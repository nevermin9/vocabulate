<?php
declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function save(User $user): User
    {
        $user->save();
        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return User::findOne(['email' => $email]);
    }

    public function findById(string $id): ?User
    {
        return User::findOne(['id' => $id]);
    }

    public function update(User $user, array $data): ?User
    {
        return $user->update($data);
    }

    public function verifyUser(User $user): ?User
    {
        return $this->update($user, ["verified" => 1]);
    }

    public function updatePassword(User $user, string $passwordHash): ?User
    {
        return $this->update($user, ["password_hash" => $passwordHash]);
    }
}
