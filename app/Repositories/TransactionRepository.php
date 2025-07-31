<?php
namespace App\Repositories;

use App\Models\Transaction;
use App\Models\User;

class TransactionRepository
{
    public function getUserTransactions(User $user)
    {
        return $user->transactions()->latest()->get();
    }

    public function createTransaction(User $user, array $data)
    {
        return $user->transactions()->create($data);
    }

    public function updateUserBalance(User $user, float $amount)
    {
        $user->balance = $amount;
        $user->save();
    }

    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
