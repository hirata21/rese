@extends('layouts.app')

@section('title', 'マイページ')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/index.css') }}">
@endsection

@section('content')
<div class="mypage-container">

    {{-- ページ主見出し（見た目は不要なら隠す） --}}
    <h1 class="sr-only">マイページ</h1>

    {{-- 左：予約状況 --}}
    <section class="mypage-left" aria-labelledby="reservation-heading">
        <h2 id="reservation-heading" class="section-title">予約状況</h2>

        {{-- フラッシュ（成功/エラー） --}}
        @if (session('error'))
        <div class="flash flash-error" role="alert">
            {{ session('error') }}
        </div>
        @endif

        @if (session('status'))
        <div class="flash flash-success" role="status">
            {{ session('status') }}
        </div>
        @endif

        {{-- タブ（予約 / 来店済み） --}}
        <div class="reservation-tabs" role="tablist" aria-label="予約タブ">
            <button
                type="button"
                id="tab-btn-reserved"
                class="tab-btn is-active"
                data-tab="tab-reserved"
                role="tab"
                aria-controls="tab-reserved"
                aria-selected="true"
                tabindex="0">
                予約
            </button>

            <button
                type="button"
                id="tab-btn-visited"
                class="tab-btn"
                data-tab="tab-visited"
                role="tab"
                aria-controls="tab-visited"
                aria-selected="false"
                tabindex="-1">
                来店済み
            </button>
        </div>

        @php
        $makeReservedAt = function ($r) {
        // reserved_date が Carbon キャスト前提
        return $r->reserved_date->copy()->setTimeFromTimeString($r->reserved_time);
        };

        $reservedReservations = $reservations->filter(function ($r) use ($makeReservedAt) {
        if ($r->status === \App\Models\Reservation::STATUS_CANCELLED) return false;
        return $makeReservedAt($r)->isFuture();
        });

        $visitedReservations = $reservations->filter(function ($r) use ($makeReservedAt) {
        if ($r->status === \App\Models\Reservation::STATUS_CANCELLED) return false;
        return $makeReservedAt($r)->isPast();
        });
        $favoriteShops = $favoriteShops ?? collect();
        @endphp

        {{-- 予約タブ --}}
        <div
            id="tab-reserved"
            class="tab-panel is-active"
            role="tabpanel"
            aria-labelledby="tab-btn-reserved">
            @forelse($reservedReservations as $index => $reservation)
            <div class="reservation-card">

                <div class="reservation-card-header">
                    <div class="reservation-title">

                        {{-- 時計アイコン（CSS描画） --}}
                        <div class="reservation-icon" aria-hidden="true">
                            <div class="clock-face">
                                <div class="hand hour"></div>
                                <div class="hand minute"></div>
                                <div class="tick t1"></div>
                                <div class="tick t2"></div>
                                <div class="tick t3"></div>
                                <div class="tick t4"></div>
                                <div class="tick t5"></div>
                                <div class="tick t6"></div>
                                <div class="tick t7"></div>
                                <div class="tick t8"></div>
                                <div class="tick t9"></div>
                                <div class="tick t10"></div>
                                <div class="tick t11"></div>
                                <div class="tick t12"></div>
                            </div>
                        </div>

                        <span>予約{{ $index + 1 }}</span>
                    </div>

                    {{-- 右上：変更 + QR + キャンセル --}}
                    <div class="reservation-header-actions">
                        <a href="{{ route('reservations.edit', $reservation) }}" class="reservation-edit-btn">
                            変更
                        </a>

                        <a href="{{ route('reservations.qr', $reservation) }}" class="reservation-edit-btn">
                            来店用QR
                        </a>

                        <form
                            method="POST"
                            action="{{ route('reservations.destroy', $reservation) }}"
                            class="reservation-delete-form"
                            onsubmit="return confirm('本当にこの予約をキャンセルしますか？');">
                            @csrf
                            @method('DELETE')
                            <button class="close-reservation" type="submit" aria-label="予約をキャンセル">×</button>
                        </form>
                    </div>
                </div>

                <div class="reservation-row">
                    <span>Shop</span>
                    <span>{{ $reservation->shop->name ?? '未設定' }}</span>
                </div>

                <div class="reservation-row">
                    <span>Date</span>
                    <span>{{ optional($reservation->reserved_date)->format('Y/m/d') }}</span>
                </div>

                <div class="reservation-row">
                    <span>Time</span>
                    <span>{{ $reservation->reserved_time }}</span>
                </div>

                <div class="reservation-row">
                    <span>Number</span>
                    <span>{{ $reservation->number_of_people }} 人</span>
                </div>

                <div class="reservation-row">
                    <span>Course</span>
                    <span>{{ $reservation->course->name ?? '未設定' }}</span>
                </div>

            </div>
            @empty
            <p class="no-reservation">現在、予約はありません。</p>
            @endforelse
        </div>

        {{-- 来店済みタブ --}}
        <div
            id="tab-visited"
            class="tab-panel"
            role="tabpanel"
            aria-labelledby="tab-btn-visited"
            hidden>
            @forelse($visitedReservations as $index => $reservation)
            <div class="reservation-card">

                <div class="reservation-card-header">
                    <div class="reservation-title">

                        {{-- 時計アイコン（CSS描画） --}}
                        <div class="reservation-icon" aria-hidden="true">
                            <div class="clock-face">
                                <div class="hand hour"></div>
                                <div class="hand minute"></div>
                                <div class="tick t1"></div>
                                <div class="tick t2"></div>
                                <div class="tick t3"></div>
                                <div class="tick t4"></div>
                                <div class="tick t5"></div>
                                <div class="tick t6"></div>
                                <div class="tick t7"></div>
                                <div class="tick t8"></div>
                                <div class="tick t9"></div>
                                <div class="tick t10"></div>
                                <div class="tick t11"></div>
                                <div class="tick t12"></div>
                            </div>
                        </div>

                        <span>来店{{ $index + 1 }}</span>
                    </div>

                    <div class="reservation-header-actions">
                        <a href="{{ route('reservations.review.form', $reservation) }}" class="reservation-edit-btn">
                            {{ $reservation->hasReview() ? '口コミを編集' : '口コミを書く' }}
                        </a>
                    </div>
                </div>

                <div class="reservation-row">
                    <span>Shop</span>
                    <span>{{ $reservation->shop->name ?? '未設定' }}</span>
                </div>

                <div class="reservation-row">
                    <span>Date</span>
                    <span>{{ optional($reservation->reserved_date)->format('Y/m/d') }}</span>
                </div>

                <div class="reservation-row">
                    <span>Time</span>
                    <span>{{ $reservation->reserved_time }}</span>
                </div>

                <div class="reservation-row">
                    <span>Number</span>
                    <span>{{ $reservation->number_of_people }} 人</span>
                </div>

                <div class="reservation-row">
                    <span>Course</span>
                    <span>{{ $reservation->course->name ?? '未設定' }}</span>
                </div>

            </div>
            @empty
            <p class="no-reservation">来店済みの予約はありません。</p>
            @endforelse
        </div>

    </section>

    {{-- 右：ユーザー情報 & お気に入り --}}
    <section class="mypage-right" aria-labelledby="favorites-heading">

        <h2 class="user-name">{{ $user->name }}さん</h2>

        <h2 id="favorites-heading" class="section-title">お気に入り店舗</h2>

        <div class="favorite-list">
            @forelse($favoriteShops as $shop)
            <article class="shop-card">
                <div class="shop-card-image">
                    @php
                    $image = $shop->image_path
                    ? asset('storage/' . $shop->image_path)
                    : asset('images/noimage.jpg');
                    @endphp
                    <img src="{{ $image }}" alt="{{ $shop->name }}">
                </div>

                <div class="shop-card-body">
                    <h3 class="shop-name">{{ $shop->name }}</h3>
                    <p class="shop-tags">#{{ $shop->area }} #{{ $shop->genre }}</p>

                    <div class="shop-card-footer">
                        <a href="{{ route('detail', $shop->id) }}" class="detail-btn">
                            詳しくみる
                        </a>

                        <form action="{{ route('favorite.destroy', $shop->id) }}" method="POST" class="favorite-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="like-btn liked" aria-label="お気に入り解除">
                                <i class="fa-solid fa-heart" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
            @empty
            <p class="no-favorite">お気に入り登録された店舗はありません。</p>
            @endforelse
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('.tab-panel');

        const activate = (btn) => {
            const targetId = btn.dataset.tab;

            buttons.forEach(b => {
                const isActive = (b === btn);
                b.classList.toggle('is-active', isActive);
                b.setAttribute('aria-selected', isActive ? 'true' : 'false');
                b.setAttribute('tabindex', isActive ? '0' : '-1');
            });

            panels.forEach(p => {
                const isTarget = (p.id === targetId);
                p.hidden = !isTarget;
                p.classList.toggle('is-active', isTarget);
            });
        };

        buttons.forEach(btn => {
            btn.addEventListener('click', () => activate(btn));
            btn.addEventListener('keydown', (e) => {
                if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') return;

                const arr = Array.from(buttons);
                const idx = arr.indexOf(btn);
                const next = e.key === 'ArrowRight' ?
                    arr[(idx + 1) % arr.length] :
                    arr[(idx - 1 + arr.length) % arr.length];

                next.focus();
                activate(next);
            });
        });
    });
</script>
@endpush