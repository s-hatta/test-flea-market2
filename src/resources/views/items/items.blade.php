@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/items.css') }}">
@endsection

@section('content')
<div class="item-container">
    @foreach($items as $item)
        <div class="item-card">
            <a href="{{ url('/item/' . $item->id) }}"> 
                <img src="{{ Storage::url('public/images/items/' . $item->img_url) }}" alt="商品画像" class="item-image">
            </a>
            <p class="item-name">{{ $item->name }}</p>
        </div>
    @endforeach
</div>
@endsection
