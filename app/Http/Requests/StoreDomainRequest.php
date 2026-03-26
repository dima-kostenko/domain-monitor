<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:253',
                'regex:/^(?!-)[A-Za-z0-9\-]{1,63}(?<!-)(\.[A-Za-z0-9\-]{1,63})*\.[A-Za-z]{2,}$/',
                Rule::unique('domains')->where('user_id', $this->user()->id),
            ],
            'check_interval' => ['required', 'integer', 'min:1', 'max:1440'],
            'timeout'        => ['required', 'integer', 'min:1', 'max:60'],
            'method'         => ['required', Rule::in(['GET', 'HEAD'])],
            'is_active'      => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex'  => 'Enter a valid domain name (e.g. example.com).',
            'name.unique' => 'This domain is already being monitored.',
        ];
    }
}
