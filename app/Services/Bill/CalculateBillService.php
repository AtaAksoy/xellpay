<?php

namespace App\Services\Bill;

use App\DTOs\Bill\BillDetailDTO;
use App\DTOs\Bill\BillDTO;
use App\DTOs\Usage\UsageDTO;
use App\Enums\FeatureType;
use App\Models\Usage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalculateBillService
{

    public int $totalCalls = 0;
    public int $totalMbs = 0;
    public float $totalBillAmount = 0;
    public bool $internetBaseCharged = false;


    public BillDTO $bill;

    public function calculateBill(User $subscriber, int $month, int $year)
    {
        $formatDate = Carbon::createFromDate($year, $month, null);
        $this->bill = new BillDTO($formatDate, new Collection());

        $usages = $this->getUsages($subscriber, $month, $year);

        $usages->map(fn($u) => $this->calculateUsage($u));

        $createBillService = new CreateBillService();

        $billCreateStatus = $createBillService->storeBill($subscriber, $this->bill);

        if ($billCreateStatus)
            return $this->bill;
        else
            return false;
    }

    public function getUsages(User $subscriber, int $month, int $year): Collection
    {
        $usages = $subscriber->sim_registration->usages()
            ->whereYear('usage_date', $year)
            ->whereMonth('usage_date', $month)->get();

        return $usages;
    }



    public function calculateUsage(Usage $usage)
    {
        switch ($usage->feature_type) {
            case FeatureType::CALL:
                $this->totalCalls += $usage->feature_amount;

                $freeLimit = 1000;
                $extraMinutes = max(0, $this->totalCalls - $freeLimit);
                $chargeableMinutes = min($usage->feature_amount, $extraMinutes);

                $amount = ($chargeableMinutes / 1000) * 10;

                $billDetail = new BillDetailDTO(
                    $usage->id,
                    UsageDTO::createFromModel($usage),
                    round($amount, 2)
                );
                $this->bill->addDetail($billDetail);
                break;
            case FeatureType::INTERNET:
                $previousTotalMbs = $this->totalMbs;
                $this->totalMbs += $usage->feature_amount;

                $freeLimitMb = 20 * 1024; // 20480
                $overageChunkMb = 10 * 1024; // 10240

                $amount = 0;

                if (!$this->internetBaseCharged && $this->totalMbs >= $freeLimitMb) {
                    $this->internetBaseCharged = true;
                    $amount += 50;
                }

                $prevOverage = max(0, $previousTotalMbs - $freeLimitMb);
                $currentOverage = max(0, $this->totalMbs - $freeLimitMb);
                $newOverage = $currentOverage - $prevOverage;

                if ($newOverage > 0) {
                    $chunks = ceil($newOverage / $overageChunkMb);
                    $amount += $chunks * 10;
                }

                $billDetail = new BillDetailDTO(
                    $usage->id,
                    UsageDTO::createFromModel($usage),
                    round($amount, 2)
                );

                $this->bill->addDetail($billDetail);
                break;
            default:
                break;
        }
    }
}
