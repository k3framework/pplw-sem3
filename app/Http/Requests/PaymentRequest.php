<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return ['method' => ['required', Rule::in(array_keys(Payment::METHODS))], 'reference' => ['nullable', 'string', 'max:255'], 'amount' => ['prohibited']];
    }
}
