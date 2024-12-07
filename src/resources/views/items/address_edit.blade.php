@extends('/layouts.common')
@section('css')
    
@endsection
@section('title','住所の変更')

@section('content')
    <div class="wrapper">
        <h1>住所の変更</h1>
        <form class="form">
            {{--郵便番号--}}
            <div class="form__item">
                <div class="form__item-label">郵便番号</div>
                <input class="form__item-input" type="text" name="postal_code" value="{{$address->postal_code}}">
            </div>
            {{--住所--}}
            <div class="form__item">
                <div class="form__item-label">住所</div>
                <input class="form__item-input" type="text" name="address" value="{{$address->address}}">
            </div>
            {{--建物名--}}
            <div class="form__item">
                <div class="form__item-label">建物名</div>
                <input class="form__item-input" type="text" name="building" value="{{$address->building}}">
            </div>
            {{--登録実行--}}
            <button class="form__submit" type="submit">更新する</button>
        </form>
    </div>
@endsection