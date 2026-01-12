<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owner');
    }

    public function index()
    {
        $ownerId = Auth::guard('owner')->id();

        $shop = Shop::where('owner_id', $ownerId)->first();

        return view('owner.dashboard', compact('shop'));
    }
}
