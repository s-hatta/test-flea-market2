<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Item;

class PurchaseRequest extends AddressRequest
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
            'payment_method' => 'required|in:cvs,card',
        ]);
    }
    
    public function messages(): array
    {
        $messages = parent::messages();
        return array_merge($messages, [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '支払い方法を選択してください',
        ]);
    }
}
