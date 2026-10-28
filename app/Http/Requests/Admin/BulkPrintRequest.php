<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkPrintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasAdminAccess();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_ids' => ['required', 'array', 'min:1', 'max:' . (int) config('invoice.bulk_max', 50)],
            'order_ids.*' => ['integer', 'distinct', 'exists:orders,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order_ids.required' => 'Pilih minimal satu pesanan untuk dicetak.',
            'order_ids.min' => 'Pilih minimal satu pesanan untuk dicetak.',
            'order_ids.max' => 'Maksimal :max pesanan per sekali cetak massal.',
            'order_ids.*.exists' => 'Salah satu pesanan yang dipilih tidak ditemukan.',
        ];
    }
}

