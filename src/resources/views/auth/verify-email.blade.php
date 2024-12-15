@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection
@section('title','メール')

@section('content')
<div class="wrapper">
    <h1>メールアドレスの確認</h1>

    <div class="form__item">
        <div class="form__item">
            <div class="form__item-label">
                メールアドレスの確認が必要です。
            </div>
        </div>
        <form class="form" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="form__submit">
                確認メールを再送信する
            </button>
        </form>
    </div>
</div>
@endsection