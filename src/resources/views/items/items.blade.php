@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/items.css') }}">
    <link rel="stylesheet" href="{{ asset('css/parts/items.css') }}">
@endsection
@section('title','トップページ')

@section('content')
<div class="tab-container">
    <button class="tab-button {{ request()->query('tab') !== 'mylist' ? 'active' : '' }}" type="button" onclick="location.href='{{ url('/') }}'">
        おすすめ
    </button>
    <button class="tab-button {{ request()->query('tab') === 'mylist' ? 'active' : '' }}" type="button" onclick="location.href='{{ url('/?tab=mylist') }}'">
        マイリスト
    </button>
</div>
@include('parts.items', ['items' => $items])
@endsection
