<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class MypageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $reservations = Reservation::query()
            ->with('shop')
            ->where('user_id', $user->id)
            ->orderByDesc('reserved_date')
            ->orderByDesc('reserved_time')
            ->get();

        $favoriteShops = Shop::whereHas('favorites', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        return view('mypage.index', compact('user', 'reservations', 'favoriteShops'));
    }
}