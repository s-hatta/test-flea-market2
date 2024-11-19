@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection
@section('title','ユーザー登録')

@section('content')
    <div class="wrapper">
        {{--会員登録フォーム--}}
        <h1>会員登録</h1>
        <form class="form" method="POST" action="{{route('register')}}">
            @csrf
            {{--ユーザー名--}}
            <div class="form__item">
                <div class="form__item-label">ユーザー名 </div>
                <input class="form__item-input" type="text" name="name">
            </div>
            {{--メールアドレス--}}
            <div class="form__item">
                <div class="form__item-label">メールアドレス</div>
                <input class="form__item-input" type="text" name="email">
            </div>
            {{--パスワード--}}
            <div class="form__item">
                <div class="form__item-label">パスワード</div>
                <input class="form__item-input" type="password" name="password">
            </div>
            {{--確認用パスワード--}}
            <div class="form__item">
                <div class="form__item-label">確認用パスワード</div>
                <input class="form__item-input" type="pssword" name="password_confirmation">
            </div>
            {{--登録実行--}}
            <button class="form__submit" type="submit">登録する</button>
        </form>
        {{--ログイン画面へ遷移--}}
        <div class="link">
            <a href="{{'login'}}">ログインはこちら</a>
        </div>
    </div>
@endsection