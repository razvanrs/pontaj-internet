<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentSanctionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return auth()->user()->can('create sanction');
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
            'student' => 'required|integer|exists:students,id',
            'sanction' => 'required|integer|exists:sanctions,id',
            'date' => 'required|date|before_or_equal:today',
        ];
    }
}
