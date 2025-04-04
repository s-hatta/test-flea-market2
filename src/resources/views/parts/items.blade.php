<div class="item-container">
    @if( isset($items) )
        @foreach($items as $item)
            <div class="item-card @if($item->stock == 0) sold-out @endif">
                <a href="{{ url('/item/' . $item->id) }}">
                    <div class="item-image-wrapper">
                        <img src="{{ Storage::url('public/images/items/' . $item->img_url) }}" alt="商品画像" class="item-image">
                        @if($item->stock == 0)
                            <div class="sold-text">Sold</div>
                        @endif
                    </div>
                </a>
                <p class="item-name">{{ $item->name }}</p>
            </div>
        @endforeach
    @endif
</div>
