<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardMetrics(): array
    {
        $today = now()->format('Y-m-d');
        $startOfMonth = now()->startOfMonth();

        $kpi = [
            'total_leads'     => Lead::count(),
            'new_enquiries'   => Lead::where('status', 'new')->count(),
            'followups_today' => FollowUp::whereDate('follow_up_date', $today)->where('status', 'pending')->count(),
            'confirmed'       => Booking::where('status', 'confirmed')->count(),
            'revenue'         => (float) Payment::where('paid_at', '>=', $startOfMonth)->sum('amount'),
            'pending_pay'     => (float) Booking::sum('due_amount'),
        ];

        $funnelRows = Lead::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $departures = Booking::with('package:id,name')
            ->where('travel_date', '>=', $today)
            ->where('status', 'confirmed')
            ->orderBy('travel_date', 'asc')
            ->limit(6)
            ->get(['id', 'booking_no', 'travel_date', 'package_id']);

        return [
            'kpis'                => $kpi,
            'funnel'              => $funnelRows,
            'upcoming_departures' => $departures,
        ];
    }
}
