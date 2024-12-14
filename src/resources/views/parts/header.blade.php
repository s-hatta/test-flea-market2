<header class="header">
    <div class="header-logo">
        <a class="header-logo__inner" href="{{ url('/') }}">
            <img src={{ asset('logo.svg') }} alt="COACHTECH">
        </a>
    </div>
    
    @if (!request()->is('login') && !request()->is('register'))
    <div class="header-search">
        <form method="POST" action="{{ url('/') }}">
            @csrf
            <input class="header-search__input" type="text" name="item_name" value="{{isset($itemName)?$itemName:''}}" placeholder="なにをお探しですか？">
        </form>
    </div>
    <div class="header-nav">
        @if (Auth::check())
        <div class="header-nav__logout">
            <form method="POST" action="{{ url('logout') }}">
                @csrf
                <button class="header-nav__logout-button" type="submit">ログアウト</button>
            </form>
        </div>
        @else
        <div class="header-nav__login">
            <button class="header-nav__login-button" type="button" onclick="location.href='{{ url('login') }}'">
                ログイン
            </button>
        </div>
        @endif
        <div class="header-nav__mypage">
            <button class="header-nav__mypage-button" type="button" onclick="location.href='{{ url('/mypage') }}'">
                マイページ
            </button>
        </div>
        <div class="header-nav__exhibition">
            <button class="header-nav__exhibiton-button" type="button" onclick="location.href='{{ url('/sell') }}'">
                出品
            </button>
        </div>
    </div>
    @endif
</header>
