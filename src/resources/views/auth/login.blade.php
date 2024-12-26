@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection
@section('title','ログイン')

@section('content')
    <div class="wrapper">
        {{-- ログインフォーム --}}
        <h1>ログイン</h1>
        @if(isset($message))
            <div class="form__item-alert">{{$message}}</div>
        @endif
        <form class="form" method="POST" action="{{url('login')}}">
            @csrf
            {{-- メールアドレス --}}
            <div class="form__item">
                <div class="form__item-label">メールアドレス</div>
                <input class="form__item-input" type="text" name="email">
                <div class="form__item-alert">{{$errors->first('email')}}</div>
            </div>
            {{--パスワード--}}
            <div class="form__item">
                <div class="form__item-label">パスワード</div>
                <input class="form__item-input" type="password" name="password">
                <div class="form__item-alert">{{$errors->first('password')}}</div>
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
