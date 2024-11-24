@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/item_exhibit.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>商品の出品</h1>
    <form>
        <div class="form-group">
            <h3>商品画像</h3>
            <input type="file" id="image" name="image" class="btn">
        </div>
        <div class="form-group">
            <h2>商品の詳細</h2>
        </div>
        <div class="form-group">
            <h3>カテゴリー</h3>
            <div id="categories">
                @foreach($categories as $category)
                    <label>
                        <input type="checkbox" name="categories" value="{{ $category->id }}">
                        {{ $category->content }}
                    </label>
                @endforeach
            </div>
            <h3>商品の状態</h3>
            <select id="condition" name="condition">
                <option value="">選択してください</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}">{{ $condition->condition }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <h2>商品名と説明</h2>
            <h3>商品名</h3>
            <input type="text" id="name" name="name">
            <h3>商品の説明</h3>
            <textarea id="description" name="description"></textarea>
            <h3>販売価格</h3>
            <input type="text" id="price" name="price">
        </div>
        <button type="submit" class="btn submit-btn">出品する</button>
    </form>
</div>
@endsection
