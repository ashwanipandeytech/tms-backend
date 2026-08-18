<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class PaymentService extends BaseService
{
    public function __construct(PaymentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): EloquentModel
    {
        $payment = parent::create($data);

        // Synchronize Booking paid_amount and due_amount
        if (!empty($payment->booking_id)) {
            $booking = Booking::find($payment->booking_id);
            if ($booking) {
                $totalPaid = $booking->payments()->sum('amount');
                $dueAmount = max(0, (float)$booking->total_amount - (float)$totalPaid);
                $booking->update([
                    'paid_amount' => $totalPaid,
                    'due_amount'  => $dueAmount,
                ]);
            }
        }

        return $payment;
    }
}
