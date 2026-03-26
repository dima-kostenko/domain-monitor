<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('domain')->user_id
            || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:253',
                'regex:/^(?!-)[A-Za-z0-9\-]{1,63}(?<!-)(\.[A-Za-z0-9\-]{1,63})*\.[A-Za-z]{2,}$/',
                Rule::unique('domains')
                    ->where('user_id', $this->user()->id)
                    ->ignore($this->route('domain')),
            ],
            'check_interval' => ['sometimes', 'integer', 'min:1', 'max:1440'],
            'timeout'        => ['sometimes', 'integer', 'min:1', 'max:60'],
            'method'         => ['sometimes', Rule::in(['GET', 'HEAD'])],
            'is_active'      => ['boolean'],
        ];
    }
}
