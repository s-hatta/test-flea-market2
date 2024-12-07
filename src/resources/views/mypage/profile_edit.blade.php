@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection
@section('title','プロフィール設定')

@section('content')
    <div class="wrapper">
        <h1>プロフィール設定</h1>
        <form class="form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            {{--プロフィール画像--}}
            <div class="form__item">
                <div class="form__item-label">プロフィール画像</div>
                <input class="form__item-input" type="file" name="profile_image">
            </div>
            {{--ユーザー名--}}
            <div class="form__item">
                <div class="form__item-label">ユーザー名</div>
                <input class="form__item-input" type="text" name="name" value="{{ $user->name }}">
            </div>
            {{--郵便番号--}}
            <div class="form__item">
                <div class="form__item-label">郵便番号</div>
                <input class="form__item-input" type="text" name="postal_code" value="{{ $user->address->postal_code }}">
            </div>
            {{--住所--}}
            <div class="form__item">
                <div class="form__item-label">住所</div>
                <input class="form__item-input" type="text" name="address" value="{{ $user->address->address }}">
            </div>
            {{--建物名--}}
            <div class="form__item">
                <div class="form__item-label">建物名</div>
                <input class="form__item-input" type="text" name="building" value="{{ $user->address->building }}">
            </div>
            {{--登録実行--}}
            <button class="form__submit" type="submit">更新する</button>
        </form>
    </div>
@endsection