@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/items/item_detail.css')}}">
@endsection

@section('content')
<div class="item-detail">
    <div class="item-image">
        <img src="{{ asset('storage/images/items/'.$item->img_url) }}" alt="商品画像">
    </div>
    <div class="item-info">
        <h1>{{$item->name}}</h1>
        <p class="brand-name">ブランド名</p>
        <p class="price">¥{{ number_format($item->price) }} (税込)</p>
        <div class="rating">
            <span>☆</span> <span>{{count($likes)}}</span> <span>💬</span> <span>{{count($comments)}}</span>
        </div>
        <button class="purchase-button" type="button" onclick="location.href='{{ url('/purchase/'.$item->id) }}'">
            購入手続きへ
        </button>
        <div class="item-description">
            <h2>商品説明</h2>
            <p>{{$item->detail}}</p>
        </div>
        <div class="item-details">
            <h2>商品の情報</h2>
            <p>カテゴリー: <span class="category">洋服</span> <span class="subcategory">メンズ</span></p>
            <p>商品の状態: <span class="condition">{{$condition->condition}}</span></p>
        </div>
        <div class="comments-section">
            <h2>コメント ({{count($comments)}})</h2>
            <div class="comment">
                <p class="comment-author">admin</p>
                <p class="comment-text">こちらにコメントが入ります。</p>
            </div>
            <div class="add-comment">
                <form method="POST" action="{{ route('comments.store', $item->id) }}">
                    @csrf
                    <h2>商品へのコメント</h2>
                    <textarea name="comment" placeholder="コメントを入力する"></textarea>
                    <button class="submit-comment">コメントを送信する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
