<?php

namespace App\Interface;

use App\Repository\Account;

interface BankInterface
{
    public function addBankAccount(Account $bankAccount): void;
    public function getBankAccounts(): array;
    public function getPostalAddress(): string;
    public function makeTransaction(string $fromAccountNumber, string $toAccountNumber, float $amount): void;
}
