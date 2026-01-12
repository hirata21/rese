<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Owner\SendMailRequest;

class OwnerMailController extends Controller
{
    /**
     * 作成画面
     */
    public function create()
    {
        $ownerId = auth('owner')->id() ?? auth()->id();

        $shop = Shop::where('owner_id', $ownerId)->first();

        if (!$shop) {
            return redirect()
                ->route('owner.dashboard')
                ->with('status', '先に店舗情報を登録してください。');
        }

        return view('owner.mail', compact('shop'));
    }

    /**
     * 送信（予約者限定）
     */
    public function send(SendMailRequest $request)
    {
        $ownerId = auth('owner')->id() ?? auth()->id();

        $shop = Shop::where('owner_id', $ownerId)->firstOrFail();

        $data = $request->validated();

        $emails = Reservation::query()
            ->where('shop_id', $shop->id)
            ->whereNotNull('user_id')
            ->join('users', 'reservations.user_id', '=', 'users.id')
            ->whereNotNull('users.email')
            ->distinct()
            ->pluck('users.email');

        if ($emails->isEmpty()) {
            return back()->with('status', '予約者がいないため、メールを送信できませんでした。');
        }

        $subject = $data['subject'];
        $body    = $data['body'];

        $rawBody = "【{$shop->name}】\n\n" . $body;

        foreach ($emails as $email) {
            Mail::raw($rawBody, function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject);
            });
        }

        return redirect()
            ->route('owner.dashboard')
            ->with('status', '予約者へお知らせメールを送信しました。');
    }
}