<?php

namespace App\DTOs\Bill;

use App\Traits\ArrayConvertable;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BillDTO
{

    use ArrayConvertable;

    public function __construct(
        public readonly Carbon $billDate,
        public readonly Collection $details
    ) {}


    public function addDetail(BillDetailDTO $detail) {
        $this->details->add($detail);
    }
}
