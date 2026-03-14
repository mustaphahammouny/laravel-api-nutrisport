<?php

namespace App\Enums;

use App\Enums\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case BankTransfer = 'bank_transfer';

    public function label(): string
    {
        return match ($this) {
            self::BankTransfer => 'Bank Transfer',
        };
    }
}
