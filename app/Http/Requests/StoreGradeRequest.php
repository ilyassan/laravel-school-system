<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return $user->isTeacher();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "class_id" => ["required", "exists:classes,id"],
            "student_id" => [
                "required",
                function ($attribute, $value, $fail) {
                    $student = User::select([User::PRIMARY_KEY_COLUMN_NAME, User::CLASS_COLUMN])->find($value);
                    if (!$student) {
                        $fail("Selected student not found.");
                    } elseif ($student->class_id != $this->input('class_id')) {
                        $fail("Selected student not belong to the selected class.");
                    }
                }
            ],
            "grade" => ["required", "numeric", 'min:0', 'max:20'],
        ];
    }
}
