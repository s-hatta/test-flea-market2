@extends('/layouts.common')
@section('css')
    <link rel="stylesheet" href="{{asset('css/mypage/profile_edit.css')}}">
@endsection
@section('title','プロフィール設定')

@section('content')
    <div class="wrapper">
        <h1>プロフィール設定</h1>
        <form class="form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            {{--プロフィール画像--}}
            <div class="form__item--flex">
                <div class="profile-image" id="preview-container">
                    @if($user->img_url)
                        <img id="preview-image" src="{{ asset('storage/images/users/'.Auth::user()->img_url) }}" alt="プロフィール画像">
                    @else
                        <div class="profile-image__placeholder" id="placeholder"></div>
                    @endif
                </div>
                <label class="form__item-label--button">
                    <input class="form__item-input" type="file" name="profile_image" id="profile-image-input" accept="image/*" hidden>
                    画像を選択する
                </label>
                <div class="form__item-space"></div>
                <div class="form__item-alert">{{$errors->first('profile_image')}}</div>
            </div>
            {{--ユーザー名--}}
            <div class="form__item">
                <div class="form__item-label">ユーザー名</div>
                <input class="form__item-input" type="text" name="name" value="{{ $user->name }}">
                <div class="form__item-alert">{{$errors->first('name')}}</div>
            </div>
            {{--郵便番号--}}
            <div class="form__item">
                <div class="form__item-label">郵便番号</div>
                <input class="form__item-input" type="text" name="postal_code" value="{{ $user->address->postal_code }}">
                <div class="form__item-alert">{{$errors->first('postal_code')}}</div>
            </div>
            {{--住所--}}
            <div class="form__item">
                <div class="form__item-label">住所</div>
                <input class="form__item-input" type="text" name="address" value="{{ $user->address->address }}">
                <div class="form__item-alert">{{$errors->first('address')}}</div>
            </div>
            {{--建物名--}}
            <div class="form__item">
                <div class="form__item-label">建物名</div>
                <input class="form__item-input" type="text" name="building" value="{{ $user->address->building }}">
            </div>
            {{--登録実行--}}
            <button class="form__submit" type="submit">更新する</button>
        </form>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('profile-image-input');
    const previewContainer = document.getElementById('preview-container');
    const placeholder = document.getElementById('placeholder');
    let previewImage = document.getElementById('preview-image');
    
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (!previewImage) {
                    previewImage = document.createElement('img');
                    previewImage.id = 'preview-image';
                    previewImage.alt = 'プロフィール画像';
                    if (placeholder) {
                        previewContainer.removeChild(placeholder);
                    }
                    previewContainer.appendChild(previewImage);
                }
                previewImage.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
