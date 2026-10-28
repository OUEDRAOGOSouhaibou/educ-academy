<?php

namespace App\Http\Requests\Admin;

use App\Models\SchoolYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MonthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('school_year_id')) {
            $currentYear = SchoolYear::getCurrent();
            if ($currentYear) {
                $this->merge(['school_year_id' => $currentYear->id]);
            }
        }

        $this->merge([
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    public function rules(): array
    {
        $monthId = $this->route('month') ? $this->route('month')->id : null;
        $schoolYearId = $this->input('school_year_id');

        return [
            'school_year_id' => ['required', 'exists:school_years,id'],
            'name' => ['required', 'string', 'max:50'],
            'calendar_number' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('months', 'calendar_number')
                    ->where('school_year_id', $schoolYearId)
                    ->ignore($monthId),
            ],
            'position' => ['required', 'integer', 'min:1', 'max:50'],
            'is_published' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'calendar_number.unique' => 'Ce mois calendaire existe déjà pour cette année scolaire.',
            'position.min' => 'L\'ordre d\'affichage doit être au moins 1.',
        ];
    }
}

