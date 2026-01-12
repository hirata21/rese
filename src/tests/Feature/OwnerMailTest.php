<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OwnerMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_mail_routes_are_protected_and_accessible(): void
    {
        Mail::fake();

        // 未ログインは保護（302）
        $this->get(route('owner.mail.create'))->assertStatus(302);

        /** @var \App\Models\User $owner */
        $owner = User::factory()->createOne(['role' => 'owner']);

        $shop = Shop::factory()->createOne([
            'owner_id' => $owner->id,
        ]);

        $user = User::factory()->createOne(['role' => 'user']);

        Reservation::factory()->createOne([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($owner, 'owner');

        $this->get(route('owner.mail.create'))->assertOk();

        $resp = $this->post(route('owner.mail.send'), [
            'subject' => 'お知らせ',
            'body' => 'テスト送信です',
        ]);

        // 成功時は owner.dashboard に redirect
        $resp->assertStatus(302);
    }
}