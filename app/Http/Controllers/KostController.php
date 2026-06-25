<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KostController extends Controller
{
    public function index(Request $request)
    {
        $query = Kost::with(['primaryPhoto', 'reviews'])->active();

        if ($request->search) $query->search($request->search);
        if ($request->type) $query->where('type', $request->type);
        if ($request->city) $query->where('city', $request->city);
        if ($request->min_price) $query->where('price_monthly', '>=', $request->min_price);
        if ($request->max_price) $query->where('price_monthly', '<=', $request->max_price);
        if ($request->facilities) {
            foreach ($request->facilities as $fac) {
                $query->whereJsonContains('facilities', $fac);
            }
        }

        $sortBy = $request->sort ?? 'latest';
        match ($sortBy) {
            'price_asc'  => $query->orderBy('price_monthly', 'asc'),
            'price_desc' => $query->orderBy('price_monthly', 'desc'),
            'popular'    => $query->withCount('bookings')->orderBy('bookings_count', 'desc'),
            default      => $query->latest(),
        };

        $kosts = $query->paginate(12)->withQueryString();
        $cities = Kost::active()->distinct()->pluck('city');
        $featured = Kost::featured()->active()->with('primaryPhoto')->limit(3)->get();

        return view('kost.index', compact('kosts', 'cities', 'featured'));
    }

    public function show(Kost $kost)
    {
        $kost->load(['photos', 'rooms', 'reviews.user', 'creator']);
        $related = Kost::active()->where('city', $kost->city)
            ->where('id', '!=', $kost->id)->with('primaryPhoto')->limit(3)->get();

        $userReview = null;
        if (Auth::check()) {
            $userReview = Review::where('user_id', Auth::id())
                ->where('kost_id', $kost->id)
                ->first();
        }

        return view('kost.show', compact('kost', 'related', 'userReview'));
    }

    public function storeReview(Request $request, Kost $kost)
    {
        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        // Cegah user mengulas kost yang sama lebih dari sekali
        $alreadyReviewed = Review::where('user_id', Auth::id())
            ->where('kost_id', $kost->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Anda sudah pernah memberikan ulasan untuk kost ini.');
        }

        Review::create([
            'user_id'    => Auth::id(),
            'kost_id'    => $kost->id,
            'booking_id' => null,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'is_approved'=> false,
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim! Menunggu persetujuan admin.');
    }
}
