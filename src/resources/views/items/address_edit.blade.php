@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/items/address_edit.css')}}">
@endsection
@section('title','住所の変更')

@section('content')
    <div class="wrapper">
        <h1>住所の変更</h1>
        <form class="form" method="POST" action="{{ url('/purchase/address/' . $item->id) }}">
            @csrf
            {{--郵便番号--}}
            <div class="form__item">
                <div class="form__item-label">郵便番号</div>
                <input class="form__item-input" type="text" name="postal_code" value="{{$address->postal_code}}">
                <div class="form__item-alert">{{$errors->first('postal_code')}}</div>
            </div>
            {{--住所--}}
            <div class="form__item">
                <div class="form__item-label">住所</div>
                <input class="form__item-input" type="text" name="address" value="{{$address->address}}">
                <div class="form__item-alert">{{$errors->first('address')}}</div>
            </div>
            {{--建物名--}}
            <div class="form__item">
                <div class="form__item-label">建物名</div>
                <input class="form__item-input" type="text" name="building" value="{{$address->building}}">
            </div>
            {{--登録実行--}}
            <input type="hidden" name="name" value={{Auth::user()->name}}>
            <button class="form__submit" type="submit">更新する</button>
        </form>
    </div>
@endsection