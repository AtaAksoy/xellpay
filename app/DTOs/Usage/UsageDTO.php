<?php

namespace App\DTOs\Usage;

use App\Enums\FeatureType;
use App\Http\Requests\UsageAddRequest;
use App\Models\Usage;
use App\Traits\ArrayConvertable;
use Carbon\Carbon;

class UsageDTO
{

    use ArrayConvertable;

    public function __construct(
        public readonly FeatureType $featureType,
        public readonly int $usageAmount,
        public readonly int $month,
        public readonly ?int $year
    ) {}

    public static function createFromRequest(UsageAddRequest $request) : self {
        $usageType = FeatureType::{$request->get('usage_type')};
        return new self($usageType, $request->get('usage_amount'), $request->get('month'), null);
    }

    public static function createFromModel(Usage $model) : self {
        $formatDate = Carbon::parse($model->usage_date);
        return new self($model->feature_type, $model->feature_amount, $formatDate->month, $formatDate->year);
    }
}
