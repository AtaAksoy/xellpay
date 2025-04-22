<?php

namespace App\DTOs\Bill;

use App\Traits\ArrayConvertable;
use Carbon\Carbon;

class QueryBillDTO
{

    use ArrayConvertable;

    public function __construct(
        public readonly Carbon $date,
        public readonly float $amount,
        public readonly bool $paid,
    ) {}

    public function toArray()
    {
        return [
            'date' => $this->date->toString(),
            'dateFormatted' => $this->date->format('F d'),
            'amount' => $this->amount,
            'paid' => $this->paid
        ];
    }
}
