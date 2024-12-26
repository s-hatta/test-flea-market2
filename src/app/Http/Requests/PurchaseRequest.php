<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Item;

class PurchaseRequest extends FormRequest
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
            'payment_method' => 'required|in:cvs,card',
            'postal_code' => 'required|regex:/^[0-9]{3}-[0-9]{4}$/',
            'address' => 'required',
        ];
    }
    
    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '支払い方法を選択してください',
            'postal_code.required' => '配送先を選択してください',
            'postal_code.regex' => '配送先を選択してください',
            'address.required' => '配送先を選択してください',
        ];
    }
}
