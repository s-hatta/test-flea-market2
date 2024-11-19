<header class="header">
    <div class="header-logo">
        <a class="header-logo__inner" href="{{ url('/') }}">
            <img src="logo.svg" alt="COACHTECH">
        </a>
    </div>
    
    @if (!request()->is('login') && !request()->is('register'))
    <div class="header-search">
        <input class="header-search__input" type="text" placeholder="なにをお探しですか？">
    </div>
    <div class="header-nav">
        @if (Auth::check())
        <div class="header-nav__logout">
            ログアウト
        </div>
        @else
        <div class="header-nav__login">
            ログイン
        </div>
        @endif
        <div class="header-nav__mypage">
            マイページ
        </div>
        <div class="header-nav__exhibition">
            <button class="header-nav__exhibiton-button">
                出品
            </button>
        </div>
    </div>
    @endif
</header>