<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'requested-disc-name' => 'required|max:255',
            'requested-disc-type' => 'required',
            'requested-disc-code' => 'required|max:255',
            // essas regras de validação dos arquivos tem que ser colocadas nessa ordem
            // (com o mimes:pdf no final), senão da ruim 
            // limite de tamanho imposto pelo servidor do IME
            'taken-disc-record' => 'required|file|max:150|mimes:pdf',
            'course-record' => 'required|file|max:150|mimes:pdf',
            'taken-disc-syllabus' => 'required|file|max:150|mimes:pdf',
            'requested-disc-syllabus' => 'required|file|max:150|mimes:pdf',
            'disc-department' => 'required'
        ];
        
        $routeName = $this->route()->getName();

        if ($routeName === 'sg.create') {

            $sgSpecificRules = [
                'name' => 'required|max:255',
                'email' => 'required|max:255|email ',
                'nusp' => 'required|numeric|integer'
            ];

            $rules = $rules + $sgSpecificRules;
        }

        $takenDiscCount = $this->input('takenDiscCount');

        for ($i = 1; $i <= $takenDiscCount; $i++) {
            $rules["disc$i-name"] = 'required|max:255';
            $rules["disc$i-code"] = 'max:255';
            $rules["disc$i-year"] = 'required|numeric|integer|digits: 4';
            $rules["disc$i-grade"] = 'required|max:15';
            $rules["disc$i-semester"] = 'required';
            $rules["disc$i-institution"] = 'required|max:255';
        }  

        return $rules;
    }
}
