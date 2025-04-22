<?php

namespace App\DTOs\Bill;

use App\Traits\ArrayConvertable;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class QueryBillDetailedDTO
{

    use ArrayConvertable;

    public function __construct(
        public readonly Carbon $date,
        public readonly float $amount,
        public readonly Collection $details
    ) {}

    public function toArray()
    {
        return [
            'date' => $this->date->toString(),
            'dateFormatted' => $this->date->format('F d'),
            'amount' => $this->amount,
            'details' => $this->details
        ];
    }
}
