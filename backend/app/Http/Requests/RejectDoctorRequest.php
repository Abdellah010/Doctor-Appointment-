<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|in:incomplete_credentials,license_not_verifiable,missing_documents,other',
            'notes'  => 'nullable|string|max:1000',
        ];
    }
}
