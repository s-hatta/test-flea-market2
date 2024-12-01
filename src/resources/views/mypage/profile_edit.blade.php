@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection
@section('title','プロフィール設定')

@section('content')
    <div class="wrapper">
        <h1>プロフィール設定</h1>
        <form class="form">
            {{--ユーザー名--}}
            <div class="form__item">
                <div class="form__item-label">ユーザー名</div>
                <input class="form__item-input" type="text" name="name" value="{{ $user->name }}">
            </div>
            {{--郵便番号--}}
            <div class="form__item">
                <div class="form__item-label">郵便番号</div>
                <input class="form__item-input" type="text" name="postal_code" value="{{ $user->postal_code }}">
            </div>
            {{--住所--}}
            <div class="form__item">
                <div class="form__item-label">住所</div>
                <input class="form__item-input" type="text" name="address" value="{{ $user->address }}">
            </div>
            {{--建物名--}}
            <div class="form__item">
                <div class="form__item-label">建物名</div>
                <input class="form__item-input" type="text" name="building" value="{{ $user->building }}">
            </div>
            {{--登録実行--}}
            <button class="form__submit" type="submit">更新する</button>
        </form>
    </div>
@endsection