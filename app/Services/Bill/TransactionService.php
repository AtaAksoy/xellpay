<?php

namespace App\Services\Bill;

use App\Models\Transaction;
use Carbon\Carbon;

class TransactionService
{

    public function createTransaction(int $simRegistrationId, Carbon $transactionDate, int $transactionAmount): bool
    {
        if (Transaction::create([
            'sim_registration_id' => $simRegistrationId,
            'transaction_date' => $transactionDate,
            'transaction_amount' => $transactionAmount
        ]))
            return true;
        else
            return false;
    }
}
