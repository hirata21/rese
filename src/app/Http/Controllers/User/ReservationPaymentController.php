<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Stripe;

class ReservationPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Stripe Checkout 作成（カード決済）
     */
    public function checkout(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        if ($reservation->payment_status === Reservation::PAYMENT_PAID) {
            return redirect()
                ->route('mypage')
                ->with('status', 'この予約は既に支払い済みです。');
        }

        if (empty($reservation->course_id)) {
            return redirect()
                ->route('mypage')
                ->with('error', 'コースが選択されていないため、決済できません。');
        }

        $reservation->loadMissing('shop');

        $course = Course::query()
            ->where('shop_id', $reservation->shop_id)
            ->where('is_active', true)
            ->findOrFail($reservation->course_id);

        // 予約に保存されている金額を優先（予約時点の金額を守る）
        $amountYen = (int) ($reservation->price ?: $course->price);

        if ($amountYen <= 0) {
            return redirect()
                ->route('mypage')
                ->with('error', 'コース金額が不正のため決済できません。');
        }

        if ((int) $reservation->price !== $amountYen) {
            $reservation->update(['price' => $amountYen]);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => auth()->user()->email,

            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $amountYen,
                    'product_data' => [
                        'name' => "予約：{$reservation->shop->name}",
                        'description' =>
                        "{$reservation->reserved_date} {$reservation->reserved_time} / "
                            . "{$reservation->number_of_people}名 / "
                            . "コース：{$course->name}",
                    ],
                ],
            ]],

            'success_url' => route('reservations.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('reservations.cancel'),

            'metadata' => [
                'reservation_id' => (string) $reservation->id,
                'course_id'      => (string) $course->id,
                'amount_yen'     => (string) $amountYen,
                'user_id'        => (string) auth()->id(),
            ],
        ]);

        $reservation->update([
            'stripe_session_id' => $session->id,
        ]);

        return redirect($session->url);
    }

    /**
     * 決済成功
     */
    public function success(Request $request)
    {
        $data = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = CheckoutSession::retrieve($data['session_id']);

        if (($session->payment_status ?? null) !== 'paid') {
            return redirect()
                ->route('shops.index')
                ->with('error', '決済が完了していません。');
        }

        $reservationId = $session->metadata->reservation_id ?? null;
        if (!$reservationId) {
            return redirect()
                ->route('shops.index')
                ->with('error', '予約情報を取得できませんでした。');
        }

        $reservation = Reservation::findOrFail((int) $reservationId);

        // 🔒 自分の予約のみ反映
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        if (!empty($reservation->stripe_session_id) && $reservation->stripe_session_id !== $session->id) {
            return redirect()
                ->route('shops.index')
                ->with('error', '不正な決済セッションです。');
        }

        if ($reservation->payment_status === Reservation::PAYMENT_PAID) {
            return redirect()
                ->route('mypage')
                ->with('status', '既に決済済みの予約です。');
        }

        $reservation->update([
            'payment_status' => Reservation::PAYMENT_PAID,
        ]);

        return redirect()
            ->route('mypage')
            ->with('status', '決済が完了しました。');
    }

    /**
     * 決済キャンセル
     */
    public function cancel()
    {
        return redirect()
            ->route('mypage')
            ->with('error', '決済をキャンセルしました。');
    }
}