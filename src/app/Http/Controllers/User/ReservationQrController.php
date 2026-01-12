<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;

class ReservationQrController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Reservation $reservation)
    {
        // ✅ 自分の予約以外は見せない
        abort_unless((int)$reservation->user_id === (int)auth()->id(), 404);

        /**
         * ✅ QRトークンを保証
         */
        $reservation->ensureQrToken();

        // 過去データ救済用：tokenが新規生成された場合だけ保存
        if ($reservation->isDirty('qr_token')) {
            $reservation->save();
        }

        // ✅ オーナー側チェックインURL（QRに埋め込むURL）
        $checkinUrl = route('owner.checkin.token', [
            'token' => $reservation->qr_token,
        ]);

        return view('reservations.qr', [
            'reservation' => $reservation,
            'checkinUrl'  => $checkinUrl,
        ]);
    }
}
