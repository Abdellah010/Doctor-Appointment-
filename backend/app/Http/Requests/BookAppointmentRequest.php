<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPatient() ?? false;
    }

    public function rules(): array
    {
        return [
            'doctor_id'      => 'required|integer|exists:doctors,id',
            'slot_id'        => 'required|integer|exists:slots,id',
            'reason'         => 'nullable|string|max:500',
            'insurance_type' => 'nullable|in:cnss,ramed,private,none',
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.exists' => 'The selected doctor no longer exists.',
            'slot_id.exists'   => 'The selected time slot no longer exists.',
        ];
    }
}
