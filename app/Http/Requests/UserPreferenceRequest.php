<?php

namespace App\Http\Requests;

class UserPreferenceRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sources' => 'array',
            'sources.*' => 'string',
            'categories' => 'array',
            'categories.*' => 'string',
            'authors' => 'array',
            'authors.*' => 'string',
        ];
    }
}
