<?php

namespace App\Http\Requests\Sprint;

use App\Http\Requests\ApiFormRequest;

class SprintTasksIndexRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sprint' => ['required', 'integer', 'exists:sprints,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sprint' => $this->route('sprint'),
        ]);
    }
}
