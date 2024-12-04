@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/items.css') }}">
@endsection

@section('content')
<div class="tab-container">
    <button class="tab-button" type="button" onclick="location.href='{{ url('/') }}'">
        おすすめ
    </button>
    <button class="tab-button" type="button" onclick="location.href='{{ url('/?tab=mylist') }}'">
        マイリスト
    </button>
</div>
@include('parts.items', ['items' => $items])
@endsection
