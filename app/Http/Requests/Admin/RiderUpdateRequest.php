<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $rider = $this->route('rider');
        return $this->user()->can('update', $rider);
    }

    public function rules(): array
    {
        $riderId = $this->route('rider')->id;

        return [
            'full_name' => 'required|string|max:255',
            'dni' => ['required', 'string', Rule::unique('riders')->ignore($riderId)],
            'email' => ['required', 'email', Rule::unique('riders')->ignore($riderId)],
            'city' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
            'status' => 'required|in:active,inactive,blocked',
            // --- AÑADIR ESTA LÍNEA DE VALIDACIÓN ---
            'weekly_contract_hours' => 'required|integer|min:0|max:100',
            'edits_remaining' => 'required|integer|min:0|max:255',
        ];
    }
}
