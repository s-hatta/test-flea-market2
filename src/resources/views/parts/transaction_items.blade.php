<div class="item-container">
    @foreach($transactions as $transaction)
        @php
            $unreadMessagesCount = $transaction->unreadMessagesCount(Auth::id());
        @endphp
        <div class="item-card">
            <a href="{{ url('/transaction/' . $transaction->id) }}">
                <div class="item-image-wrapper">
                    <img src="{{ Storage::url('public/images/items/' . $transaction->item->img_url) }}" alt="商品画像" class="item-image">
                    @if($unreadMessagesCount > 0)
                        <div class="unread-badge">{{$unreadMessagesCount}}</div>
                    @endif
                </div>
            </a>
            <p class="item-name">{{ $transaction->item->name }}</p>
        </div>
    @endforeach
</div>
