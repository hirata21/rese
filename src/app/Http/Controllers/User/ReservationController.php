<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Models\Course;
use App\Models\Reservation;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * マイページ：予約一覧
     */
    public function index()
    {
        $user = Auth::user();

        $reservations = Reservation::query()
            ->with(['shop', 'course'])
            ->where('user_id', $user->id)
            ->orderByDesc('reserved_date')
            ->orderByDesc('reserved_time')
            ->get();

        return view('mypage.index', compact('user', 'reservations'));
    }

    /**
     * 予約作成
     */
    public function store(ReservationRequest $request, Shop $shop)
    {
        $data = $request->validated();

        $course = Course::query()
            ->where('shop_id', $shop->id)
            ->where('is_active', true)
            ->findOrFail($data['course_id']);

        $reservation = Reservation::create([
            'user_id'          => Auth::id(),
            'shop_id'          => $shop->id,
            'course_id'        => $course->id,
            'price'            => $course->price,
            'payment_method'   => $course->payment_method,
            'reserved_date'    => $data['date'],
            'reserved_time'    => $data['time'],
            'number_of_people' => $data['number'],
        ]);

        if ($reservation->payment_method === Course::PAYMENT_CARD) {
            return redirect()->route('reservations.checkout', $reservation);
        }

        return redirect()->route('reservations.done');
    }

    /**
     * 予約変更画面
     */
    public function edit(Reservation $reservation)
    {
        $this->ensureOwnReservation($reservation);

        if ($reservation->payment_status === Reservation::PAYMENT_PAID) {
            return redirect()
                ->route('mypage')
                ->with('error', '決済済みの予約は変更できません。');
        }

        $reservation->load(['shop', 'course']);

        $courses = Course::query()
            ->where('shop_id', $reservation->shop_id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return view('reservations.edit', [
            'reservation' => $reservation,
            'shop'        => $reservation->shop,
            'courses'     => $courses,
        ]);
    }

    /**
     * 予約更新
     */
    public function update(ReservationRequest $request, Reservation $reservation)
    {
        $this->ensureOwnReservation($reservation);

        if ($reservation->payment_status === Reservation::PAYMENT_PAID) {
            return redirect()
                ->route('mypage')
                ->with('error', '決済済みの予約は変更できません。');
        }

        if ($reservation->status === Reservation::STATUS_CANCELLED) {
            abort(403);
        }

        $data = $request->validated();
        $beforeMethod = $reservation->payment_method;

        $course = Course::query()
            ->where('shop_id', $reservation->shop_id)
            ->where('is_active', true)
            ->findOrFail($data['course_id']);

        $reservation->update([
            'reserved_date'    => $data['date'],
            'reserved_time'    => $data['time'],
            'number_of_people' => $data['number'],
            'course_id'        => $course->id,
            'price'            => $course->price,
            'payment_method'   => $course->payment_method,
        ]);

        if (
            $beforeMethod !== Course::PAYMENT_CARD &&
            $reservation->payment_method === Course::PAYMENT_CARD
        ) {
            return redirect()->route('reservations.checkout', $reservation);
        }

        return redirect()
            ->route('mypage')
            ->with('status', '予約内容を変更しました。');
    }

    /**
     * 予約キャンセル
     */
    public function destroy(Reservation $reservation)
    {
        $this->ensureOwnReservation($reservation);

        $reservation->delete();

        return redirect()
            ->route('mypage')
            ->with('status', '予約をキャンセルしました。');
    }

    /**
     * 口コミ入力画面
     */
    public function reviewForm(Reservation $reservation)
    {
        $this->ensureOwnReservation($reservation);

        if ($reservation->status === Reservation::STATUS_CANCELLED) {
            abort(403);
        }

        $reservedAt = Carbon::parse($reservation->reserved_date)
            ->setTimeFromTimeString($reservation->reserved_time);

        if ($reservedAt->isFuture()) {
            abort(403);
        }

        $reservation->load('shop');

        return view('reservations.review', compact('reservation'));
    }

    /**
     * 口コミ保存
     */
    public function review(Request $request, Reservation $reservation)
    {
        $this->ensureOwnReservation($reservation);

        if ($reservation->status === Reservation::STATUS_CANCELLED) {
            abort(403);
        }

        $reservedAt = Carbon::parse($reservation->reserved_date)
            ->setTimeFromTimeString($reservation->reserved_time);

        if ($reservedAt->isFuture()) {
            abort(403);
        }

        $data = $request->validate([
            'rating'         => ['required', 'integer', 'between:1,5'],
            'review_comment' => ['nullable', 'string', 'max:500'],
        ]);

        $reservation->update([
            'rating'         => $data['rating'],
            'review_comment' => $data['review_comment'] ?? null,
        ]);

        return back()->with('status', 'レビューを投稿しました。');
    }

    /**
     * 来店用QR表示（generateQrTokenIfEmpty を使用）
     */
    public function qr(Reservation $reservation)
    {
        $this->ensureOwnReservation($reservation);

        if ($reservation->status === Reservation::STATUS_CANCELLED) {
            abort(403);
        }

        // ✅ トークン生成（空なら生成）
        $token = $reservation->generateQrTokenIfEmpty();

        // ✅ DBに保存（重要）
        if ($reservation->isDirty('qr_token')) {
            $reservation->save();
        }

        return view('reservations.qr', [
            'reservation' => $reservation,
            'qrToken'     => $token,
        ]);
    }

    /**
     * QRコード照合（ユーザー側）
     */
    public function verifyQr(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $reservation = Reservation::query()
            ->where('qr_token', $data['token'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$reservation) {
            return response()->json(['result' => 'invalid'], 404);
        }

        if ($reservation->checked_in_at) {
            return response()->json(['result' => 'already_checked_in'], 409);
        }

        $reservation->update([
            'checked_in_at' => now(),
            'status'        => Reservation::STATUS_VISITED,
        ]);

        return response()->json(['result' => 'ok']);
    }

    /**
     * 自分の予約かどうか
     */
    private function ensureOwnReservation(Reservation $reservation): void
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
