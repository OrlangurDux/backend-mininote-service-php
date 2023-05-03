<?php

namespace App\Http\Requests\Api\V1;

use Anik\Form\FormRequest;
use Illuminate\Support\Arr;

class NoteRequest extends FormRequest{
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
    protected function rules(): array{
        $as = Arr::get($this->route()[1], 'as');
        return match($as){
            'notes.update', 'notes.create' => [
                'title' => 'required|min:4',
                'note' => 'required',
                'status' => 'required'
            ],
            default => []
        };
    }
}
