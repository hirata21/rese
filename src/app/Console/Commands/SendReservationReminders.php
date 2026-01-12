<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReservationReminders extends Command
{
    protected $signature = 'reminders:reservations-today {--dry-run : Actually send nothing, just show targets}';
    protected $description = 'Send reminder emails to users who have a reservation today (morning reminder).';

    public function handle(): int
    {
        // Asia/Tokyo で統一（config/app.php の timezone が Asia/Tokyo なら不要）
        $today = Carbon::now('Asia/Tokyo')->toDateString();

        // 今日の予約（例：予約中のみ。キャンセルは除外）
        $reservations = Reservation::query()
            ->with(['user:id,name,email', 'shop:id,name'])
            ->whereDate('reserved_date', $today)
            ->where('status', 'reserved') // ← 必要なら条件変えてOK
            ->get();

        if ($reservations->isEmpty()) {
            $this->info("No reservations for today ({$today}).");
            return self::SUCCESS;
        }

        $dryRun = (bool)$this->option('dry-run');

        $sent = 0;

        foreach ($reservations as $r) {
            $email = $r->user?->email;
            if (!$email) continue;

            $shopName = $r->shop?->name ?? '店舗';
            $userName = $r->user?->name ?? 'お客様';
            $time     = Carbon::parse($r->reserved_time)->format('H:i');
            $date     = Carbon::parse($r->reserved_date)->format('Y/m/d');

            $subject = "【リマインダー】本日 {$date} {$time} のご予約（{$shopName}）";
            $body = "{$userName} 様\n\n"
                . "本日はご予約日です。\n\n"
                . "■ 店舗：{$shopName}\n"
                . "■ 日時：{$date} {$time}\n"
                . "■ 人数：{$r->number_of_people}名\n\n"
                . "ご来店をお待ちしております。\n";

            if ($dryRun) {
                $this->line("[DRY] to={$email} reservation_id={$r->id} shop={$shopName} {$date} {$time}");
                continue;
            }

            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });

            $sent++;
        }

        $this->info($dryRun ? "Dry-run done. targets={$reservations->count()}" : "Sent reminders={$sent} / targets={$reservations->count()}");
        return self::SUCCESS;
    }
}
