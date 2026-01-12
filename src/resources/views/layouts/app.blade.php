<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ asset('css/base/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/menu.css') }}">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />

    @yield('css')
</head>

<body>
    @php
    /**
    * ロゴ・メニュー非表示の自動判定
    * - admin.* / owner.* のルートでは自動で非表示
    * - Blade 側で $hideLogo が渡されたらそれを最優先
    */
    $hideLogoAuto = request()->routeIs('admin.*') || request()->routeIs('owner.*');
    $hideLogoFinal = isset($hideLogo) ? (bool) $hideLogo : $hideLogoAuto;
    @endphp

    {{-- ヘッダー --}}
    <header class="site-header">
        <div class="site-header-row">

            {{-- 左：ロゴ＋メニュー（admin / owner では非表示） --}}
            @unless($hideLogoFinal)
            <div class="site-header-left">
                <button
                    type="button"
                    class="logo-link js-menu-open"
                    aria-label="メニューを開く">
                    <span class="logo-icon" aria-hidden="true">
                        <span class="logo-lines">
                            <span class="bottom-line"></span>
                        </span>
                    </span>
                </button>
                <span class="logo-text">Rese</span>
            </div>
            @endunless

            {{-- 右：ページごとに差し替え（ログアウト等） --}}
            <div class="site-header-right">
                @yield('header-right')
            </div>

        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- モーダルメニュー（ロゴを出す画面のみ） --}}
    @unless($hideLogoFinal)
    @include('menu')
    @endunless

    {{-- メニュー開閉JS（ロゴを出す画面のみ） --}}
    @unless($hideLogoFinal)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuOverlay = document.querySelector('.menu-overlay');
            const openBtn = document.querySelector('.js-menu-open');
            const closeBtn = document.querySelector('.js-menu-close');

            if (!menuOverlay || !openBtn || !closeBtn) return;

            // 開く
            openBtn.addEventListener('click', function() {
                menuOverlay.classList.add('is-open');
            });

            // 閉じる（×）
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                menuOverlay.classList.remove('is-open');
            });

            // 黒背景クリックで閉じる
            menuOverlay.addEventListener('click', function(e) {
                if (e.target === menuOverlay) {
                    menuOverlay.classList.remove('is-open');
                }
            });
        });
    </script>
    @endunless

    @stack('scripts')
</body>

</html>