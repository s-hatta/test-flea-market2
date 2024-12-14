@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/parts/items.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="profile-header">
        <div class="profile-image">
            @if($user->img_url)
                <img src="{{ asset('storage/images/users/'.Auth::user()->img_url) }}" alt="プロフィール画像">
            @else
                <div class="profile-image__placeholder"></div>
            @endif
        </div>
        <h2 class="profile-name">{{ Auth::user()->name }}</h2>
        <a href="{{ url('/mypage/profile') }}" class="edit-profile-button">プロフィールを編集</a>
    </div>
    <div class="profile-tabs">
        <button class="tab-button active" data-tab="exhibitions">出品した商品</button>
        <button class="tab-button" data-tab="purchases">購入した商品</button>
    </div>
    <div id="exhibitions" class="tab-content active">
        @include('parts.items', ['items'=>$exhibitedItems])
    </div>
    <div id="purchases" class="tab-content">
        @include('parts.items', ['items'=>$purchasedItems])
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab-button');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const target = document.getElementById(this.dataset.tab);

                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                this.classList.add('active');
                target.classList.add('active');
            });
        });
    });
</script>