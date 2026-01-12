@extends('layouts.app', ['hideLogo' => true])

@section('title', 'Admin Login')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')
<div class="auth-page admin-login">
    <div class="auth-card">
        <div class="card-header">
            <h1 class="card-title">Admin Login</h1>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.login') }}" class="auth-form" novalidate>
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <div class="form-row">
                        <span class="form-icon">
                            <img src="{{ asset('icons/mail.png') }}" alt="">
                        </span>
                        <div class="form-field">
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Email">
                        </div>
                    </div>
                    @error('email')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <div class="form-row">
                        <span class="form-icon">
                            <img src="{{ asset('icons/lock.png') }}" alt="">
                        </span>
                        <div class="form-field">
                            <input
                                type="password"
                                name="password"
                                placeholder="Password">
                        </div>
                    </div>
                    @error('password')
                    <p class="error-text">{{ $message }}</p>
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