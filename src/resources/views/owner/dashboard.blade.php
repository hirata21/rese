@extends('layouts.app')

@section('title', 'オーナー管理画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/owner/dashboard.css') }}">
@endsection

@section('content')
<div class="owner-admin">

    {{-- 左メニュー --}}
    <aside class="owner-side">
        <div class="owner-side__brand">Owner Panel</div>

        <nav class="owner-nav" aria-label="オーナーメニュー">
            <a class="owner-nav__item is-active" href="{{ route('owner.dashboard') }}">
                ダッシュボード
            </a>

            @if($shop)
            <a class="owner-nav__item" href="{{ route('owner.shop.edit') }}">
                店舗情報の更新
            </a>
            @else
            <a class="owner-nav__item" href="{{ route('owner.shop.create') }}">
                店舗情報の作成
            </a>
            @endif

            <a class="owner-nav__item" href="{{ route('owner.reservations.index') }}">
                予約確認
            </a>

            <a class="owner-nav__item" href="{{ route('owner.mail.create') }}">
                お知らせメール
            </a>

            <a class="owner-nav__item" href="{{ route('owner.checkin.form') }}">
                QR照合（チェックイン）
            </a>
        </nav>
    </aside>

    {{-- メイン（layoutのmainと二重にならないようdiv） --}}
    <div class="owner-main">

        <header class="owner-header">
            <div>
                <h1 class="owner-title">オーナー管理画面</h1>
                <p class="owner-sub">
                    ログイン中：{{ auth()->user()->name }}
                    @if($shop)
                    / 店舗：{{ $shop->name }}
                    @endif
                </p>
            </div>

            {{-- ログアウト（右上） --}}
            <form method="POST" action="{{ route('owner.logout') }}" class="owner-logout">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                    ログアウト
                </button>
            </form>
        </header>

        @if (session('status'))
        <div class="owner-flash" role="status">{{ session('status') }}</div>
        @endif

        <section class="owner-grid" aria-label="管理メニュー">
            {{-- 店舗情報 --}}
            <div class="owner-card">
                <div class="owner-card__head">
                    <h2 class="owner-card__title">店舗情報</h2>
                    <span class="owner-badge {{ $shop ? 'is-ok' : 'is-warn' }}">
                        {{ $shop ? '登録済み' : '未登録' }}
                    </span>
                </div>

                <p class="owner-card__text">
                    店舗名・エリア・ジャンル・説明・画像などを管理できます。
                </p>

                <div class="owner-card__actions">
                    @if($shop)
                    <a class="btn btn--primary" href="{{ route('owner.shop.edit') }}">
                        店舗情報を更新する
                    </a>
                    @else
                    <a class="btn btn--primary" href="{{ route('owner.shop.create') }}">
                        店舗情報を作成する
                    </a>
                    @endif
                </div>
            </div>

            {{-- 予約確認 --}}
            <div class="owner-card">
                <div class="owner-card__head">
                    <h2 class="owner-card__title">予約確認</h2>
                </div>

                <p class="owner-card__text">
                    自店舗の予約一覧を確認できます（日時・人数・予約者など）。
                </p>

                <div class="owner-card__actions">
                    <a class="btn btn--primary" href="{{ route('owner.reservations.index') }}">
                        予約一覧を見る
                    </a>
                </div>
            </div>

            {{-- お知らせメール --}}
            <div class="owner-card">
                <div class="owner-card__head">
                    <h2 class="owner-card__title">お知らせメール</h2>
                </div>

                <p class="owner-card__text">
                    予約者にキャンペーンや臨時休業などのお知らせを送信できます。
                </p>

                <div class="owner-card__actions">
                    <a class="btn btn--primary" href="{{ route('owner.mail.create') }}">
                        メールを作成する
                    </a>
                </div>
            </div>

            {{-- QR照合（チェックイン） --}}
            <div class="owner-card">
                <div class="owner-card__head">
                    <h2 class="owner-card__title">QR照合</h2>
                </div>

                <p class="owner-card__text">
                    来店時に予約者のQRコードを読み取り、チェックイン処理を行います。
                </p>

                <div class="owner-card__actions">
                    <a class="btn btn--primary" href="{{ route('owner.checkin.form') }}">
                        QR照合を開始する
                    </a>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection