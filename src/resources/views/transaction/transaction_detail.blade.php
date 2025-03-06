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
                <form action="{{ url('/transaction/' . $transaction->id . '/complete') }}" method="POST" onsubmit="return confirm('この取引を完了しますか？');">
                    @csrf
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
                        <div class="message-content">
                            @if($message->image_url)
                                <img src="{{ asset('storage/images/messages/' . $message->image_url) }}">
                            @endif
                            {{ $message->content }}
                        </div>
                        編集
                        <form method="POST" action="{{ route('transaction.message.delete', ['id' => $transaction->id, 'messageId' => $message->id]) }}" onsubmit="return confirm('このメッセージを削除してもよろしいですか？');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-message-btn">削除</button>
                        </form>
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
                        <div class="message-content">
                            @if($message->image_url)
                                <img src="{{ asset('storage/images/messages/' . $message->image_url) }}">
                            @endif
                            {{ $message->content }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{--メッセージ入力欄--}}
        <div class="message-input">
            @if ($errors->any())
                <div class="alert-error">
                    @foreach ($errors->all() as $error)
                        <ul>{{ $error }}</ul>
                    @endforeach
                </div>
            @endif
            <form class="input-form" action="{{ url('/transaction/' . $transaction->id . '/message') }}" method="POST" enctype="multipart/form-data">
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

{{--取引完了モーダル--}}
@if($transaction->isCompleted() && !$hasRated)
<div class="rating-modal" id="rating-modal">
    <div class="rating-modal-content">
        <div class="rating-modal-header">
            取引が完了しました。
        </div>
        <div class="rating-modal-body">
            <p>今回の取引相手はどうでしたか？</p>
            <form action="{{ url('/transaction/' . $transaction->id . '/rate') }}" method="POST" class="rating-stars-form">
                @csrf
                <div class="star-rating">
                    <input type="radio" id="star1" name="score" value="1">
                    <label for="star1" class="star"></label>
                    <input type="radio" id="star2" name="score" value="2">
                    <label for="star2" class="star"></label>
                    <input type="radio" id="star3" name="score" value="3" checked>
                    <label for="star3" class="star"></label>
                    <input type="radio" id="star4" name="score" value="4">
                    <label for="star4" class="star"></label>
                    <input type="radio" id="star5" name="score" value="5">
                    <label for="star5" class="star"></label>
                </div>
                <div class="rating-submit">
                    <button type="submit" class="rating-submit-btn">送信する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('item_image-input');
    const messageInput = document.getElementById('input-content');

    /* URLから取引IDを取得 */
    const transactionId = window.location.pathname.split('/').pop();

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

    /* ローカルストレージから保存されたメッセージを読み込む */
    if (messageInput) {
        const savedMessage = localStorage.getItem(`transaction_message_${transactionId}`);
        if (savedMessage) {
            messageInput.value = savedMessage;
        }

        /* 入力内容の変更を監視して保存 */
        messageInput.addEventListener('input', function() {
            localStorage.setItem(`transaction_message_${transactionId}`, this.value);
        });
    }

    /* フォーム送信時にローカルストレージをクリア */
    const messageForm = document.querySelector('.input-form');
    if (messageForm) {
        messageForm.addEventListener('submit', function() {
            localStorage.removeItem(`transaction_message_${transactionId}`);
        });
    }

    /* 取引完了モーダル */
    const ratingModal = document.getElementById('rating-modal');
    if (ratingModal) {

        /* 評価の入力と表示 */
        const stars = document.querySelectorAll('.star-rating input');
        stars.forEach(star => {
            star.addEventListener('change', function() {
                const rating = this.value;
                stars.forEach(s => {
                    const sRating = s.value;
                    const label = document.querySelector(`label[for="star${sRating}"]`);
                    if (sRating <= rating) {
                        label.classList.add('active');
                    } else {
                        label.classList.remove('active');
                    }
                });
            });
        });

        /* 初期表示は「3」 */
        document.querySelector('label[for="star3"]').classList.add('active');
        document.querySelector('label[for="star2"]').classList.add('active');
        document.querySelector('label[for="star1"]').classList.add('active');

        /* ボタンイベント */
        const showRatingModalBtn = document.getElementById('show-rating-modal');
        if (showRatingModalBtn) {
            showRatingModalBtn.addEventListener('click', function() {
                ratingModal.style.display = 'flex';
            });
        }
    }
});
</script>
