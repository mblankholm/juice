<?php

namespace App\Repository;

use App\Interface\AccountInterface;
use App\Validator\AccountNumberValidator;
use Exception;

class Account implements AccountInterface
{
    /**
     * @throws Exception
     */
    public function __construct(
        private readonly string $accountNumber,
        private float           $balance,
        private array           $transactions = []
    )
    {
        if (AccountNumberValidator::isUnique($accountNumber)) {
            AccountNumberValidator::addAccountNumber($accountNumber);
        } else {
            throw new Exception('Account creation fail: account number already exist or is empty');
        }
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @throws Exception
     */
    public function makeDeposit(float $deposit): void
    {
        if ($deposit <= 0) {
            throw new \Exception('Deposit amount must be positive');
        }

        /*
         * In order to avoid floating-point arithmetics, we use bcsub with 2 digits after the decimal, you could
         * consider converting into something thats not using decimals for more precise calculations in real life
         *
         * Fx. 23.55 minus 3.55 resulted in 3.5500000000000007
         */

        $this->balance = bcadd($this->balance, $deposit, 2);
        $this->transactions[] = ['type' => 'deposit', 'amount' => $deposit];
    }

    /**
     * @throws Exception
     */
    public function makeWithdrawal(float $withdrawal): void
    {
        if ($withdrawal <= 0) {
            throw new \Exception('Withdrawal amount must be positive');
        }

        if ($withdrawal > $this->balance) {
            throw new \Exception('Insufficient funds for withdrawal');
        }

        /*
         * In order to avoid floating-point arithmetics, we use bcsub with 2 digits after the decimal, you could
         * consider converting into something thats not using decimals for more precise calculations in real life
         *
         * Fx. 23.55 minus 3.55 resulted in 3.5500000000000007
         */
        $this->balance = bcsub($this->balance, $withdrawal, 2);

        $this->transactions[] = ['type' => 'withdrawal', 'amount' => $withdrawal];
    }

    public function getWithdrawals(): array
    {
        return array_filter($this->transactions, function($w) {
            return $w['type'] == 'withdrawal';
        });
    }

    public function getDeposits(): array
    {
        return array_filter($this->transactions, function($d) {
            return $d['type'] == 'deposit';
        });
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
}