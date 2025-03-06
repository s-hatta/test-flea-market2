<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>取引完了のお知らせ</title>
    <style>
        body {
            font-family: "Inter", sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .item-details {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff;
            border-radius: 5px;
            border: 1px solid #eee;
        }
        .button {
            display: inline-block;
            background-color: #ff5555;
            color: white;
            padding: 10px 20px;
            margin: 20px 0;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>取引完了のお知らせ</h1>
    </div>

    <div class="content">
        <p>{{ $transaction->seller->name }} 様</p>

        <p>以下の商品について、{{ $buyer->name }} 様が取引を完了しました。</p>

        <div class="item-details">
            <h3>{{ $transaction->item->name }}</h3>
            <p>価格: ¥{{ number_format($transaction->item->price) }}</p>
            <p>取引完了日時: {{ $transaction->completed_at->format('Y年m月d日 H:i') }}</p>
        </div>

        <p>取引相手のユーザーに評価をつけることができます。以下のボタンから取引画面に移動して評価を行ってください。</p>

        <a href="{{ url('/transaction/' . $transaction->id) }}" class="button">取引画面へ移動する</a>
    </div>

    <div class="footer">
        <p>※このメールは自動送信されています。返信はできませんのでご了承ください。</p>
        <p>© {{ date('Y') }} COACHTECH Flea Market. All rights reserved.</p>
    </div>
</body>
</html>
