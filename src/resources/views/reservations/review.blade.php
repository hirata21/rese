@extends('layouts.app')

@section('title', '口コミ投稿')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/review.css') }}">
@endsection

@section('content')
<div class="review-page">
    <div class="review-card">

        {{-- 店舗情報ヘッダー --}}
        <div class="review-shop-header">

            <div class="shop-image">
                <img src="{{ shop_image_url($reservation->shop) }}" alt="{{ $reservation->shop->name }}">
            </div>

            <div class="shop-info">
                <h2 class="shop-name">{{ $reservation->shop->name }}</h2>
                <p class="shop-meta">
                    {{ optional($reservation->reserved_date)->format('Y/m/d') }}
                    {{ $reservation->reserved_time }}
                    / {{ $reservation->number_of_people }}人
                </p>
            </div>
        </div>

        <h1 class="review-title">口コミ投稿</h1>

        @if (session('status'))
        <div class="flash" role="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('reservations.review', $reservation) }}" class="review-form">
            @csrf

            {{-- 実際に送る値 --}}
            <input
                type="hidden"
                name="rating"
                id="rating-value"
                value="{{ old('rating', $reservation->rating) }}">

            <div class="star-block">
                <div class="star-label">評価</div>

                <div class="stars" id="stars" role="group" aria-label="星評価">
                    @for ($i = 1; $i <= 5; $i++)
                        <button
                        type="button"
                        class="star"
                        data-value="{{ $i }}"
                        aria-label="{{ $i }}点"
                        aria-pressed="false">★</button>
                        @endfor
                </div>

                @error('rating')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="comment-block">
                <label class="comment-label" for="review_comment">コメント（任意）</label>
                <textarea
                    id="review_comment"
                    name="review_comment"
                    rows="5"
                    maxlength="500"
                    class="comment-textarea"
                    placeholder="例：料理が美味しかった、スタッフが丁寧だった等">{{ old('review_comment', $reservation->review_comment) }}</textarea>

                @error('review_comment')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="review-actions">
                <a href="{{ route('mypage') }}" class="btn-sub">戻る</a>
                <button type="submit" class="btn-main">投稿する</button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const starsWrap = document.getElementById('stars');
        if (!starsWrap) return;

        const stars = [...starsWrap.querySelectorAll('.star')];
        const hidden = document.getElementById('rating-value');
        if (!hidden) return;

        const paint = (value) => {
            const v0 = Number(value || 0);
            stars.forEach(star => {
                const v = Number(star.dataset.value);
                const on = v <= v0;
                star.classList.toggle('is-on', on);
                star.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
        };

        // 初期表示（編集時）
        paint(hidden.value);

        // クリックで確定
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = Number(star.dataset.value);
                hidden.value = String(value);
                paint(value);
            });

            // ホバーでプレビュー（PC向け）
            star.addEventListener('mouseenter', () => {
                paint(Number(star.dataset.value));
            });
        });

        // ホバーを外したら確定値に戻す
        starsWrap.addEventListener('mouseleave', () => {
            paint(hidden.value);
        });
    });
</script>
@endpush