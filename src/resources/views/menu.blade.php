<div class="menu-overlay js-menu-overlay"
    role="dialog"
    aria-modal="true"
    aria-hidden="true">
    <div class="menu-box" role="document">

        {{-- 閉じるボタン（JSで閉じる） --}}
        <button type="button"
            class="close-btn js-menu-close"
            aria-label="メニューを閉じる">
        </button>

        @guest
        {{-- 未ログイン（ゲスト） --}}
        <a href="{{ route('shops.index') }}" class="menu-item">Home</a>
        <a href="{{ route('register') }}" class="menu-item">Registration</a>
        <a href="{{ route('login') }}" class="menu-item">Login</a>
        @endguest

        @auth
        {{-- ログイン中 --}}
        <a href="{{ route('shops.index') }}" class="menu-item">Home</a>
        <a href="{{ route('mypage') }}" class="menu-item">Mypage</a>

        <form action="{{ route('logout') }}" method="POST" class="menu-form">
            @csrf
            <button type="submit" class="menu-item logout-btn">Logout</button>
        </form>
        @endauth

    </div>
</div>