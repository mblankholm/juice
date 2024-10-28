<?php

namespace App\Validator;

class AccountNumberValidator
{
    private static array $accountNumbers = [];

    // Store the account number after creation
    public static function addAccountNumber(string $accountNumber):void
    {
        self::$accountNumbers[] = $accountNumber;
    }

    // Check if the account number is unique and not empty
    public static function isUnique(string $accountNumber):bool
    {
        return !in_array($accountNumber, self::$accountNumbers) && !empty($accountNumber);
    }
}