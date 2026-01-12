@extends('layouts.app', ['hideLogo' => true])

@section('title', '店舗代表者の作成')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/owner-create.css') }}">
@endsection

@section('header-right')
@auth('admin')
<form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button type="submit" class="logout-btn">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        ログアウト
    </button>
</form>
@endauth
@endsection

@section('content')
<div class="container owner-create-container">
    <h1 class="owner-create-title">
        店舗代表者（オーナー）作成
    </h1>

    @if (session('status'))
    <div class="owner-create-flash" role="status">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.owners.store') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">名前</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="form-input">
            @error('name')
            <div class="form-error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-input">
            @error('email')
            <div class="form-error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">パスワード</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input">
            @error('password')
            <div class="form-error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group form-group--spaced">
            <label for="password_confirmation" class="form-label">
                パスワード（確認）
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-input">
        </div>

        <button type="submit" class="submit-btn">
            登録する
        </button>
    </form>
</div>
@endsection