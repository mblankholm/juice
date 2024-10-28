<?php

namespace App\Interface;

interface AccountInterface
{
    public function getAccountNumber(): string;
    public function makeDeposit(float $deposit): void;
    public function makeWithdrawal(float $withdrawal): void;
    public function getWithdrawals(): array;
    public function getDeposits(): array;
    public function getTransactions(): array;
    public function getBalance(): float;
}
