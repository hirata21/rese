<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_after_visited(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->createOne();
        $shop = Shop::factory()->createOne();

        $reservation = Reservation::factory()->createOne([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'status' => 'visited',
            'checked_in_at' => Carbon::now('Asia/Tokyo'),
        ]);

        $this->actingAs($user);

        $resp = $this->get(route('reservations.review.form', $reservation));
        $this->assertTrue(
            in_array($resp->getStatusCode(), [200, 302], true),
            'Expected 200 or 302, got ' . $resp->getStatusCode()
        );

        $post = $this->post(route('reservations.review', $reservation), [
            'rating' => 5,
            'review_comment' => '最高でした',
        ]);

        $post->assertStatus(302);
        $post->assertSessionHasNoErrors();

        $reservation->refresh();
        $this->assertSame(5, $reservation->rating);
    }
}
