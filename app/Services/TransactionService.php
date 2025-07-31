<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\TransactionRepository;

class TransactionService
{
    protected $repo;

    public function __construct(TransactionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getTransactions(User $user)
    {
        return $this->repo->getUserTransactions($user);
    }

    public function topUp(User $user, float $amount)
    {
        $this->repo->updateUserBalance($user, $user->balance + $amount);
        $this->repo->createTransaction($user, [
            'type' => 'top-up',
            'amount' => $amount,
            'description' => 'Account Top-up',
        ]);
    }

    public function payBill(User $user, string $biller, float $amount)
    {
        $feePercent = match($biller) {
            'electricity' => 0.10,
            'water' => 0.05,
            'internet' => 0,
        };

        $total = $amount + ($amount * $feePercent);

        if ($user->balance < $total) {
            throw new \Exception("Insufficient balance.");
        }

        $this->repo->updateUserBalance($user, $user->balance - $total);
        $this->repo->createTransaction($user, [
            'type' => 'bill',
            'amount' => $total,
            'description' => ucfirst($biller) . ' Bill Payment',
        ]);
    }

    public function transfer(User $sender, string $recipientEmail, float $amount)
    {
        $recipient = $this->repo->findUserByEmail($recipientEmail);

        if (!$recipient || $recipient->id === $sender->id) {
            throw new \Exception("Invalid recipient.");
        }

        if ($sender->balance < $amount) {
            throw new \Exception("Insufficient balance.");
        }

        $this->repo->updateUserBalance($sender, $sender->balance - $amount);
        $this->repo->updateUserBalance($recipient, $recipient->balance + $amount);

        $this->repo->createTransaction($sender, [
            'type' => 'transfer',
            'amount' => $amount,
            'description' => "Transfer to {$recipient->email}",
        ]);

        $this->repo->createTransaction($recipient, [
            'type' => 'top-up',
            'amount' => $amount,
            'description' => "Received from {$sender->email}",
        ]);
    }
}
