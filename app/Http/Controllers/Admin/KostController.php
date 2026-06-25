<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kost;
use App\Models\KostPhoto;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KostController extends Controller
{
    public function index(Request $request)
    {
        $query = Kost::withCount(['rooms', 'bookings', 'reviews'])
            ->with('photos');

        if ($request->search) {
            $query->search($request->search);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $kosts = $query->latest()->paginate(10)->withQueryString();
        return view('admin.kost.index', compact('kosts'));
    }

    public function create()
    {
        return view('admin.kost.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string'],
            'address'           => ['required', 'string'],
            'city'              => ['required', 'string', 'max:100'],
            'province'          => ['required', 'string', 'max:100'],
            'postal_code'       => ['nullable', 'string', 'max:10'],
            'type'              => ['required', 'in:Putra,Putri,Campur'],
            'price_monthly'     => ['required', 'numeric', 'min:0'],
            'price_yearly'      => ['nullable', 'numeric', 'min:0'],
            'total_rooms'       => ['required', 'integer', 'min:1'],
            'available_rooms'   => ['required', 'integer', 'min:0'],
            'facilities'        => ['nullable', 'array'],
            'shared_facilities' => ['nullable', 'array'],
            'owner_name'        => ['required', 'string', 'max:255'],
            'owner_phone'       => ['required', 'string', 'max:20'],
            'thumbnail'         => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'video_tour'        => ['nullable', 'mimes:mp4,avi,mov', 'max:51200'],
            'status'            => ['required', 'in:active,inactive,full'],
            'is_featured'       => ['boolean'],
            'min_stay'          => ['required', 'integer', 'min:1'],
            'allow_cooking'     => ['boolean'],
            'allow_pets'        => ['boolean'],
            'allow_guest'       => ['boolean'],
            'entry_time'        => ['nullable', 'string'],
            'exit_time'         => ['nullable', 'string'],
            'rules'             => ['nullable', 'array'],
            'photos.*'          => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ]);

        // Handle thumbnail upload ke Supabase
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            // Menyimpan langsung dengan generate nama otomatis dari Laravel ke folder 'thumbnails' di disk 'supabase'
            $thumbnailPath = Storage::disk('supabase')->put('thumbnails', $request->file('thumbnail'));
        }

        // Handle video upload ke Supabase
        $videoPath = null;
        if ($request->hasFile('video_tour')) {
            $videoPath = Storage::disk('supabase')->put('videos', $request->file('video_tour'));
        }

        $kost = Kost::create(array_merge($validated, [
            'thumbnail'  => $thumbnailPath, // Menyimpan path lengkap (cth: thumbnails/xyz.jpg)
            'video_tour' => $videoPath,
            'slug'       => Str::slug($validated['name']) . '-' . Str::random(6),
            'created_by' => Auth::id(),
            'facilities' => $request->facilities ?? [],
            'shared_facilities' => $request->shared_facilities ?? [],
            'rules' => $request->rules ?? [],
            'is_featured'   => $request->boolean('is_featured'),
            'allow_cooking' => $request->boolean('allow_cooking'),
            'allow_pets'    => $request->boolean('allow_pets'),
            'allow_guest'   => $request->boolean('allow_guest'),
        ]));

        // Handle multiple photo uploads ke Supabase
        if ($request->hasFile('photos')) {
            $order = 0;
            foreach ($request->file('photos') as $photo) {
                $photoPath = Storage::disk('supabase')->put('photos', $photo);

                KostPhoto::create([
                    'kost_id'    => $kost->id,
                    'photo_path' => $photoPath, // Menyimpan path lengkap (cth: photos/xyz.jpg)
                    'type'       => 'other',
                    'is_primary' => $order === 0,
                    'order'      => $order,
                ]);
                $order++;
            }
        }

        return redirect()->route('admin.kost.index')
            ->with('success', 'Data kost berhasil ditambahkan!');
    }

    public function show(Kost $kost)
    {
        $kost->load(['photos', 'rooms', 'bookings.user', 'reviews.user']);
        return view('admin.kost.show', compact('kost'));
    }

    public function edit(Kost $kost)
    {
        $kost->load('photos');
        return view('admin.kost.edit', compact('kost'));
    }

    public function update(Request $request, Kost $kost)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string'],
            'address'           => ['required', 'string'],
            'city'              => ['required', 'string', 'max:100'],
            'province'          => ['required', 'string', 'max:100'],
            'postal_code'       => ['nullable', 'string', 'max:10'],
            'type'              => ['required', 'in:Putra,Putri,Campur'],
            'price_monthly'     => ['required', 'numeric', 'min:0'],
            'price_yearly'      => ['nullable', 'numeric', 'min:0'],
            'total_rooms'       => ['required', 'integer', 'min:1'],
            'available_rooms'   => ['required', 'integer', 'min:0'],
            'facilities'        => ['nullable', 'array'],
            'shared_facilities' => ['nullable', 'array'],
            'owner_name'        => ['required', 'string', 'max:255'],
            'owner_phone'       => ['required', 'string', 'max:20'],
            'thumbnail'         => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'video_tour'        => ['nullable', 'mimes:mp4,avi,mov', 'max:51200'],
            'status'            => ['required', 'in:active,inactive,full'],
            'is_featured'       => ['boolean'],
            'min_stay'          => ['required', 'integer', 'min:1'],
            'photos.*'          => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ]);

        if ($request->hasFile('thumbnail')) {
            // Hapus file thumbnail lama dari Supabase jika ada
            if ($kost->thumbnail) {
                Storage::disk('supabase')->delete($kost->thumbnail);
            }
            $thumbnailPath = Storage::disk('supabase')->put('thumbnails', $request->file('thumbnail'));
            $validated['thumbnail'] = $thumbnailPath;
        }

        if ($request->hasFile('video_tour')) {
            // Hapus file video lama dari Supabase jika ada
            if ($kost->video_tour) {
                Storage::disk('supabase')->delete($kost->video_tour);
            }
            $videoPath = Storage::disk('supabase')->put('videos', $request->file('video_tour'));
            $validated['video_tour'] = $videoPath;
        }

        $kost->update(array_merge($validated, [
            'facilities' => $request->facilities ?? [],
            'shared_facilities' => $request->shared_facilities ?? [],
            'is_featured'   => $request->boolean('is_featured'),
            'allow_cooking' => $request->boolean('allow_cooking'),
            'allow_pets'    => $request->boolean('allow_pets'),
            'allow_guest'   => $request->boolean('allow_guest'),
        ]));

        // Handle additional gallery photo uploads ke Supabase
        if ($request->hasFile('photos')) {
            $existingCount = $kost->photos()->count();
            $order = $existingCount;
            foreach ($request->file('photos') as $photo) {
                $photoPath = Storage::disk('supabase')->put('photos', $photo);

                KostPhoto::create([
                    'kost_id'    => $kost->id,
                    'photo_path' => $photoPath,
                    'type'       => 'other',
                    'is_primary' => $existingCount === 0 && $order === 0,
                    'order'      => $order,
                ]);
                $order++;
            }
        }

        return redirect()->route('admin.kost.index')
            ->with('success', 'Data kost berhasil diperbarui!');
    }

    public function destroy(Kost $kost)
    {
        // Hapus thumbnail dari Supabase sebelum menghapus data kost
        if ($kost->thumbnail) {
            Storage::disk('supabase')->delete($kost->thumbnail);
        }

        // Hapus video dari Supabase jika ada
        if ($kost->video_tour) {
            Storage::disk('supabase')->delete($kost->video_tour);
        }

        // Hapus semua foto gallery kost terkait di Supabase
        foreach ($kost->photos as $photo) {
            Storage::disk('supabase')->delete($photo->photo_path);
        }

        $kost->delete();
        return redirect()->route('admin.kost.index')
            ->with('success', 'Data kost berhasil dihapus!');
    }

    public function deletePhoto(KostPhoto $photo)
    {
        // Hapus file foto satuan dari Supabase
        Storage::disk('supabase')->delete($photo->photo_path);
        $photo->delete();
        return response()->json(['success' => true]);
    }
}
