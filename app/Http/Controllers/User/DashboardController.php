<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->with('kost')->latest()->limit(5)->get();
        $active_booking = Booking::where('user_id', $user->id)
            ->where('booking_status', 'active')->with('kost.photos')->first();
        $total_spent = Booking::where('user_id', $user->id)
            ->where('payment_status', 'paid')->sum('total_price');

        return view('user.dashboard', compact('bookings', 'active_booking', 'total_spent'));
    }
}
