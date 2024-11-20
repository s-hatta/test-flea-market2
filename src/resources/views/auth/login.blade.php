@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection

@section('content')
    <div class="wrapper">
        {{-- ログインフォーム --}}
        <h1>ログイン</h1>
        <form class="form" method="POST" action="{{route('login')}}">
            @csrf
            {{-- ユーザー名もしくはメールアドレス --}}
            <div class="form__item">
                <div class="form__item-label">ユーザー名 / メールアドレス</div>
                <input class="form__item-input" type="text" name="email">
            </div>
            {{--パスワード--}}
            <div class="form__item">
                <div class="form__item-label">パスワード</div>
                <input class="form__item-input" type="password" name="password">
            </div>
            {{--ログイン実行--}}
            <button class="form__submit" type="submit">ログインする</button>
        </form>
        
        {{-- 会員登録画面へ遷移 --}}
        <div class="link">
            <a href="{{'register'}}">会員登録はこちら</a>
        </div>
    </div>
@endsection