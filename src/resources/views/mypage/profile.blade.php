@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/parts/items.css') }}">
@endsection
@section('title','マイページ')

@section('content')
<div class="container">
    {{--プロフィール--}}
    <div class="profile-header">
        {{--プロフィール画像--}}
        <div class="profile-image">
            @if(Auth::user()->img_url)
                <img src="{{ asset('storage/images/users/'.Auth::user()->img_url) }}" alt="プロフィール画像">
            @else
                <div class="profile-image__placeholder"></div>
            @endif
        </div>
        {{--ユーザー名--}}
        <h2 class="profile-name">{{ Auth::user()->name }}</h2>
        {{--プロフィール編集ボタン--}}
        <a href="{{ url('/mypage/profile') }}" class="edit-profile-button">プロフィールを編集</a>
    </div>
    {{--表示切替用のタブ--}}
    <div class="profile-tabs">
        <button class="tab-button {{ request()->query('tab') === 'sell' || !request()->query('tab') ? 'active' : '' }}" type="button" onclick="location.href='{{ url('/mypage/?tab=sell') }}'">
            出品した商品
        </button>
        <button class="tab-button {{ request()->query('tab') === 'buy' ? 'active' : '' }}" type="button" onclick="location.href='{{ url('/mypage/?tab=buy') }}'">
            購入した商品
        </button>
        <button class="tab-button {{ request()->query('tab') === 'transaction' ? 'active' : '' }}" type="button" onclick="location.href='{{ url('/mypage/?tab=transaction') }}'">
            取引中
        </button>
    </div>
    @if( isset($transactions) )
        {{--取引中の商品一覧--}}
        @include('parts.transaction_items', ['transactions' => $transactions])
    @else
        {{--商品一覧--}}
        @include('parts.items', ['items' => $items])
    @endif
</div>
@endsection
