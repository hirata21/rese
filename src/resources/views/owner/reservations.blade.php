@extends('layouts.app')

@section('title', '予約確認')

@section('css')
<link rel="stylesheet" href="{{ asset('css/owner/reservations.css') }}">
@endsection

@section('content')
<div class="owner-wrap">

    {{-- ヘッダー --}}
    <div class="page-head">
        <div>
            <h1 class="page-title">予約確認</h1>
            <p class="page-sub">店舗：{{ $shop->name }}</p>
        </div>

        <a href="{{ route('owner.dashboard') }}" class="btn">
            ダッシュボードに戻る
        </a>
    </div>

    {{-- フラッシュ --}}
    @if (session('status'))
    <div class="owner-flash" role="status">
        {{ session('status') }}
    </div>
    @endif

    {{-- 一覧 --}}
    <div class="card">
        <div class="card-head">
            <h2 class="card-title">予約一覧</h2>
            <span class="count">全 {{ $reservations->total() }} 件</span>
        </div>

        @php
        $statusLabel = [
        'reserved' => '予約中',
        'visited' => '来店済み',
        'cancelled' => 'キャンセル',
        ];

        $statusClass = [
        'reserved' => 'badge is-ok',
        'visited' => 'badge is-info',
        'cancelled' => 'badge is-warn',
        ];
        @endphp

        @if ($reservations->isEmpty())
        <p class="empty">予約はまだありません。</p>
        @else
        <div class="table-wrap">
            <table class="table">
                <caption class="sr-only">予約一覧テーブル</caption>
                <thead>
                    <tr>
                        <th scope="col">日時</th>
                        <th scope="col">人数</th>
                        <th scope="col">予約者</th>
                        <th scope="col">ステータス</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($reservations as $reservation)
                    @php
                    $label = $statusLabel[$reservation->status] ?? $reservation->status;
                    $class = $statusClass[$reservation->status] ?? 'badge';

                    $dateText = optional($reservation->reserved_date)->format('Y/m/d');
                    $timeText = $reservation->reserved_time; // 形式が H:i 前提
                    @endphp

                    <tr>
                        <td>{{ $dateText }} {{ $timeText }}</td>
                        <td>{{ $reservation->number_of_people }} 人</td>
                        <td>{{ $reservation->user->name ?? '不明' }}</td>
                        <td><span class="{{ $class }}">{{ $label }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $reservations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection