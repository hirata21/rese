<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_route_is_accessible(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('reservations.checkout', $reservation))
            ->assertStatus(302);
    }
}
