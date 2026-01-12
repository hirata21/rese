@extends('layouts.app')

@section('title', '予約完了')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/done.css') }}">
@endsection

@section('content')
<div class="done-container">
    <section class="done-card" aria-labelledby="done-heading">

        <p class="message">ご予約ありがとうございます</p>

        <a href="{{ route('shops.index') }}" class="back-btn">戻る</a>
    </section>
</div>
@endsection