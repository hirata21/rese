@extends('layouts.app')

@section('title', 'お知らせメール')

@section('css')
<link rel="stylesheet" href="{{ asset('css/owner/mail.css') }}">
@endsection

@section('content')
<div class="wrap">

    <div class="head">
        <div>
            <h1 class="title">お知らせメール</h1>
            <p class="sub">店舗：{{ $shop->name }}</p>
        </div>

        <a href="{{ route('owner.dashboard') }}" class="btn">
            ダッシュボードに戻る
        </a>
    </div>

    @if (session('status'))
    <div class="flash" role="status">
        {{ session('status') }}
    </div>
    @endif

    <div class="card">
        <h2 class="card-title">メール作成</h2>
        <p class="desc">
            登録されている利用者へお知らせを送信できます（キャンペーン、臨時休業など）。
        </p>

        <form method="POST" action="{{ route('owner.mail.send') }}">
            @csrf

            <div class="form-group">
                <label for="subject">件名 <span class="req" aria-hidden="true">*</span></label>
                <input
                    id="subject"
                    type="text"
                    name="subject"
                    value="{{ old('subject') }}"
                    placeholder="例：年末キャンペーンのお知らせ"
                    required
                    autocomplete="off"
                    aria-describedby="@error('subject') subject-error @enderror">
                @error('subject')
                <div id="subject-error" class="error" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="body">本文 <span class="req" aria-hidden="true">*</span></label>
                <textarea
                    id="body"
                    name="body"
                    rows="10"
                    placeholder="例：いつもご利用ありがとうございます。"
                    required
                    aria-describedby="@error('body') body-error @enderror">{{ old('body') }}</textarea>
                @error('body')
                <div id="body-error" class="error" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button type="submit" class="btn btn--primary">送信する</button>
            </div>
        </form>
    </div>
</div>
@endsection