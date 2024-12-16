<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
        return [
            'name' => 'required',
            'condition_id' => 'required',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'price' => 'required|numeric|min:0',
            'detail' => 'required|max:255',
            'img_url' => 'required|mimes:jpg,jpeg,png',
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => '商品名を入力してください',
            'condition_id.required' => '商品の状態を選択してください',
            'categories.required' => 'カテゴリーを1つ以上選択してください',
            'categories.array' => 'カテゴリーの形式が正しくありません',
            'categories.min' => 'カテゴリーを1つ以上選択してください',
            'categories.*.exists' => '選択されたカテゴリーは存在しません',
            'price.required' => '販売価格を入力してください',
            'price.numeric' => '販売価格を数値で入力してください',
            'price.min' => '販売価格は0円以上で入力してください',
            'detail.required' => '商品説明を入力してください',
            'detail.max' => '商品説明は255文字以内で入力してください',
            'img_url.required' => '商品画像を選択してください',
            'img_url.mimes' => '商品画像はjpg、jpeg、png形式のファイルを選択してください',
        ];
    }
}
