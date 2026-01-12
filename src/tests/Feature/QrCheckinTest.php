<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCheckinTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_page_and_owner_checkin_marks_visited(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-04 10:00:00', 'Asia/Tokyo'));

        /** @var \App\Models\User $user */
        $user = User::factory()->createOne();

        /** @var \App\Models\User $owner */
        $owner = User::factory()->createOne(['role' => 'owner']);

        $shop = Shop::factory()->createOne([
            'owner_id' => $owner->id,
        ]);

        $reservation = Reservation::factory()->createOne([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'reserved_date' => Carbon::now('Asia/Tokyo')->toDateString(),
            'reserved_time' => '17:00',
            'checked_in_at' => null,
            'status' => 'reserved',
        ]);

        $this->actingAs($user)
            ->get(route('reservations.qr', $reservation))
            ->assertOk();

        $this->actingAs($owner, 'owner')
            ->get(route('owner.checkin.token', ['token' => $reservation->qr_token]))
            ->assertStatus(302);

        $reservation->refresh();
        $this->assertSame('visited', $reservation->status);
        $this->assertNotNull($reservation->checked_in_at);
    }
}
