<?php

namespace App\Http\Requests;

use App\Models\Requisition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class RequisitionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'requisitionId' => 'required|integer',
            'requestedDiscDepartment' => 'required|max:255',
            'observations' => 'string',
            'takenDiscRecord' => 'required|file|max:512|mimes:pdf',
            'courseRecord' => 'required|file|max:512|mimes:pdf',
            'takenDiscSyllabus' => 'required|file|max:512|mimes:pdf',
            'requestedDiscSyllabus' => 'required|file|max:512|mimes:pdf',
            'takenDiscCount' => 'required|numeric|integer',
        ];
            
        $takenDiscCount = $this->input('takenDiscCount');

        for($i = 0; $i < $takenDiscCount; $i++) {
            $rules["takenDiscNames.$i"] = 'required|max:255';
            $rules["takenDiscCodes.$i"] = 'max:255';
            $rules["takenDiscYears.$i"] = 'required|numeric|integer|digits:4';
            $rules["takenDiscGrades.$i"] = 'required';
            $rules["takenDiscSemesters.$i"] = 'required';
            $rules["takenDiscInstitutions.$i"] = 'required|max:255';
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'observations' => $this->observations ?? '',
        ]);
    }
}
