@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/item_purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-details">
        <div class="item-info-container">
            <div class="item-image">
                <img src="{{ Storage::url('public/images/items/' . $item->img_url) }}" alt="{{ $item->name }}">
            </div>
            <div class="item-info">
                <h2>{{ $item->name }}</h2>
                <p class="price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>
        <div class="payment-method">
            <label for="payment">支払い方法</label>
            <select id="payment" name="payment" onchange="displayPaymentMethod()">
                <option value="">選択してください</option>
                <option value="cvs">コンビニ払い</option>
                <option value="card">カード払い</option>
            </select>
        </div>
        <div class="shipping-address">
            <h3>配送先</h3>
            <p>〒{{ $address->postal_code }}</p>
            <p>{{ $address->address }} {{ $address->building }}</p>
            <a href="{{ url('/purchase/address/' . $item->id) }}" class="change-address">変更する</a>
        </div>
    </div>
    <div class="purchase-summary">
        <table>
            <tr>
                <th>商品代金</th>
                <td>¥{{ number_format($item->price) }}</td>
            </tr>
            <tr>
                <th>支払い方法</th>
                <td id="selected-payment">選択してください</td>
            </tr>
        </table>
        <form method="POST" action="{{ url('/purchase/'.$item->id) }}">
            @csrf
            <button type="submit" class="purchase-button">購入する</button>
        </form>
    </div>
</div>

<script>
    function displayPaymentMethod() {
        var paymentMethod = document.getElementById('payment').value;
        var selectedPayment = document.getElementById('selected-payment');
        if( paymentMethod === 'cvs') {
            selectedPayment.innerText = 'コンビニ払い';
        } else if (paymentMethod === 'card') {
            selectedPayment.innerText = 'カード払い';
        } else {
            selectedPayment.innerText = '選択してください';
        }
    }
</script>
@endsection
