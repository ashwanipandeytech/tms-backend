<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Events\BookingConfirmedEvent;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class BookingService extends BaseService
{
    public function __construct(BookingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): EloquentModel
    {
        if (empty($data['booking_no'])) {
            $data['booking_no'] = 'BK-' . strtoupper(uniqid());
        }

        $totalAmount = (float)($data['total_amount'] ?? 0);
        $paidAmount  = (float)($data['paid_amount'] ?? 0);
        $data['due_amount'] = max(0, $totalAmount - $paidAmount);

        if (auth()->check()) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
        }

        $booking = parent::create($data);

        if ($booking->status === BookingStatus::CONFIRMED || $booking->status === 'confirmed') {
            event(new BookingConfirmedEvent($booking));
        }

        return $booking;
    }

    public function update(int|string $id, array $data): EloquentModel
    {
        $booking = parent::update($id, $data);

        if ($booking->status === BookingStatus::CONFIRMED || $booking->status === 'confirmed') {
            event(new BookingConfirmedEvent($booking));
        }

        return $booking;
    }
}
