@extends('layouts.app')

@section('title', 'チェックイン照合')

@section('css')
<link rel="stylesheet" href="{{ asset('css/owner/checkin_form.css') }}">
@endsection

@section('content')
<div class="checkin-wrap">

    {{-- タイトル行（左：h1 / 右：戻る） --}}
    <div class="checkin-head">
        <h1 class="checkin-title">チェックイン照合</h1>

        <a href="{{ route('owner.dashboard') }}" class="btn">
            ダッシュボードに戻る
        </a>
    </div>

    @if (session('status'))
    <div class="checkin-flash" role="status">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('owner.checkin') }}">
        @csrf

        <div class="checkin-group">
            <label for="token" class="checkin-label">QRトークン</label>

            <input
                id="token"
                name="token"
                type="text"
                class="checkin-input"
                value="{{ old('token') }}"
                autocomplete="off"
                required
                inputmode="text"
                aria-describedby="token-help @error('token') token-error @enderror">

            <p id="token-help" class="checkin-help">
                QRコードに含まれるトークン文字列を貼り付けてください。
            </p>

            @error('token')
            <div id="token-error" class="checkin-error" role="alert">
                {{ $message }}
            </div>
            @enderror
        </div>

        {{-- 右寄せボタン --}}
        <div class="checkin-actions">
            <button type="submit" class="btn btn--primary">
                照合してチェックイン
            </button>
        </div>
    </form>
</div>
@endsection