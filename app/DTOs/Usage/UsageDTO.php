<?php

namespace App\DTOs\Usage;

use App\Enums\FeatureType;
use App\Http\Requests\UsageAddRequest;
use App\Traits\ArrayConvertable;

class UsageDTO
{

    use ArrayConvertable;

    public function __construct(
        public readonly FeatureType $featureType,
        public readonly int $usageAmount,
        public readonly int $month
    ) {}

    public static function createFromRequest(UsageAddRequest $request) : self {
        $usageType = FeatureType::{$request->get('usage_type')};
        return new self($usageType, $request->get('usage_amount'), $request->get('month'));
    }
}
