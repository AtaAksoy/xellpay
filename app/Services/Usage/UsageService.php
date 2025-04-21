<?php

namespace App\Services\Usage;

use App\DTOs\Usage\UsageDTO;
use Carbon\Carbon;

class UsageService {


    public function addUsage(UsageDTO $usage) : bool {
        $addUsageOperation = request()->user()->sim_registration->usages()->create([
            'usage_date' => Carbon::now()->setMonth($usage->month),
            'feature_type' => $usage->featureType->value,
            'feature_amount' => $usage->usageAmount,
        ]);

        return boolval($addUsageOperation);
    }

}