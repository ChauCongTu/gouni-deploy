<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTargetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'hour_per_day' => 'required|numeric',
            'target_score' => 'required|numeric',
            'subject_id' => 'required|integer',
            'stage' => 'required|string',
        ];
    }

    public function attributes(): array {
        return [
            'hour_per_day' => 'Số giờ mỗi ngày',
            'target_score' => 'Mục tiêu điểm số',
            'subject_id' => 'Môn học',
            'stage' => 'Giai đoạn',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'integer' => ':attribute phải là một số nguyên',
            'numeric' => ':attribute phải là một số',
            'string' => ':attribute phải là một chuỗi',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->getMessageBag()
        ]));
    }
}
