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
                @if($message->user->id === Auth::id())
                    {{-- 送信メッセージ（右側表示） --}}
                    <div class="message-wrapper message-right" id="message-{{ $message->id }}">
                        <div class="message-right-container">
                            <div class="user-info-right">
                                <span class="message-name">{{ $message->user->name }}</span>
                                <div class="message-image-wrapper">
                                    @if($message->user->img_url)
                                        <img src="{{ asset('storage/images/users/' . $message->user->img_url) }}" class="message-image">
                                    @else
                                        <div class="message-image__placeholder"></div>
                                    @endif
                                </div>
                            </div>
                            <div class="message-content">
                                @if($message->image_url)
                                    <img src="{{ asset('storage/images/messages/' . $message->image_url) }}" class="message-image-content">
                                @endif
                                <span class="message-text">{{ $message->content }}</span>
                            </div>
                            <div class="message-actions">
                                <button type="button" class="edit-message-btn" onclick="showEditForm('{{ $message->id }}')">編集</button>
                                <form method="POST" action="{{ route('transaction.message.delete', ['id' => $transaction->id, 'messageId' => $message->id]) }}" onsubmit="return confirm('このメッセージを削除してもよろしいですか？');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-message-btn">削除</button>
                                </form>
                            </div>

                            {{--編集フォーム--}}
                            <div class="edit-form" id="edit-form-{{ $message->id }}" style="display: none;">
                                <form method="POST" action="{{ route('transaction.message.update', ['id' => $transaction->id, 'messageId' => $message->id]) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="content" class="edit-textarea" rows="3">{{ $message->content }}</textarea>
                                    <div class="edit-actions">
                                        <button type="submit" class="update-btn">更新</button>
                                        <button type="button" class="cancel-btn" onclick="hideEditForm('{{ $message->id }}')">キャンセル</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- 受信メッセージ（左側表示） --}}
                    <div class="message-wrapper message-left" id="message-{{ $message->id }}">
                        <div class="message-left-container">
                            <div class="user-info-left">
                                <div class="message-image-wrapper">
                                    @if($message->user->img_url)
                                        <img src="{{ asset('storage/images/users/' . $message->user->img_url) }}" class="message-image">
                                    @else
                                        <div class="message-image__placeholder"></div>
                                    @endif
                                </div>
                                <span class="message-name">{{ $message->user->name }}</span>
                            </div>
                            <div class="message-content">
                                @if($message->image_url)
                                    <img src="{{ asset('storage/images/messages/' . $message->image_url) }}" class="message-image-content">
                                @endif
                                <span class="message-text">{{ $message->content }}</span>
                            </div>
                        </div>
                    </div>
                @endif
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
@include('parts.rating_modal', ['transaction' => $transaction, 'hasRated' => $hasRated])
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
                previewImage.style.display = 'block';
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

/* 編集フォーム表示 */
function showEditForm(messageId) {
    const messageWrapper = document.querySelector(`#message-${messageId}`);
    messageWrapper.classList.add('editing');
    document.querySelector(`#message-${messageId} .user-info-right`).style.display = 'none';
    document.querySelector(`#message-${messageId} .message-content`).style.display = 'none';
    document.querySelector(`#message-${messageId} .message-actions`).style.display = 'none';
    document.getElementById(`edit-form-${messageId}`).style.display = 'block';
}

/* 編集フォーム非表示 */
function hideEditForm(messageId) {
    const messageWrapper = document.querySelector(`#message-${messageId}`);
    messageWrapper.classList.remove('editing');
    document.querySelector(`#message-${messageId} .user-info-right`).style.display = 'flex';
    document.querySelector(`#message-${messageId} .message-content`).style.display = 'block';
    document.querySelector(`#message-${messageId} .message-actions`).style.display = 'flex';
    document.getElementById(`edit-form-${messageId}`).style.display = 'none';
}
</script>
