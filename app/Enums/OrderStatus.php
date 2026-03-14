<?php

namespace App\Enums;

use App\Enums\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::PartiallyPaid => 'Partially paid',
            self::Paid => 'Paid',
            self::Cancelled => 'Cancelled',
        };
    }
}
