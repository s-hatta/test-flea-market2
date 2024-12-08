@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/items/item_detail.css')}}">
@endsection

@section('content')
<div class="wrapper">
    {{-- 商品イメージ --}}
    <div class="item-image">
        <img src="{{ asset('storage/images/items/'.$item->img_url) }}" alt="商品画像">
    </div>
    {{-- 商品情報 --}}
    <div class="item-info">
        {{-- 商品名 --}}
        <h1>{{$item->name}}</h1>
        {{-- ブランド名 --}}
        <p class="brand-name">{{$item->brand_name}}</p>
        {{-- 価格 --}}
        <p class="price">¥{{ number_format($item->price) }} (税込)</p>
        {{-- いいね数とコメント数 --}}
        <div class="rating">
            <table>
                <tr>
                    <th><img src="{{ asset('images/icons/icon_like.png') }}" alt="いいね" id="like-icon" onclick="toggleLike({{ $item->id }})"></th>
                    <th><img src="{{ asset('images/icons/icon_comment.png') }}" alt="コメント"></th>
                </tr>
                <tr>
                    <td id="like-count">{{$likeNum}}</td>
                    <td>{{count($comments)}}</td>
                </tr>
            </table>
        </div>
        {{-- 購入ボタン --}}
        <button class="purchase-button" type="button" onclick="location.href='{{ url('/purchase/'.$item->id) }}'">
            購入手続きへ
        </button>
        {{-- 商品説明 --}}
        <div class="item-description">
            <h2>商品説明</h2>
            <p>{{$item->detail}}</p>
        </div>
        {{-- 商品詳細 --}}
        <div class="item-details">
            <h2>商品の情報</h2>
            <table>
                <tr>
                    <th>カテゴリ</th>
                    <td>
                        @foreach($item->categories as $category)
                            <span class="category">{{$category->content}}</span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>商品の状態</th>
                    <td>
                        <span class="condition">{{$condition->condition}}</span></p>
                    </td>
                </tr>
            </table>
        </div>
        {{-- コメント --}}
        <div class="comments-section">
            <h2>コメント ({{count($comments)}})</h2>
            {{-- コメント一覧 --}}
            <div class="comment">
                @foreach($item->comments as $comment)
                    <p class="comment-author">{{ $comment->user->name }}</p>
                    <p class="comment-text">{{ $comment->comment }}</p>
                @endforeach
            </div>
            {{-- コメント入力 --}}
            <div class="add-comment">
                <form method="POST" action="{{ route('comments.store', $item->id) }}">
                    @csrf
                    <h2>商品へのコメント</h2>
                    <textarea name="comment" placeholder="コメントを入力する"></textarea>
                    <button class="submit-comment">コメントを送信する</button>
                    {{$errors->first('comment')}}
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 押下するごとにいいねの登録／解除をおこなう --}}
<script>
    function toggleLike(itemId) {
        fetch(`/item/${itemId}/toggle-like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('like-count').innerText = data.likeNum;
        });
    }
</script>