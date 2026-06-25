<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kost;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['kost', 'room'])->latest()->paginate(10);
        return view('user.booking.index', compact('bookings'));
    }

    public function create(Kost $kost)
    {
        $rooms = $kost->rooms()->where('status', 'available')->get();
        return view('user.booking.create', compact('kost', 'rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kost_id'          => ['required', 'exists:kosts,id'],
            'room_id'          => ['nullable', 'exists:rooms,id'],
            'check_in_date'    => ['required', 'date', 'after_or_equal:today'],
            'duration_months'  => ['required', 'integer', 'min:1'],
            'payment_method'   => ['required', 'string'],
            'special_requests' => ['nullable', 'string'],
            'notes'            => ['nullable', 'string'],
        ]);

        $kost = Kost::findOrFail($validated['kost_id']);
        $total_price = $kost->price_monthly * $validated['duration_months'];

        Booking::create(array_merge($validated, [
            'user_id'      => Auth::id(),
            'total_price'  => $total_price,
            'deposit'      => $kost->price_monthly,
            'booking_status' => 'pending',
            'payment_status' => 'pending',
        ]));

        return redirect()->route('user.booking.index')
            ->with('success', 'Booking berhasil diajukan! Tunggu konfirmasi dari admin.');
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);
        $booking->load(['kost.photos', 'room', 'review']);
        return view('user.booking.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);
        if (!in_array($booking->booking_status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Booking tidak dapat dibatalkan.');
        }
        $booking->update(['booking_status' => 'cancelled']);
        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    public function uploadPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ]);

        if ($booking->payment_proof) {
            Storage::disk('public')->delete('payments/' . $booking->payment_proof);
        }
        $proofName = time() . '_proof.' . $request->file('payment_proof')->extension();
        $request->file('payment_proof')->storeAs('payments', $proofName, 'public');

        $booking->update(['payment_proof' => $proofName, 'payment_status' => 'partial']);
        return back()->with('success', 'Bukti pembayaran berhasil diunggah!');
    }

    public function storeReview(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10'],
        ]);

        Review::create([
            'user_id'    => Auth::id(),
            'kost_id'    => $booking->kost_id,
            'booking_id' => $booking->id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim! Menunggu persetujuan admin.');
    }
}
