<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\RoleId;

class RequisitionCreationRequest extends FormRequest
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
            'course' => 'required|max:255',
            'requestedDiscName' => 'required|max:255',
            'requestedDiscType' => 'required',
            'requestedDiscCode' => 'required|max:255',
            'requestedDiscDepartment' => 'required|max:255',
            'takenDiscRecord' => 'required|file|max:512|mimes:pdf',
            'courseRecord' => 'required|file|max:512|mimes:pdf',
            'takenDiscSyllabus' => 'required|file|max:512|mimes:pdf',
            'requestedDiscSyllabus' => 'required|file|max:512|mimes:pdf',
            'takenDiscCount' => 'required|numeric|integer',
            'observations' => 'string',
        ];
        
        if (Auth::user()->current_role_id != RoleId::STUDENT) {
            $rules['student_name'] = 'required|max:255';
            $rules['email'] = 'required|max:255|email ';
            $rules['student_nusp'] = 'required|numeric|integer';
        }

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
