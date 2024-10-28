<?php

namespace App\Tests\Repository;

use App\Repository\Account;
use App\Repository\Bank;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testAccount()
    {
        $account = new Account(accountNumber: 'ab021', balance: 100);
        $this->assertInstanceOf(Account::class, $account);
        $this->assertEquals('ab021', $account->getAccountNumber());
    }

    public function testDuplicateAccountNames()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Account creation fail: account number already exist or is empty');

        $account = new Account(accountNumber: 'ab021', balance: 0);
        $secondAccount = new Account(accountNumber: 'ab021', balance: 0);
    }

    /**
     * @throws \Exception
     */
    public function testDeposit()
    {
        $account = new Account(accountNumber: 'ab01', balance: 100);
        $account->makeDeposit(50);

        $this->assertEquals(150, $account->getBalance());
    }

    /**
     * @throws \Exception
     */
    public function testWithdrawal()
    {
        $account = new Account(accountNumber: 'ac05', balance: 100);
        $account->makeWithdrawal(30);

        $this->assertEquals(70, $account->getBalance());
    }

    /**
     * @throws \Exception
     */
    public function testWithdrawalWithZeroAmount()
    {
        $account = new Account(accountNumber: 'ac055', balance: 100);
        $this->expectException(\Exception::class);
        $account->makeWithdrawal(0);
    }

    /**
     * @throws \Exception
     */
    public function testWithdrawalWithZeroBalance()
    {
        $this->expectException(\Exception::class);

        $account = new Account(accountNumber: 'ac07', balance: 0);
        $account->makeWithdrawal(30);
    }

    /**
     * @throws \Exception
     */
    public function testDepositWithZeroAmount()
    {
        $this->expectException(\Exception::class);

        $account = new Account(accountNumber: 'ac075', balance: 100);
        $account->makeDeposit(0);
    }

    public function testInsufficientFundsForWithdrawal()
    {
        $this->expectException(\Exception::class);
        $account = new Account(accountNumber: 'qc15', balance: 100);
        $account->makeWithdrawal(200);
    }

    /**
     * @throws \Exception
     */
    public function testTransactions()
    {
        $bankName = 'JOE & THE BANK';
        $bankAddress = 'Joe Street,\\nCopenhagen';
        $bank = new Bank(name: $bankName, address: $bankAddress);

        $firstAccountNumber = 'jc01';
        $firstAccount = new Account(accountNumber: $firstAccountNumber, balance: 100);
        $this->assertEquals($firstAccountNumber, $firstAccount->getAccountNumber());

        $secondAccountNumber = 'jc02';
        $secondAccount = new Account(accountNumber: $secondAccountNumber, balance: 0);
        $this->assertEquals($secondAccountNumber, $secondAccount->getAccountNumber());

        $bank->addBankAccount($firstAccount);
        $bank->addBankAccount($secondAccount);

        $this->assertCount(2, $bank->getBankAccounts());

        $bank->makeTransaction(
            fromAccountNumber: $firstAccount->getAccountNumber(),
            toAccountNumber: $secondAccount->getAccountNumber(),
            amount: 10
        );

        // Assert that we have 90, initial 100 minus 10
        $this->assertEquals(90, $firstAccount->getBalance());
        // Assert that we have 10, initial 0 plus 10
        $this->assertEquals(10, $secondAccount->getBalance());

        $bank->makeTransaction(
            fromAccountNumber: $firstAccount->getAccountNumber(),
            toAccountNumber: $secondAccount->getAccountNumber(),
            amount: 3.55
        );

        // Assert that we have 90, initial 90 minus 3.55
        $this->assertEquals(86.45, $firstAccount->getBalance());

        // Assert that we have 13.55, initial 10 plus 3.55
        $this->assertEquals(13.55, $secondAccount->getBalance());

        $bank->makeTransaction(
            fromAccountNumber: $firstAccount->getAccountNumber(),
            toAccountNumber: $secondAccount->getAccountNumber(),
            amount: 10
        );

        // Assert that we have 76.45, initial 86.45 minus 10
        $this->assertEquals(76.45, $firstAccount->getBalance());

        // Assert that we have 23.55, initial 13.55 plus 10
        $this->assertEquals(23.55, $secondAccount->getBalance());

        $bank->makeTransaction(
            fromAccountNumber: $secondAccount->getAccountNumber(),
            toAccountNumber: $firstAccount->getAccountNumber(),
            amount: 20
        );

        // Assert that we have 96.45, initial 76.45 plus 20
        $this->assertEquals(96.45, $firstAccount->getBalance());

        // Assert that we have 3.55, initial 23.55 minus 20
        $this->assertEquals(3.55, $secondAccount->getBalance());

        $this->assertCount(3, $firstAccount->getWithdrawals());
        $this->assertCount(1, $firstAccount->getDeposits());
        $this->assertCount(4, $firstAccount->getTransactions());

        $this->assertCount(3, $secondAccount->getDeposits());
        $this->assertCount(1, $secondAccount->getWithdrawals());
        $this->assertCount(4, $secondAccount->getTransactions());
    }


    /**
     * @throws \Exception
     */
    public function testBothAccountsBelongsToSameBank()
    {
        $bankName = 'JOE & THE BANK';
        $bankAddress = 'Joe Street,\\nCopenhagen';
        $bank = new Bank(name: $bankName, address: $bankAddress);

        $bankName = 'JOE & THE BANK THE SECOND';
        $bankAddress = 'Joe New Street,\\nCopenhagen';
        $secondBank = new Bank(name: $bankName, address: $bankAddress);

        $firstAccountNumber = 'jc012';
        $firstAccount = new Account(accountNumber: $firstAccountNumber, balance: 100);
        $bank->addBankAccount($firstAccount);
        $this->assertEquals($firstAccountNumber, $firstAccount->getAccountNumber());

        $secondAccountNumber = 'jc021';
        $secondAccount = new Account(accountNumber: $secondAccountNumber, balance: 0);
        $secondBank->addBankAccount($secondAccount);
        $this->assertEquals($secondAccountNumber, $secondAccount->getAccountNumber());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Both accounts must belong to the same bank');

        $bank->makeTransaction(
            fromAccountNumber: $firstAccount->getAccountNumber(),
            toAccountNumber: $secondAccount->getAccountNumber(),
            amount: 10
        );
    }

    /**
     * @throws \Exception
     */
    public function testForAccountBalance()
    {
        $bankName = 'JOE & THE BANK';
        $bankAddress = 'Joe Street,\\nCopenhagen';
        $bank = new Bank(name: $bankName, address: $bankAddress);

        $firstAccountNumber = 'jc0121';
        $firstAccount = new Account(accountNumber: $firstAccountNumber, balance: 0);
        $bank->addBankAccount($firstAccount);
        $this->assertEquals($firstAccountNumber, $firstAccount->getAccountNumber());

        $secondAccountNumber = 'jc0212';
        $secondAccount = new Account(accountNumber: $secondAccountNumber, balance: 0);
        $bank->addBankAccount($secondAccount);
        $this->assertEquals($secondAccountNumber, $secondAccount->getAccountNumber());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds for the transfer');

        $bank->makeTransaction(
            fromAccountNumber: $firstAccount->getAccountNumber(),
            toAccountNumber: $secondAccount->getAccountNumber(),
            amount: 10
        );
    }
}
