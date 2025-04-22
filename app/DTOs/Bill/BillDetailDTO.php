<?php

namespace App\DTOs\Bill;

use App\DTOs\Usage\UsageDTO;
use App\Traits\ArrayConvertable;

class BillDetailDTO
{

    use ArrayConvertable;

    public function __construct(
        public readonly ?int $usageId,
        public readonly UsageDTO $usage,
        public readonly float $amount
    ) {}
}
