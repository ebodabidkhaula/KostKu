<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? 'monthly';
        $year = $request->year ?? now()->year;

        // Revenue by month
        $revenueByMonth = Booking::selectRaw('MONTH(created_at) as month, SUM(total_price) as total, COUNT(*) as count')
            ->where('payment_status', 'paid')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Booking by status
        $bookingByStatus = Booking::selectRaw('booking_status, COUNT(*) as count')
            ->groupBy('booking_status')
            ->get();

        // Top kosts by revenue
        $topKosts = Kost::select('kosts.id', 'kosts.name', 'kosts.city')
            ->join('bookings', 'kosts.id', '=', 'bookings.kost_id')
            ->where('bookings.payment_status', 'paid')
            ->selectRaw('SUM(bookings.total_price) as revenue, COUNT(bookings.id) as total_bookings')
            ->groupBy('kosts.id', 'kosts.name', 'kosts.city')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // User registration trend
        $userRegistrations = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('role', 'user')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Summary
        $summary = [
            'total_revenue'      => Booking::where('payment_status', 'paid')->whereYear('created_at', $year)->sum('total_price'),
            'total_bookings'     => Booking::whereYear('created_at', $year)->count(),
            'avg_booking_value'  => Booking::where('payment_status', 'paid')->whereYear('created_at', $year)->avg('total_price') ?? 0,
            'occupancy_rate'     => $this->getOccupancyRate(),
        ];

        $years = Booking::selectRaw('DISTINCT YEAR(created_at) as year')->orderByDesc('year')->pluck('year');
        if ($years->isEmpty()) $years = collect([now()->year]);

        return view('admin.reports.index', compact(
            'revenueByMonth', 'bookingByStatus', 'topKosts',
            'userRegistrations', 'summary', 'year', 'years'
        ));
    }

    private function getOccupancyRate(): float
    {
        $totalRooms = Kost::sum('total_rooms');
        $availableRooms = Kost::sum('available_rooms');
        if ($totalRooms == 0) return 0;
        return round((($totalRooms - $availableRooms) / $totalRooms) * 100, 1);
    }
}
