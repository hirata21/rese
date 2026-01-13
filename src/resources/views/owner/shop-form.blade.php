@extends('layouts.app')

@section('title', $mode === 'create' ? '店舗情報の作成' : '店舗情報の更新')

@section('css')
<link rel="stylesheet" href="{{ asset('css/owner/shop-form.css') }}">
@endsection

@section('content')
<div class="owner-main">

    @php
    $genres = [
    '和食',
    '寿司',
    'イタリアン',
    'フレンチ',
    '中華',
    '焼肉',
    'ラーメン',
    'うどん・そば',
    '定食・食堂',
    ];

    $prefectures = [
    '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県',
    '茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県',
    '新潟県','富山県','石川県','福井県','山梨県','長野県',
    '岐阜県','静岡県','愛知県','三重県',
    '滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
    '鳥取県','島根県','岡山県','広島県','山口県',
    '徳島県','香川県','愛媛県','高知県',
    '福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県',
    '沖縄県',
    ];
    @endphp

    {{-- タイトル行（左：h1 / 右：戻るボタン） --}}
    <div class="owner-head">
        <h1 class="owner-title">
            {{ $mode === 'create' ? '店舗情報の作成' : '店舗情報の更新' }}
        </h1>

        <a href="{{ route('owner.dashboard') }}" class="btn owner-head__btn">
            ダッシュボードに戻る
        </a>
    </div>

    @if (session('status'))
    <div class="owner-flash" role="status">
        {{ session('status') }}
    </div>
    @endif

    <form
        method="POST"
        enctype="multipart/form-data"
        action="{{ $mode === 'create' ? route('owner.shop.store') : route('owner.shop.update') }}">
        @csrf

        @if ($mode === 'edit')
        @method('PUT')
        @endif

        {{-- 店舗画像 --}}
        <div class="form-group">
            <label for="imageInput">店舗画像</label>

            <input
                type="file"
                name="image"
                id="imageInput"
                accept="image/jpeg,image/png">

            @error('image')
            <div class="error" role="alert">{{ $message }}</div>
            @enderror

            {{-- 画像プレビュー横並び --}}
            <div class="image-compare">

                {{-- 選択中プレビュー --}}
                <div class="current-image">
                    <p class="current-image__label">選択中の画像</p>
                    <img
                        id="imagePreview"
                        class="current-image__img"
                        alt="選択中の店舗画像プレビュー"
                        style="display:none;">
                </div>

                {{-- 編集時の現在画像 --}}
                @if ($mode === 'edit' && !empty($shop->image_path))
                <div class="current-image">
                    <p class="current-image__label">現在の画像</p>
                    <img
                        src="{{ shop_image_url($shop) }}"
                        alt="現在の店舗画像"
                        class="current-image__img">
                </div>
                @endif
            </div>
        </div>

        {{-- 店舗名 --}}
        <div class="form-group">
            <label for="name">店舗名 <span class="required">*</span></label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name', $shop->name ?? '') }}"
                required>
            @error('name')
            <div class="error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        {{-- エリア（都道府県） --}}
        <div class="form-group">
            <label for="area">エリア（都道府県） <span class="required">*</span></label>

            <select id="area" name="area" required>
                <option value="">選択してください</option>
                @foreach ($prefectures as $prefecture)
                <option
                    value="{{ $prefecture }}"
                    {{ old('area', $shop->area ?? '') === $prefecture ? 'selected' : '' }}>
                    {{ $prefecture }}
                </option>
                @endforeach
            </select>

            @error('area')
            <div class="error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        {{-- ジャンル --}}
        <div class="form-group">
            <label for="genre">ジャンル <span class="required">*</span></label>

            <select id="genre" name="genre" required>
                <option value="">選択してください</option>

                @foreach ($genres as $genre)
                <option
                    value="{{ $genre }}"
                    {{ old('genre', $shop->genre ?? '') === $genre ? 'selected' : '' }}>
                    {{ $genre }}
                </option>
                @endforeach
            </select>

            @error('genre')
            <div class="error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        {{-- 説明 --}}
        <div class="form-group">
            <label for="description">店舗説明 <span class="required">*</span></label>
            <textarea
                id="description"
                name="description"
                rows="4"
                required>{{ old('description', $shop->description ?? '') }}</textarea>

            @error('description')
            <div class="error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        <div class="owner-actions">
            <button class="btn btn--primary" type="submit">
                {{ $mode === 'create' ? '作成する' : '更新する' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');

        if (!input || !preview) return;

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];

            if (!file) {
                preview.src = '';
                preview.style.display = 'none';
                return;
            }

            if (!file.type.startsWith('image/')) {
                alert('画像ファイル（JPEG/PNG）を選択してください。');
                input.value = '';
                preview.src = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush