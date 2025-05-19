<?php

namespace App\Services\Bill;

use App\DTOs\Bill\PaymentResponseDTO;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayBillService
{

    public function hasUnpaidBill(User $subscriber, int $month, int $year): bool
    {

        return $subscriber->sim_registration->bills()->where('is_paid', 0)->whereYear('bill_date', $year)
            ->whereMonth('bill_date', $month)->exists();
    }

    public function makePayment(User $subscriber, int $month, int $year, int $amount): PaymentResponseDTO
    {
        $billDate = Carbon::createFromDate($year, $month, null);

        return DB::transaction(function () use ($subscriber, $billDate, $amount, $month, $year) {
            $simRegistration = $subscriber->sim_registration;

            $bill = $simRegistration->bills()
                ->whereYear('bill_date', $year)
                ->whereMonth('bill_date', $month)
                ->first();

            if (!$bill) {
                return new PaymentResponseDTO(false, "Bill not found for this period.");
            }

            $billTotal = $bill->details()->sum('amount');

            $totalPaid = Transaction::where('sim_registration_id', $simRegistration->id)
                ->whereDate('transaction_date', $billDate)
                ->sum('transaction_amount');

            $remaining = $billTotal - $totalPaid;

            if ($amount > $remaining) {
                return new PaymentResponseDTO(
                    false,
                    "You are paying too much. Remaining amount is $" . $remaining,
                    $remaining
                );
            }

            // Save transaction
            $transactionService = new TransactionService();
            $success = $transactionService->createTransaction(
                $simRegistration->id,
                now(),
                $amount
            );

            if (!$success) {
                return new PaymentResponseDTO(false, "Transaction failed.");
            }

            // Update bill paid status if fully paid
            if (($totalPaid + $amount) >= $billTotal && !$bill->is_paid) {
                $bill->update(['is_paid' => true]);
            }

            $newRemaining = $billTotal - ($totalPaid + $amount);

            return new PaymentResponseDTO(
                true,
                "Payment successful.",
                $newRemaining > 0 ? $newRemaining : 0
            );
        });
    }
}
