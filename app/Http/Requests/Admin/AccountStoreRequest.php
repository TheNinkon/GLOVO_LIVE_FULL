<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AccountStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Account::class);
    }

    public function rules(): array
    {
        return [
            'courier_id' => 'required|string|unique:accounts,courier_id',
            'email' => 'required|email|unique:accounts,email',
            'password' => 'required|string|min:6',
            'city' => 'required|in:GRO,MAT,FIG,BCN,CAL',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,blocked',
            'notes' => 'nullable|string',
        ];
    }
}
