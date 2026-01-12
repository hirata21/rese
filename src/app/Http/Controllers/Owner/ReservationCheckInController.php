<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CheckInRequest;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReservationCheckInController extends Controller
{
    private const TIMEZONE = 'Asia/Tokyo';

    public function __construct()
    {
        $this->middleware('auth:owner');
    }

    public function form()
    {
        return view('owner.checkin_form');
    }

    public function checkin(CheckInRequest $request): RedirectResponse
    {
        $token = $request->validated()['token'];

        return $this->doCheckin($token);
    }

    public function checkinByToken(string $token): RedirectResponse
    {
        return $this->doCheckin($token);
    }

    private function doCheckin(string $token): RedirectResponse
    {
        $owner = Auth::guard('owner')->user();

        // ✅ 予約取得（トークン一致）
        $reservation = Reservation::with('shop')
            ->where('qr_token', $token)
            ->first();

        if (!$reservation) {
            return redirect()
                ->route('owner.checkin.form')
                ->with('status', 'QRが無効です（予約が見つかりません）。');
        }

        // ✅ セキュリティ：オーナーの店舗の予約だけ許可
        if (!$reservation->shop || (int)$reservation->shop->owner_id !== (int)$owner->id) {
            return redirect()
                ->route('owner.checkin.form')
                ->with('status', 'この予約をチェックインする権限がありません。');
        }

        $now = Carbon::now(self::TIMEZONE);

        $reservedDate = $reservation->reserved_date instanceof Carbon
            ? $reservation->reserved_date->copy()->timezone(self::TIMEZONE)
            : Carbon::parse($reservation->reserved_date, self::TIMEZONE);

        // ✅ 当日予約のみチェックイン可能
        if (!$reservedDate->isSameDay($now)) {
            return redirect()
                ->route('owner.checkin.form')
                ->with('status', '本日の予約ではありません。');
        }

        // ✅ 二重チェックイン防止
        if (!empty($reservation->checked_in_at)) {
            return redirect()
                ->route('owner.checkin.form')
                ->with('status', 'すでにチェックイン済みです。');
        }

        $reservation->forceFill([
            'checked_in_at' => $now,
            'status'        => defined(Reservation::class . '::STATUS_VISITED')
                ? Reservation::STATUS_VISITED
                : 'visited',
        ])->save();

        return redirect()
            ->route('owner.checkin.form')
            ->with('status', "チェックイン完了（予約ID: {$reservation->id}）");
    }
}
