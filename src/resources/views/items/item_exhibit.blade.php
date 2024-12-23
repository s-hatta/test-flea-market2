@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/item_exhibit.css') }}">
@endsection
@section('title','商品の出品')

@section('content')
<div class="wrapper">
    <h1>商品の出品</h1>
    <form class="form" method="POST" action="{{ route('item.update') }}" enctype="multipart/form-data">
        @csrf
        {{--商品画像--}}
        <div class="form-group">
            <h3>商品画像</h3>
            <div class=alert>{{$errors->first('item_image')}}</div>
            <div class="form-item__border--dashed">
                <img id="preview-image">
                <label class="select-image">
                    <input type="file" name="item_image" id="item_image-input" accept="image/*" hidden>
                    画像を選択する
                </label>
            </div>
        </div>
        
        {{--商品の特徴--}}
        <div class="form-group">
            <h2>商品の特徴</h2>
            
            {{--カテゴリー--}}
            <div class="form-item">
                <h3>カテゴリー</h3>
                <div class=alert>{{$errors->first('categories')}}</div>
                <div class="categories">
                    @foreach($categories as $category)
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" id="{{ "category". $category->id }}" hidden>
                        <label for="{{ "category". $category->id }}">{{ $category->content }}</label>
                    @endforeach
                </div>
            </div>
            
            {{--商品の状態--}}
            <div class="form-item">
                <h3>商品の状態</h3>
                <div class=alert>{{$errors->first('condition_id')}}</div>
                <select name="condition_id">
                    <option value="" hidden>選択してください</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}" 
                                @if( (int)old('condition_id') === $condition->id ) selected @endif>
                            {{ $condition->condition }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        {{--商品名と説明--}}
        <div class="form-group">
            <h2>商品名と説明</h2>
            
            {{--商品名--}}
            <div class="form-item">
                <h3>商品名</h3>
                <div class=alert>{{$errors->first('name')}}</div>
                <input type="text" name="name" value="{{old('name')}}">
            </div>
            
            {{--ブランド名--}}
            <div class="form-item">
                <h3>ブランド名</h3>
                <input type="text" name="brand_name" value="{{old('brand_name')}}" placeholder="ノーブランド">
            </div>
            
            {{--商品の説明--}}
            <div class="form-item">
                <h3>商品の説明</h3>
                <div class=alert>{{$errors->first('detail')}}</div>
                <textarea name="detail">{{old('detail')}}</textarea>
            </div>
            
            {{--販売価格--}}
            <div class="form-item">
                <h3>販売価格</h3>
                <div class=alert>{{$errors->first('price')}}</div>
                <input type="text" name="price" value="{{old('price')}}">
            </div>
        </div>
        
        {{--出品ボタン--}}
        <button type="submit" class="submit-exhibit">出品する</button>
    </form>
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