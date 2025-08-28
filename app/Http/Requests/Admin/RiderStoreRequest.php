<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RiderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Rider::class);
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'dni' => 'required|string|unique:riders,dni',
            'email' => 'required|email|unique:riders,email',
            'city' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'status' => 'required|in:active,inactive,blocked',
            // --- AÑADIR ESTA LÍNEA DE VALIDACIÓN ---
            'weekly_contract_hours' => 'required|integer|min:0|max:100',
            'edits_remaining' => 'required|integer|min:0|max:255',
        ];
    }
}
