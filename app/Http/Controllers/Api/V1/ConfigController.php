<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use Illuminate\Http\JsonResponse;

class ConfigController extends BaseApiController
{
    /**
     * Get all system status configurations grouped section-wise for frontend initialization.
     */
    public function statuses(): JsonResponse
    {
        $statuses = [
            'leads' => [
                ['key' => 'NEW_LEAD', 'label' => 'New Lead'],
                ['key' => 'ATTEMPTED_CONTACT', 'label' => 'Attempted Contact'],
                ['key' => 'CONNECTED', 'label' => 'Connected'],
                ['key' => 'FOLLOW_UP', 'label' => 'Follow Up'],
                ['key' => 'INTERESTED', 'label' => 'Interested'],
                ['key' => 'QUOTATION_SENT', 'label' => 'Quotation Sent'],
                ['key' => 'NEGOTIATION', 'label' => 'Negotiation'],
                ['key' => 'BOOKING_CONFIRMED', 'label' => 'Booking Confirmed'],
                ['key' => 'TOUR_COMPLETED', 'label' => 'Tour Completed'],
                ['key' => 'NOT_INTERESTED', 'label' => 'Not Interested'],
                ['key' => 'LOST_LEAD', 'label' => 'Lost Lead'],
                ['key' => 'CANCELLED', 'label' => 'Cancelled'],
            ],
            'bookings' => [
                ['key' => 'pending', 'label' => 'Pending'],
                ['key' => 'confirmed', 'label' => 'Confirmed'],
                ['key' => 'completed', 'label' => 'Completed'],
                ['key' => 'cancelled', 'label' => 'Cancelled'],
            ],
            'invoices' => [
                ['key' => 'unpaid', 'label' => 'Unpaid'],
                ['key' => 'partial', 'label' => 'Partially Paid'],
                ['key' => 'paid', 'label' => 'Paid'],
            ],
            'followups' => [
                ['key' => 'pending', 'label' => 'Pending'],
                ['key' => 'done', 'label' => 'Done'],
                ['key' => 'missed', 'label' => 'Missed'],
            ],
            'vehicles' => [
                ['key' => 'available', 'label' => 'Available'],
                ['key' => 'booked', 'label' => 'Booked'],
                ['key' => 'maintenance', 'label' => 'Maintenance'],
            ],
            'quotations' => [
                ['key' => 'draft', 'label' => 'Draft'],
                ['key' => 'sent', 'label' => 'Sent'],
                ['key' => 'accepted', 'label' => 'Accepted'],
                ['key' => 'rejected', 'label' => 'Rejected'],
            ],
            'customers' => [
                ['key' => 'active', 'label' => 'Active'],
                ['key' => 'inactive', 'label' => 'Inactive'],
            ],
        ];

        return $this->successResponse($statuses, 'System status configurations retrieved successfully');
    }
}
