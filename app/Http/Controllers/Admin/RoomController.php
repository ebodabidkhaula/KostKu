<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kost;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store(Request $request, Kost $kost)
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:20'],
            'floor'       => ['required', 'integer', 'min:1'],
            'size'        => ['nullable', 'numeric', 'min:0'],
            'price'       => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:available,occupied,maintenance'],
            'description' => ['nullable', 'string'],
            'facilities'  => ['nullable', 'array'],
        ]);

        $validated['facilities'] = $request->facilities ?? [];

        $kost->rooms()->create($validated);

        $this->syncAvailableRooms($kost);

        return back()->with('success', 'Kamar berhasil ditambahkan!');
    }

    public function update(Request $request, Kost $kost, Room $room)
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:20'],
            'floor'       => ['required', 'integer', 'min:1'],
            'size'        => ['nullable', 'numeric', 'min:0'],
            'price'       => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:available,occupied,maintenance'],
            'description' => ['nullable', 'string'],
            'facilities'  => ['nullable', 'array'],
        ]);

        $validated['facilities'] = $request->facilities ?? [];

        $room->update($validated);

        $this->syncAvailableRooms($kost);

        return back()->with('success', 'Kamar berhasil diperbarui!');
    }

    public function destroy(Kost $kost, Room $room)
    {
        $room->delete();

        $this->syncAvailableRooms($kost);

        return back()->with('success', 'Kamar berhasil dihapus!');
    }

    /**
     * Sinkronkan jumlah kamar tersedia & total kamar berdasarkan data kamar aktual.
     */
    private function syncAvailableRooms(Kost $kost): void
    {
        $total = $kost->rooms()->count();
        $available = $kost->rooms()->where('status', 'available')->count();

        $kost->update([
            'total_rooms'     => max($total, $kost->total_rooms),
            'available_rooms' => $available,
        ]);
    }
}
