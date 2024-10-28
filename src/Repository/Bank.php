<?php

namespace App\Repository;

use App\Interface\BankInterface;
use Exception;

class Bank implements BankInterface
{
    /**
     * @throws \Exception
     */
    public function __construct(
        private readonly string $name,
        private readonly string $address,
        public array            $bankAccounts = []
    ) {
        if (empty($name)) {
            throw new Exception('Bank creation fail: must have a name');
        }

        if (empty($address)) {
            throw new Exception('Bank creation fail: must have an address');
        }
    }

    public function addBankAccount(Account $bankAccount): void
    {
        $this->bankAccounts[$bankAccount->getAccountNumber()] = $bankAccount;
    }

    public function getBankAccounts(): array
    {
        return $this->bankAccounts;
    }

    public function getPostalAddress(): string
    {
        return $this->name ."\n".$this->address;
    }

    /**
     * @throws Exception
     */
    public function makeTransaction(string $fromAccountNumber, string $toAccountNumber, float $amount): void
    {
        // Allow only transactions between accounts in same bank
        if (!isset($this->bankAccounts[$fromAccountNumber]) || !isset($this->bankAccounts[$toAccountNumber])) {
            throw new \Exception('Both accounts must belong to the same bank');
        }

        $fromAccount = $this->bankAccounts[$fromAccountNumber];
        $toAccount = $this->bankAccounts[$toAccountNumber];

        if ($fromAccount->getBalance() < $amount) {
            throw new \Exception('Insufficient funds for the transfer');
        }

        $fromAccount->makeWithdrawal($amount);
        $toAccount->makeDeposit($amount);
    }
}