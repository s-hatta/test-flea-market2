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
        ];
    }
    
    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '支払い方法を選択してください',
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            $item = Item::find($this->input('id'));
            if ($user && $item) {
                $address = app(PurchaseController::class)->findAddress($user, $item);
                $this->merge([
                    'name' => $user->name,
                    'postal_code' => $address->postal_code,
                    'address' => $address->address
                ]);
            }
        });
    }
}
