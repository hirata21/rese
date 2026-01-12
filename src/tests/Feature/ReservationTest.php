<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Reservation;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_create_update_delete_and_list(): void
    {
        $this->seed();

        /** @var \App\Models\User $user */
        $user = User::factory()->createOne();

        $shop = Shop::query()->firstOrFail();

        $course = Course::query()
            ->where('shop_id', $shop->id)
            ->firstOrFail();

        $this->actingAs($user);

        $res = $this->post(route('reservations.store', $shop), [
            'course_id'       => $course->id,
            'date'            => now()->addDay()->toDateString(),
            'time'            => '17:00',
            'number'          => 2,
            'payment_method'  => 'cash',
        ]);

        $res->assertStatus(302);
        $res->assertSessionHasNoErrors();

        $reservation = Reservation::where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($reservation);

        $this->get(route('reservations.index'))->assertOk();
        $this->get(route('mypage'))->assertOk();
    }
}
