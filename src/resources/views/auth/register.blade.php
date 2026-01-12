@extends('layouts.app')

@section('title', '会員登録')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<div class="register-page">
    <div class="register-card">
        <div class="card-header">
            <h1 class="card-title">会員登録</h1>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                {{-- Username --}}
                <div class="form-row">
                    <span class="form-icon" aria-hidden="true">
                        <img src="{{ asset('icons/user.png') }}" alt="">
                    </span>

                    <div class="form-field">
                        <label for="name" class="sr-only">ユーザー名</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Username"
                            autocomplete="name"
                            required
                            autofocus
                            @error('name') aria-invalid="true" aria-describedby="error-name" @enderror>
                    </div>
                </div>
                @error('name')
                <p id="error-name" class="error-text" role="alert">{{ $message }}</p>
                @enderror

                {{-- Email --}}
                <div class="form-row">
                    <span class="form-icon" aria-hidden="true">
                        <img src="{{ asset('icons/mail.png') }}" alt="">
                    </span>

                    <div class="form-field">
                        <label for="email" class="sr-only">メールアドレス</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Email"
                            autocomplete="email"
                            required
                            @error('email') aria-invalid="true" aria-describedby="error-email" @enderror>
                    </div>
                </div>
                @error('email')
                <p id="error-email" class="error-text" role="alert">{{ $message }}</p>
                @enderror

                {{-- Password --}}
                <div class="form-row">
                    <span class="form-icon" aria-hidden="true">
                        <img src="{{ asset('icons/lock.png') }}" alt="">
                    </span>

                    <div class="form-field">
                        <label for="password" class="sr-only">パスワード</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Password"
                            autocomplete="new-password"
                            required
                            @error('password') aria-invalid="true" aria-describedby="error-password" @enderror>
                    </div>
                </div>
                @error('password')
                <p id="error-password" class="error-text" role="alert">{{ $message }}</p>
                @enderror

                <div class="form-actions">
                    <button type="submit" class="btn-submit">登録</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection