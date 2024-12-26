<header class="header">
    <div class="header__inner">
        <div class="header-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('logo.svg') }}" alt="COACHTECH">
            </a>
        </div>
        
        @if (!request()->is('login') && !request()->is('register') && !request()->is('email/verify'))
        <div class="header-search">
            <form method="POST" action="{{ url('/') }}">
                @csrf
                <input class="header-search__input" type="text" name="item_name" value="{{isset($itemName)?$itemName:''}}" placeholder="なにをお探しですか？">
            </form>
        </div>

        <!--ハンバーガーメニュー用チェックボックス-->
        <input type="checkbox" id="menu-toggle" class="menu-toggle">
        <label for="menu-toggle" class="menu-button">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <nav class="header-nav">
            @auth
                <form method="POST" action="{{ url('/logout') }}" class="header-nav__item">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
            @else
                <div class="header-nav__item">
                    <button type="button" onclick="location.href='{{ url('/login') }}'">ログイン</button>
                </div>
            @endauth
            
            <div class="header-nav__item">
                <button type="button" onclick="location.href='{{ url('/mypage') }}'">マイページ</button>
            </div>
            
            <div class="header-nav__item">
                <button type="button" class="exhibition-button" onclick="location.href='{{ url('/sell') }}'">出品</button>
            </div>
        </nav>
        @endif
    </div>
</header>