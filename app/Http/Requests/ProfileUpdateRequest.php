<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['required', 'numeric', 'digits:10', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }
}
