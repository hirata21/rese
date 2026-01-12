<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Shop;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owner');
    }

    public function index()
    {
        $ownerId = auth('owner')->id();

        $shop = Shop::where('owner_id', $ownerId)->first();

        if (!$shop) {
            return redirect()
                ->route('owner.dashboard')
                ->with('status', '店舗情報が未登録のため、予約確認は利用できません。');
        }

        $reservations = Reservation::query()
            ->with('user')
            ->where('shop_id', $shop->id)
            ->orderByDesc('reserved_date')
            ->orderByDesc('reserved_time')
            ->paginate(20);

        return view('owner.reservations', compact('shop', 'reservations'));
    }

    public function show(int $reservationId)
    {
        $ownerId = auth('owner')->id();

        $shop = Shop::where('owner_id', $ownerId)->firstOrFail();

        $reservation = Reservation::query()
            ->with('user')
            ->where('shop_id', $shop->id)
            ->findOrFail($reservationId);

        return view('owner.reservations.show', compact('shop', 'reservation'));
    }
}