@extends('layouts.app')

@section('title', '店舗一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shop/index.css') }}">
@endsection

{{-- ★ ヘッダー右側に表示する検索バー --}}
@section('header-right')
<form action="{{ route('shops.index') }}" method="GET" class="search-bar" role="search" aria-label="店舗検索">

    {{-- エリア --}}
    <label class="sr-only" for="search-area">エリア</label>
    <select id="search-area" name="area">
        <option value="">All area</option>
        @foreach($areas as $a)
        <option value="{{ $a }}" {{ ($area ?? '') === $a ? 'selected' : '' }}>
            {{ $a }}
        </option>
        @endforeach
    </select>

    {{-- ジャンル --}}
    <label class="sr-only" for="search-genre">ジャンル</label>
    <select id="search-genre" name="genre">
        <option value="">All genre</option>
        @foreach($genres as $g)
        <option value="{{ $g }}" {{ ($genre ?? '') === $g ? 'selected' : '' }}>
            {{ $g }}
        </option>
        @endforeach
    </select>

    {{-- 検索キーワード --}}
    <label class="sr-only" for="search-word">キーワード</label>
    <input id="search-word" type="text" name="search" value="{{ $word ?? '' }}" placeholder="Search …">

    {{-- 見た目には出さない送信ボタン（Enter以外でも送れるように） --}}
    <button type="submit" class="sr-only">検索</button>
</form>
@endsection

@section('content')
<div class="container">

    <h1 class="page-title">店舗一覧</h1>

    {{-- --- 店舗一覧 --- --}}
    <div class="grid">
        @php
        $user = auth()->user();
        @endphp

        @foreach ($shops as $shop)
        <div class="card">

            {{-- 画像 --}}
            @php
            $image = $shop->image_path
            ? asset($shop->image_path)
            : asset('images/noimage.jpg');
            @endphp
            <img src="{{ $image }}" alt="{{ $shop->name }}">

            <div class="card-body">
                <h2 class="card-title">{{ $shop->name }}</h2>
                <p class="card-tags">#{{ $shop->area }} #{{ $shop->genre }}</p>

                <div class="card-footer">

                    {{-- 詳しくみる --}}
                    <a href="{{ route('detail', $shop->id) }}" class="btn-detail">
                        詳しくみる
                    </a>

                    {{-- お気に入り --}}
                    @php
                    $isFavorite = $user
                    ? $user->favorites()->where('shop_id', $shop->id)->exists()
                    : false;
                    @endphp

                    @if ($user)
                    @if (! $isFavorite)
                    <form action="{{ route('favorite.store', $shop->id) }}" method="POST" class="favorite-form">
                        @csrf
                        <button type="submit" class="like-btn not-liked" aria-label="お気に入りに追加">
                            <i class="fa-solid fa-heart" aria-hidden="true"></i>
                        </button>
                    </form>
                    @else
                    <form action="{{ route('favorite.destroy', $shop->id) }}" method="POST" class="favorite-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="like-btn liked" aria-label="お気に入りを解除">
                            <i class="fa-solid fa-heart" aria-hidden="true"></i>
                        </button>
                    </form>
                    @endif
                    @else
                    {{-- 未ログイン時：ログインに促す --}}
                    <a href="{{ route('login') }}" class="like-btn not-liked" aria-label="ログインしてお気に入りを利用">
                        <i class="fa-solid fa-heart" aria-hidden="true"></i>
                    </a>
                    @endif

                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection