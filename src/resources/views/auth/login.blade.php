@extends('layouts.app')

@section('title', 'ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="auth-page user-login">
    <div class="auth-card">
        <div class="card-header">
            <h1 class="card-title">Login</h1>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
                @csrf

                <div class="form-group">
                    <div class="form-row">
                        <span class="form-icon" aria-hidden="true">
                            <img src="{{ asset('icons/mail.png') }}" alt="">
                        </span>

                        <div class="form-field">
                            <label for="email" class="sr-only">Email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Email"
                                autocomplete="email"
                                inputmode="email">
                        </div>
                    </div>

                    @error('email')
                    <p class="error-text" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <span class="form-icon" aria-hidden="true">
                            <img src="{{ asset('icons/lock.png') }}" alt="">
                        </span>

                        <div class="form-field">
                            <label for="password" class="sr-only">Password</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Password"
                                autocomplete="current-password">
                        </div>
                    </div>

                    @error('password')
                    <p class="error-text" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">ログイン</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection