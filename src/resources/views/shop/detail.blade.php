@extends('layouts.app')

@section('title', $shop->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/shop/detail.css') }}">
@endsection

@section('content')
<div class="detail-page">

    {{-- 左カラム：店舗情報 --}}
    <div class="shop-column">

        {{-- 戻るボタン --}}
        <a href="{{ route('shops.index') }}" class="back-link">
            <img src="{{ asset('icons/arrow_left.png') }}" alt="戻る" class="back-icon">
            <span class="back-text">{{ $shop->name }}</span>
        </a>

        {{-- 画像（helpers 経由で統一） --}}
        <div class="shop-image-wrap">
            <img src="{{ shop_image_url($shop) }}" alt="{{ $shop->name }}">
        </div>

        {{-- ハッシュタグ的な表示 --}}
        <p class="shop-tags">#{{ $shop->area }} #{{ $shop->genre }}</p>

        {{-- 説明文 --}}
        <p class="shop-description">
            {{ $shop->description }}
        </p>
    </div>

    {{-- 右カラム：予約フォーム --}}
    <div class="reserve-column">
        <h2 class="reserve-title">予約</h2>

        @php
        $today = now()->toDateString();
        $defaultDate = old('date', $today);

        // 次の30分（例 14:01→14:30 / 14:31→15:00）
        if (old('time')) {
        $defaultTime = old('time');
        } else {
        $now = now();
        $min = (int) $now->format('i');

        if ($min === 0) {
        $defaultTime = $now->format('H:00');
        } elseif ($min <= 30) {
            $defaultTime=$now->format('H:30');
            } else {
            $defaultTime = $now->copy()->addHour()->format('H:00');
            }
            }

            $defaultNumber = (int) old('number', 1);
            $selectedCourseId = (string) old('course_id', '');
            @endphp

            <form action="{{ route('reservations.store', $shop) }}" method="POST" class="reserve-form">
                @csrf

                {{-- 日付 --}}
                <div class="reserve-field">
                    <label for="input-date" class="reserve-label">日付</label>
                    <input
                        id="input-date"
                        type="date"
                        name="date"
                        value="{{ $defaultDate }}"
                        min="{{ $today }}"
                        required>
                    @error('date')
                    <p class="error-message" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 時刻 --}}
                <div class="reserve-field">
                    <label for="input-time" class="reserve-label">時刻</label>
                    <select id="input-time" name="time" required>
                        @for ($h = 0; $h <= 23; $h++)
                            @foreach (['00', '30' ] as $m)
                            @php $time=sprintf('%02d:%s', $h, $m); @endphp
                            <option value="{{ $time }}" {{ $defaultTime === $time ? 'selected' : '' }}>
                            {{ $time }}
                            </option>
                            @endforeach
                            @endfor
                    </select>
                    @error('time')
                    <p class="error-message" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 人数 --}}
                <div class="reserve-field">
                    <label for="input-number" class="reserve-label">人数</label>
                    <select id="input-number" name="number" required>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ $defaultNumber === $i ? 'selected' : '' }}>
                            {{ $i }}人
                            </option>
                            @endfor
                    </select>
                    @error('number')
                    <p class="error-message" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- コース --}}
                <div class="reserve-field">
                    <label for="courseSelect" class="reserve-label">コース</label>
                    <select name="course_id" id="courseSelect">
                        <option value="">選択してください</option>
                        @foreach($shop->courses as $course)
                        <option
                            value="{{ $course->id }}"
                            data-name="{{ $course->name }}"
                            data-price="{{ $course->price }}"
                            {{ $selectedCourseId === (string)$course->id ? 'selected' : '' }}>
                            {{ $course->name }}（{{ number_format($course->price) }}円）
                        </option>
                        @endforeach
                    </select>

                    @error('course_id')
                    <p class="error-message" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 予約内容プレビュー --}}
                <div class="reserve-summary" aria-label="予約内容のプレビュー">
                    <div class="row">
                        <span>Shop</span>
                        <span id="preview-shop">{{ $shop->name }}</span>
                    </div>
                    <div class="row">
                        <span>Date</span>
                        <span id="preview-date">{{ $defaultDate }}</span>
                    </div>
                    <div class="row">
                        <span>Time</span>
                        <span id="preview-time">{{ $defaultTime }}</span>
                    </div>
                    <div class="row">
                        <span>Number</span>
                        <span id="preview-number">{{ $defaultNumber }}人</span>
                    </div>
                    <div class="row">
                        <span>Course</span>
                        <span id="preview-course">-</span>
                    </div>
                    <div class="row">
                        <span>Price</span>
                        <span id="preview-price">-</span>
                    </div>
                </div>

                <button type="submit" class="reserve-submit">
                    予約する
                </button>
            </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const inputDate = document.getElementById("input-date");
        const inputTime = document.getElementById("input-time");
        const inputNumber = document.getElementById("input-number");
        const courseSelect = document.getElementById("courseSelect");

        const previewDate = document.getElementById("preview-date");
        const previewTime = document.getElementById("preview-time");
        const previewNumber = document.getElementById("preview-number");
        const previewCourse = document.getElementById("preview-course");
        const previewPrice = document.getElementById("preview-price");

        if (!inputDate || !inputTime) return;

        const allTimeOptions = [...inputTime.options].map(opt => ({
            value: opt.value,
            label: opt.textContent
        }));

        const pad2 = n => String(n).padStart(2, "0");

        const getTodayStr = () => {
            const d = new Date();
            return `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`;
        };

        const toMinutes = (t) => {
            const [h, m] = t.split(":").map(Number);
            return h * 60 + m;
        };

        const getThresholdMinutes = () => {
            const d = new Date();
            const h = d.getHours();
            const m = d.getMinutes();
            if (m === 0) return h * 60 + 0;
            if (m <= 30) return h * 60 + 30;
            return (h + 1) * 60 + 0;
        };

        const rebuildTimeOptions = () => {
            const selectedDate = inputDate.value;
            const today = getTodayStr();
            const isToday = selectedDate === today;

            const prevValue = inputTime.value;

            const threshold = getThresholdMinutes();
            const nextOptions = isToday ?
                allTimeOptions.filter(o => toMinutes(o.value) >= threshold) :
                allTimeOptions;

            inputTime.innerHTML = "";

            if (nextOptions.length === 0) {
                const opt = document.createElement("option");
                opt.value = "";
                opt.textContent = "選択できる時間がありません";
                inputTime.appendChild(opt);
            } else {
                nextOptions.forEach(o => {
                    const opt = document.createElement("option");
                    opt.value = o.value;
                    opt.textContent = o.label;
                    inputTime.appendChild(opt);
                });

                const canKeep = nextOptions.some(o => o.value === prevValue);
                inputTime.value = canKeep ? prevValue : nextOptions[0].value;
            }

            if (previewTime) previewTime.textContent = inputTime.value || "--:--";
        };

        const updateCoursePreview = () => {
            if (!previewCourse || !previewPrice || !courseSelect) return;

            const opt = courseSelect.options[courseSelect.selectedIndex];
            const hasCourse = !!courseSelect.value;

            if (!hasCourse || !opt) {
                previewCourse.textContent = "-";
                previewPrice.textContent = "-";
                return;
            }

            previewCourse.textContent = opt.dataset?.name || "-";
            const price = opt.dataset?.price;
            previewPrice.textContent = price ? `${Number(price).toLocaleString()}円` : "-";
        };

        if (previewDate) previewDate.textContent = inputDate.value;
        if (previewNumber && inputNumber) previewNumber.textContent = inputNumber.value + "人";

        rebuildTimeOptions();
        updateCoursePreview();

        inputDate.addEventListener("change", () => {
            if (previewDate) previewDate.textContent = inputDate.value;
            rebuildTimeOptions();
        });

        inputTime.addEventListener("change", () => {
            if (previewTime) previewTime.textContent = inputTime.value || "--:--";
        });

        if (inputNumber) {
            inputNumber.addEventListener("change", () => {
                if (previewNumber) previewNumber.textContent = inputNumber.value + "人";
            });
        }

        if (courseSelect) {
            courseSelect.addEventListener("change", updateCoursePreview);
        }
    });
</script>
@endpush