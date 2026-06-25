<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kost;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'kost', 'room']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'))
                  ->orWhereHas('kost', fn($k) => $k->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        if ($request->status) {
            $query->where('booking_status', $request->status);
        }

        if ($request->payment) {
            $query->where('payment_status', $request->payment);
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();
        return view('admin.booking.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'kost.photos', 'room', 'review']);
        return view('admin.booking.show', compact('booking'));
    }

    public function create()
    {
        $users = User::where('role', 'user')->where('status', 'active')->get();
        $kosts = Kost::active()->get();
        return view('admin.booking.create', compact('users', 'kosts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'          => ['required', 'exists:users,id'],
            'kost_id'          => ['required', 'exists:kosts,id'],
            'room_id'          => ['nullable', 'exists:rooms,id'],
            'check_in_date'    => ['required', 'date'],
            'duration_months'  => ['required', 'integer', 'min:1'],
            'total_price'      => ['required', 'numeric', 'min:0'],
            'deposit'          => ['nullable', 'numeric', 'min:0'],
            'payment_method'   => ['nullable', 'string'],
            'notes'            => ['nullable', 'string'],
            'special_requests' => ['nullable', 'string'],
            'booking_status'   => ['required', 'in:pending,confirmed,active,completed,cancelled'],
            'payment_status'   => ['required', 'in:pending,paid,partial,refunded'],
        ]);

        $validated['deposit'] = $validated['deposit'] ?? 0;

        Booking::create($validated);
        return redirect()->route('admin.booking.index')
            ->with('success', 'Booking berhasil ditambahkan!');
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'booking_status' => ['required', 'in:pending,confirmed,active,completed,cancelled'],
            'payment_status' => ['required', 'in:pending,paid,partial,refunded'],
            'notes'          => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'payment_proof'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('payment_proof')) {
            if ($booking->payment_proof) {
                Storage::disk('public')->delete('payments/' . $booking->payment_proof);
            }
            $proofName = time() . '_proof_' . $request->file('payment_proof')->getClientOriginalName();
            $request->file('payment_proof')->storeAs('payments', $proofName, 'public');
            $validated['payment_proof'] = $proofName;
        }

        if ($validated['payment_status'] === 'paid' && !$booking->paid_at) {
            $validated['paid_at'] = now();
        }
        if ($validated['booking_status'] === 'confirmed' && !$booking->confirmed_at) {
            $validated['confirmed_at'] = now();
        }

        $booking->update($validated);
        return back()->with('success', 'Status booking berhasil diperbarui!');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.booking.index')
            ->with('success', 'Booking berhasil dihapus!');
    }

    public function getRooms(Kost $kost)
    {
        $rooms = $kost->rooms()->where('status', 'available')->get();
        return response()->json($rooms);
    }
}
