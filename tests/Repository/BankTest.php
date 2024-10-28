<?php

namespace App\Tests\Repository;

use App\Repository\Bank;
use App\Repository\Account;
use Exception;
use PHPUnit\Framework\TestCase;

class BankTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPostalAddress()
    {
        $bankName = 'JOE & THE BANK';
        $bankAddress = 'Joe Street,\\nCopenhagen';

        $bank = new Bank(name: $bankName, address: $bankAddress);

        $expectedPostalAddress = $bankName . "\n" . $bankAddress;

        $this->assertEquals($expectedPostalAddress, $bank->getPostalAddress());
    }

    public function testEmptyName()
    {
        $bankName = '';
        $bankAddress = 'Joe Street1,\\nCopenhagen';

        $this->expectException(Exception::class);

        new Bank(name: $bankName, address: $bankAddress);
    }

    public function testEmptyAddress()
    {
        $bankName = 'JOE & THE BANK';
        $bankAddress = '';

        $this->expectException(Exception::class);

        new Bank(name: $bankName, address: $bankAddress);
    }

    /**
     * @throws Exception
     */
    public function testAddAndGetBankAccount()
    {
        $bank = new Bank('Test Bank', 'Test Address');
        $account = new Account('12345', 100);
        $bank->addBankAccount($account);

        $this->assertCount(1, $bank->getBankAccounts());
    }

    /**
     * @throws Exception
     */
    public function testAddMultipleBankAccounts()
    {
        $bank = new Bank('Test Bank', 'Test Address');

        $firstAccountNumber = 'ab06';
        $firstAccount = new Account(accountNumber: $firstAccountNumber, balance: 100);

        $secondAccountNumber = 'qj42';
        $secondAccount = new Account(accountNumber: $secondAccountNumber, balance: 0);

        $bank->addBankAccount($firstAccount);
        $bank->addBankAccount($secondAccount);

        $this->assertCount(2, $bank->getBankAccounts());
    }
}
