<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('account'));
    }

    public function rules(): array
    {
        $accountId = $this->route('account')->id;
        return [
            'courier_id' => ['required', 'string', Rule::unique('accounts')->ignore($accountId)],
            'email' => ['required', 'email', Rule::unique('accounts')->ignore($accountId)],
            'password' => 'nullable|string|min:6', // Contraseña opcional al editar
            'city' => 'required|in:GRO,MAT,FIG,BCN,CAL',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,blocked',
            'notes' => 'nullable|string',
        ];
    }
}
