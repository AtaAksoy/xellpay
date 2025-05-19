<?php

namespace App\Services\Bill;

use App\DTOs\Bill\BillDetailDTO;
use App\DTOs\Bill\BillDTO;
use App\DTOs\Bill\QueryBillDetailedDTO;
use App\DTOs\Bill\QueryBillDTO;
use App\DTOs\Usage\UsageDTO;
use App\Models\User;
use App\Services\Subscriber\SubscriberService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QueryBillService
{

    public function checkHasBill(User $subscriber, int $month, int $year): bool
    {
        $query = $subscriber->sim_registration->bills()
            ->whereYear('bill_date', $year)
            ->whereMonth('bill_date', $month);

        return $query->exists();
    }


    public function queryBill(User $subscriber, int $month, int $year): ?QueryBillDTO
    {
        // check subscriber
        $bill = $subscriber->sim_registration->bills()->whereYear('bill_date', $year)
            ->whereMonth('bill_date', $month)->first();
        if (!$bill)
            return null;

        return new QueryBillDTO(Carbon::createFromDate($year, $month, null), $bill->details()->sum('amount'), $bill->is_paid);
    }

    public function queryBillDetailed(User $subscriber, int $month, int $year, int $page = 1): ?QueryBillDetailedDTO
    {
        $bill = $subscriber->sim_registration->bills()->whereYear('bill_date', $year)
            ->whereMonth('bill_date', $month)->first();
        if (!$bill)
            return null;

        $dto = new QueryBillDetailedDTO(Carbon::createFromDate($year, $month, null), $bill->details()->sum('amount'), $bill->details()->paginate(5)->map(fn($d) => new BillDetailDTO($d->usage_id, UsageDTO::createFromModel($d->usage), $d->amount)));

        return $dto;
    }
}
