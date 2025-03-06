@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/transaction/transaction_detail.css')}}">
@endsection
@section('title','取引画面')

@section('content')
<div class="container">
    {{-- サイドバー --}}
    <div class="sidebar">
        <div class="sidebar-title">その他の取引</div>
        @foreach( $otherTransactions as $otherTransaction )
            <div class="sidebar-transaction">
                <a href="{{ url('/transaction/' . $otherTransaction->id) }}" class="sidebar-transaction__link">{{ $otherTransaction->item->name }}</a>
            </div>
        @endforeach
    </div>

    {{--取引チャット--}}
    <div class="main-content">

        {{--ヘッダ--}}
        <div class="chat-header">
            <div class="user-image-wrapper">
                @if($otherUser->img_url)
                    <img src="{{ Storage::url('public/images/user/' . $otherUser->img_url) }}" class="user-image">
                @else
                    <div class="user-image__placeholder"></div>
                @endif
            </div>
            <div class="chat-title">{{$otherUser->name}}さんとの取引画面</div>
            @if( Auth::id() === $transaction->buyer_id )
                <form>
                    <button type="submit" class="complete-btn">取引を完了する</button>
                </form>
            @endif
        </div>

        {{--商品情報--}}
        <div class="item-info">
            <div class="item-image-wrapper">
                <img src="{{ Storage::url('public/images/items/' . $transaction->item->img_url) }}" class="item-image">
            </div>
            <div class="item-details">
                <div class="item-name">{{ $transaction->item->name }}</div>
                <div class="item-price">¥{{ number_format($transaction->item->price) }}</div>
            </div>
        </div>

        {{--送受信メッセージ--}}
        <div class="messages">
            @foreach($messages as $message)
                <div class="message-wrapper">
                    @if( $message->user->id===Auth::id() )
                        {{--送信メッセージ--}}
                        <div class="message-name">{{ $message->user->name }}</div>
                        <div class="message-image-wrapper">
                            @if($message->user->img_url)
                                <img src="{{ Storage::url('public/images/user/' . $message->user->img_url) }}" class="message-image">
                            @else
                                <div class="message-image__placeholder"></div>
                            @endif
                        </div>
                        <div class="message-content">{{ $message->content }}</div>
                        編集
                        削除
                    @else
                        {{--受信メッセージ--}}
                        <div class="message-image-wrapper">
                            @if($message->user->img_url)
                                <img src="{{ Storage::url('public/images/user/' . $message->user->img_url) }}" class="message-image">
                            @else
                                <div class="message-image__placeholder"></div>
                            @endif
                        </div>
                        <div class="message-name">{{ $message->user->name }}</div>
                        <div class="message-content">{{ $message->content }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        {{--メッセージ入力欄--}}
        <div class="message-input">
            @if ($errors->any())
                <div class="alert-error">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            <form class="input-form" action="{{ url('/transaction/' . $transaction->id . '/message') }}" method="POST">
                @csrf
                <textarea class="input-content" name="content" id="input-content" placeholder="取引メッセージを記入してください" rows="2">{{ old('content') }}</textarea>
                <label class="select-image">
                    <input type="file" name="image" id="item_image-input" accept="image/*" hidden>
                    画像を追加
                </label>
                <input type="image" src="{{ asset('images/icons/icon_paper_airplane.svg') }}" alt="送信">
            </form>
        </div>
        <div class="preview-image">
            <img id="preview-image">
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('item_image-input');
    let previewImage = document.getElementById('preview-image');

    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
