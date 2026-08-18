<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LeadSource;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getLeadsBySource(): array
    {
        return LeadSource::leftJoin('leads', 'leads.source_id', '=', 'lead_sources.id')
            ->select('lead_sources.name', DB::raw('COUNT(leads.id) as total'))
            ->groupBy('lead_sources.id', 'lead_sources.name')
            ->get()
            ->toArray();
    }

    public function getSalesByStaff(): array
    {
        return User::leftJoin('bookings', 'bookings.created_by', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(bookings.id) as bookings_count'), DB::raw('COALESCE(SUM(bookings.total_amount), 0) as revenue'))
            ->groupBy('users.id', 'users.name')
            ->get()
            ->toArray();
    }

    public function getMonthlyRevenue(): array
    {
        return Payment::select(DB::raw("DATE_FORMAT(paid_at, '%Y-%m') as ym"), DB::raw('SUM(amount) as total'))
            ->groupBy('ym')
            ->orderBy('ym', 'desc')
            ->limit(12)
            ->get()
            ->toArray();
    }
}
