<?php

namespace App\Services\Usage;

use App\DTOs\Usage\UsageDTO;
use App\Models\User;
use Carbon\Carbon;

class UsageService {


    public function addUsage(UsageDTO $usage, User $subsriber) : bool {
        $addUsageOperation = $subsriber->sim_registration->usages()->create([
            'usage_date' => Carbon::now()->setMonth($usage->month),
            'feature_type' => $usage->featureType->value,
            'feature_amount' => $usage->usageAmount,
        ]);

        return boolval($addUsageOperation);
    }

}