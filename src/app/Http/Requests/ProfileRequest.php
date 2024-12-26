<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends AddressRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        return array_merge($rules, [
            'profile_image' => 'mimes:jpg,jpeg,png',
        ]);
    }
    
    public function messages(): array
    {
        $messages = parent::messages();
        return array_merge($messages, [
            'profile_image.mimes' => '画像はjpg、jpeg、png形式のファイルを選択してください',
        ]);
    }
}
