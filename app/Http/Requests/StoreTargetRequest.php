<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTargetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'total_time' => 'required|numeric',
            'total_exams' => 'nullable|numeric',
            'total_practices' => 'nullable|numeric',
            'total_arenas' => 'nullable|numeric',
            'min_score' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric'
        ];
    }

    public function attributes(): array {
        return [
            'total_time' => 'Tổng thời gian học (phút)',
            'total_exams' => 'Tổng số lượng bài kiểm tra',
            'total_practices' => 'Tổng số lượng bài tập',
            'total_arenas' => 'Tổng số lượng trận đấu',
            'min_score' => 'Điểm số tối thiểu',
            'accuracy' => 'Độ chính xác',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'numeric' => ':attribute phải là một số',
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
