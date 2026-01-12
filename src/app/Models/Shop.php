<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'area',
        'genre',
        'owner_id',
        'description',
        'image_path',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'shop_id');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'shop_id', 'user_id')
            ->withTimestamps();
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'shop_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'shop_id');
    }

    public function scopeArea(Builder $query, ?string $area): Builder
    {
        return !empty($area)
            ? $query->where('area', $area)
            : $query;
    }

    public function scopeGenre(Builder $query, ?string $genre): Builder
    {
        return !empty($genre)
            ? $query->where('genre', $genre)
            : $query;
    }

    public function scopeKeyword(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($keyword) {
            $subQuery->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    public function averageRating(): ?float
    {
        $average = $this->reservations()
            ->whereNotNull('rating')
            ->avg('rating');

        return $average !== null
            ? round((float) $average, 1)
            : null;
    }
}