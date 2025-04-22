<?php

namespace App\Services\Bill;

use App\DTOs\Bill\BillDTO;
use App\Models\Bill;
use App\Models\User;

class CreateBillService
{

    public function storeBill(User $subscriber, BillDTO $bill): bool
    {
        $billDb = $subscriber->sim_registration->bills()->whereDate('bill_date', $bill->billDate)->first();

        if (!$billDb)
            $billDb = $subscriber->sim_registration->bills()->create([
                'sim_registration_id' => $subscriber->sim_registration->id,
                'bill_date' => $bill->billDate,
            ]);

        return $this->storeBillDetails($billDb, $bill);
    }

    public function storeBillDetails(Bill $billDb, BillDTO $bill): bool
    {
        $detailsData = $bill->details->map(function ($detail) use ($billDb) {
            return [
                'bill_id' => $billDb->id,
                'usage_id' => $detail->usageId,
                'amount' => $detail->amount,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        $billDb->details()->delete();

        return $billDb->details()->insert($detailsData->toArray());
    }
}
