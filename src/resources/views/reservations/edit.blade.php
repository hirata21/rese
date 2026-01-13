@extends('layouts.app')

@section('title', $shop->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/shop/detail.css') }}">
@endsection

@section('content')
<div class="detail-page">

    {{-- 左カラム：店舗情報 --}}
    <div class="shop-column">
        <a href="{{ route('mypage') }}" class="back-link">
            <img src="{{ asset('icons/arrow_left.png') }}" alt="戻る" class="back-icon">
            <span class="back-text">{{ $shop->name }}</span>
        </a>

        <div class="shop-image-wrap">
            <img src="{{ shop_image_url($shop) }}" alt="{{ $shop->name }}">
        </div>

        <p class="shop-tags">#{{ $shop->area }} #{{ $shop->genre }}</p>

        <p class="shop-description">
            {{ $shop->description }}
        </p>
    </div>

    {{-- 右カラム：予約「変更」フォーム --}}
    <div class="reserve-column">
        <h2 class="reserve-title">予約</h2>

        <form action="{{ route('reservations.update', $reservation) }}"
            method="POST"
            class="reserve-form">
            @csrf
            @method('PATCH')

            @php
            // 予約済みの値を初期値にする（バリデーションエラー時は old を優先）
            $defaultDate = old('date', optional($reservation->reserved_date)->format('Y-m-d'));

            $timeRaw = old('time', $reservation->reserved_time);
            $defaultTime = $timeRaw ? substr($timeRaw, 0, 5) : ''; // 17:00:00 → 17:00

            $defaultNumber = (int) old('number', $reservation->number_of_people);

            $defaultCourseId = old('course_id', $reservation->course_id);

            $defaultCourseName = optional($reservation->course)->name ?? '未設定';
            $defaultCoursePrice = optional($reservation->course)->price ?? $reservation->price;
            @endphp

            {{-- 日付 --}}
            <label class="reserve-field">
                <input
                    id="input-date"
                    type="date"
                    name="date"
                    value="{{ $defaultDate }}"
                    min="{{ now()->toDateString() }}"
                    required>
                @error('date')
                <p class="error-message" role="alert">{{ $message }}</p>
                @enderror
            </label>

            {{-- 時刻（30分刻み／今日の場合は過去を除外） --}}
            <label class="reserve-field">
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
            </label>

            {{-- 人数 --}}
            <label class="reserve-field">
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
            </label>

            {{-- コース --}}
            <label class="reserve-field">
                <select id="input-course" name="course_id">
                    <option value="">コースを選択してください</option>
                    @foreach($courses as $course)
                    <option
                        value="{{ $course->id }}"
                        data-name="{{ $course->name }}"
                        data-price="{{ (int)$course->price }}"
                        {{ (string)$defaultCourseId === (string)$course->id ? 'selected' : '' }}>
                        {{ $course->name }}（{{ number_format($course->price) }}円）
                    </option>
                    @endforeach
                </select>

                @error('course_id')
                <p class="error-message" role="alert">{{ $message }}</p>
                @enderror
            </label>

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
                    <span id="preview-course">{{ $defaultCourseName }}</span>
                </div>
                <div class="row">
                    <span>Price</span>
                    <span id="preview-price">
                        {{ $defaultCoursePrice ? number_format((int)$defaultCoursePrice).'円' : '-' }}
                    </span>
                </div>
            </div>

            <button type="submit" class="reserve-submit">
                変更する
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const inputDate = document.getElementById("input-date");
        const inputTime = document.getElementById("input-time");
        const inputNumber = document.getElementById("input-number");
        const inputCourse = document.getElementById("input-course");

        const previewDate = document.getElementById("preview-date");
        const previewTime = document.getElementById("preview-time");
        const previewNumber = document.getElementById("preview-number");
        const previewCourse = document.getElementById("preview-course");
        const previewPrice = document.getElementById("preview-price");

        if (!inputDate || !inputTime) return;

        // YYYY-MM-DD（今日）
        const todayStr = () => {
            const d = new Date();
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, "0");
            const day = String(d.getDate()).padStart(2, "0");
            return `${y}-${m}-${day}`;
        };

        // 次の30分（例 14:01→14:30 / 14:31→15:00）
        const next30Minutes = () => {
            const d = new Date();
            let h = d.getHours();
            let m = d.getMinutes();

            if (m === 0) m = 0;
            else if (m <= 30) m = 30;
            else {
                h = (h + 1) % 24;
                m = 0;
            }

            return h * 60 + m;
        };

        // "HH:MM" → 分
        const toMinutes = (t) => {
            if (!t || !t.includes(":")) return 0;
            const [h, m] = t.split(":").map(Number);
            return h * 60 + m;
        };

        // 全 option を保存（復元用）
        const allTimeOptions = [...inputTime.options].map(opt => opt.cloneNode(true));

        //選択を“維持できるなら維持”
        const updateTimeOptions = () => {
            const isToday = inputDate.value === todayStr();
            const threshold = next30Minutes();

            const prev = inputTime.value; // 直前の選択を覚える
            inputTime.innerHTML = "";

            const restored = isToday ?
                allTimeOptions.filter(opt => toMinutes(opt.value) >= threshold) :
                allTimeOptions;

            restored.forEach(opt => inputTime.appendChild(opt.cloneNode(true)));

            if (inputTime.options.length === 0) {
                const opt = document.createElement("option");
                opt.value = "";
                opt.textContent = "選択できる時間がありません";
                inputTime.appendChild(opt);
            }

            // ✅ 以前の選択が残っていれば維持、なければ先頭
            const canKeep = [...inputTime.options].some(o => o.value === prev);
            inputTime.value = canKeep ? prev : (inputTime.options[0]?.value ?? "");

            if (previewTime) previewTime.textContent = inputTime.value || "--:--";
        };

        const updateCoursePreview = () => {
            if (!inputCourse || !previewCourse || !previewPrice) return;

            const opt = inputCourse.options[inputCourse.selectedIndex];
            const hasCourse = !!inputCourse.value;

            if (!hasCourse || !opt) {
                previewCourse.textContent = "未設定";
                previewPrice.textContent = "-";
                return;
            }

            const name = opt.dataset?.name || "未設定";
            const price = opt.dataset?.price;

            previewCourse.textContent = name;
            previewPrice.textContent = price ?
                `${Number(price).toLocaleString()}円` :
                "-";
        };

        // 初期表示
        if (previewDate) previewDate.textContent = inputDate.value;
        if (previewNumber && inputNumber) previewNumber.textContent = inputNumber.value + "人";

        updateTimeOptions();
        updateCoursePreview();

        // 日付変更
        inputDate.addEventListener("change", () => {
            if (previewDate) previewDate.textContent = inputDate.value;
            updateTimeOptions();
        });

        // 時刻変更
        inputTime.addEventListener("change", () => {
            if (previewTime) previewTime.textContent = inputTime.value || "--:--";
        });

        // 人数変更
        if (inputNumber) {
            inputNumber.addEventListener("change", () => {
                if (previewNumber) previewNumber.textContent = inputNumber.value + "人";
            });
        }

        // コース変更
        if (inputCourse) {
            inputCourse.addEventListener("change", updateCoursePreview);
        }
    });
</script>
@endsection