@extends('layouts.app')

@section('title', '来店用QRコード')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/qr.css') }}">
@endsection

@section('content')
@php
// reserved_time が "09:30:00" や "09:30 " でも落ちないようにする
$timeText = $reservation->reserved_time
? mb_substr(trim($reservation->reserved_time), 0, 5)
: '--:--';

$dateText = $reservation->reserved_date
? optional($reservation->reserved_date)->format('Y年m月d日')
: '----年--月--日';
@endphp

<div class="qr-wrap">
    <h1 class="qr-title">来店用QRコード</h1>

    <div class="qr-card">
        <p class="qr-text">
            店舗スタッフにこのQRを提示してください。
        </p>

        <div class="qr-code">
            {!! QrCode::size(240)->margin(2)->generate($checkinUrl) !!}
        </div>

        <p class="qr-info">
            予約日：{{ $dateText }} {{ $timeText }}
            / 人数：{{ $reservation->number_of_people }}名
        </p>

        {{-- チェックインURL表示 --}}
        <div class="qr-dev">
            <strong>チェックインURL：</strong><br>
            {{ $checkinUrl }}
        </div>
    </div>
</div>
@endsection