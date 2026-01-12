@extends('layouts.app')

@section('title', 'メール認証のお願い')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify_prompt.css') }}">
@endsection

@section('content')
<div class="verify-page">
    <section class="verify-card" aria-labelledby="verify-heading">

        <h1 id="verify-heading" class="verify-title">メール認証のお願い</h1>

        <p class="verify-lead">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        {{-- 認証へ（MailHog 等の閲覧導線）※本番で出さない --}}
        @if(app()->environment('local'))
        <a href="{{ route('verify.mailhog') }}" class="btn-primary">認証はこちらから</a>
        @endif

        {{-- 認証メール再送 --}}
        <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
            @csrf
            <button type="submit" class="link-like">認証メールを再送する</button>
        </form>

    </section>
</div>
@endsection