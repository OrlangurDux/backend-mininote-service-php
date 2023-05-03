<?php

namespace App\Http\Requests\Api\V1;

use Anik\Form\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class CategoryRequest extends FormRequest{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool{
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array {
        $as = Arr::get($this->route()[1], 'as');
        return match($as){
            'categories.create' => [
                'name'     => 'required',
            ],
            'categories.update' => [
                'name' => 'required'
            ],
            default => []
        };
    }

    protected function errorResponse(): ?JsonResponse {
        return response()->json([
            'success' => false,
            'status' => $this->statusCode(),
            'error' => [
                'code' => 10,
                'message' => $this->validator->errors()->messages(),
            ],
            'data' => []
        ], $this->statusCode());
    }
}
