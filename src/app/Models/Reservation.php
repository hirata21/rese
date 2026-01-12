<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    public const STATUS_RESERVED  = 'reserved';
    public const STATUS_VISITED   = 'visited';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID   = 'unpaid';
    public const PAYMENT_PAID     = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    /** @var string[] */
    public const STATUSES = [
        self::STATUS_RESERVED,
        self::STATUS_VISITED,
        self::STATUS_CANCELLED,
    ];

    /** @var string[] */
    public const PAYMENT_STATUSES = [
        self::PAYMENT_UNPAID,
        self::PAYMENT_PAID,
        self::PAYMENT_REFUNDED,
    ];

    protected $fillable = [
        'user_id',
        'shop_id',
        'course_id',
        'price',
        'payment_method',
        'reserved_date',
        'reserved_time',
        'number_of_people',
        'status',
        'qr_token',
        'checked_in_at',
        'payment_status',
        'stripe_session_id',
        'rating',
        'review_comment',
    ];

    protected $casts = [
        'course_id'         => 'integer',
        'price'             => 'integer',
        'reserved_date'     => 'date',
        'reserved_time'     => 'string',
        'number_of_people'  => 'integer',
        'checked_in_at'     => 'datetime',
        'rating'            => 'integer',
        'payment_status'    => 'string',
        'status'            => 'string',
    ];

    protected $attributes = [
        'status'         => self::STATUS_RESERVED,
        'payment_status' => self::PAYMENT_UNPAID,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $reservation) {
            if (!empty($reservation->qr_token)) {
                return;
            }

            $reservation->qr_token = (string) Str::uuid();

        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBetweenDates(Builder $query, Carbon|string $from, Carbon|string $to): Builder
    {
        return $query->whereBetween('reserved_date', [
            Carbon::parse($from)->toDateString(),
            Carbon::parse($to)->toDateString(),
        ]);
    }

    public function scopeForReminderDate(Builder $query, Carbon|string $date): Builder
    {
        $d = Carbon::parse($date)->toDateString();

        return $query
            ->whereDate('reserved_date', $d)
            ->where('status', self::STATUS_RESERVED);
    }

    public function ensureQrToken(): string
    {
        if (!empty($this->qr_token)) {
            return $this->qr_token;
        }

        $this->qr_token = (string) Str::uuid();

        return $this->qr_token;
    }

    public function isReserved(): bool
    {
        return $this->status === self::STATUS_RESERVED;
    }

    public function isVisited(): bool
    {
        return $this->status === self::STATUS_VISITED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function hasReview(): bool
    {
        return $this->rating !== null || $this->review_comment !== null;
    }
}
