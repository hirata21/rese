<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'shop_id' => Shop::factory(),
            'course_id' => null,
            'price' => 5000,
            'payment_method' => 'cash',
            'reserved_date' => now()->toDateString(),
            'reserved_time' => '17:00',
            'number_of_people' => 1,
            'status' => 'reserved',
            'payment_status' => 'unpaid',
        ];
    }
}
