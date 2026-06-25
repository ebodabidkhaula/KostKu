<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kost;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_kost'     => Kost::count(),
            'total_users'    => User::where('role', 'user')->count(),
            'total_bookings' => Booking::count(),
            'active_bookings'=> Booking::where('booking_status', 'active')->count(),
            'pending_bookings'=> Booking::where('booking_status', 'pending')->count(),
            'total_revenue'  => Booking::where('payment_status', 'paid')->sum('total_price'),
            'total_reviews'  => Review::count(),
            'pending_reviews'=> Review::where('is_approved', false)->count(),
        ];

        $recent_bookings = Booking::with(['user', 'kost'])
            ->latest()->limit(10)->get();

        $popular_kosts = Kost::withCount('bookings')
            ->orderBy('bookings_count', 'desc')->limit(5)->get();

        $monthly_revenue = Booking::selectRaw('EXTRACT(MONTH FROM created_at) as month, EXTRACT(YEAR FROM created_at) as year, SUM(total_price) as total')
            ->where('payment_status', 'paid')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('EXTRACT(MONTH FROM created_at), EXTRACT(YEAR FROM created_at)')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recent_bookings', 'popular_kosts', 'monthly_revenue'
        ));
    }
}
