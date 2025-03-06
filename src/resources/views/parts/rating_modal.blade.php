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
