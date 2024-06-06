<?php

namespace App\Http\Requests;

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

        // $routeName = $this->route()->getName();

        // if ($routeName === 'student.update') {
            
        //     $reqToBeUpdated = Requisition::find($requisitionId);
        //     $user = Auth::user();

        //     if (!$reqToBeUpdated || $reqToBeUpdated->nusp !== $user->codpes || $reqToBeUpdated->result !== 'Inconsistência nas informações') {
        //         abort(403);
        //     }
        // }


        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $routeName = $this->route()->getName();

        if ($routeName === 'sg.update') {

            $rules = [
                'name' => 'required | max:255',
                'email' => 'required | max:255 | email ',
                'nusp' => 'required | numeric | integer',
                'course' => 'required | max:255',
                'requested-disc-name' => 'required | max:255',
                'requested-disc-type' => 'required',
                'requested-disc-code' => 'required | max:255',
                'disc-department' => 'required'
            ];

        } elseif ($routeName === 'student.update') {

            $rules = [
                'course' => 'required | max:255',
                'requested-disc-name' => 'required | max:255',
                'requested-disc-type' => 'required',
                'requested-disc-code' => 'required | max:255',
                // essas regras de validação dos arquivos tem que ser colocadas nessa ordem (com o mimes:pdf no final), senão da ruim
                'taken-disc-record' => 'required | file | max:2048 | mimes:pdf',
                'course-record' => 'required | file | max:2048 | mimes:pdf',
                'taken-disc-syllabus' => 'required | file | max:2048 | mimes:pdf',
                'requested-disc-syllabus' => 'required | file | max:2048 | mimes:pdf',
                'disc-department' => 'required'
            ];
        }

        $takenDiscCount = $this->input('takenDiscCount');

        for ($i = 1; $i <= $takenDiscCount; $i++) {
            $rules["disc$i-name"] = 'required | max:255';
            $rules["disc$i-code"] = 'max:255';
            $rules["disc$i-year"] = 'required | numeric | integer';
            $rules["disc$i-grade"] = 'required | numeric';
            $rules["disc$i-semester"] = 'required';
            $rules["disc$i-institution"] = 'required | max:255';
        }  

        return $rules;
    }

    protected function passedValidation() 
    {
        $validatedData = $this->validated();
        $routeName = $this->route()->getName();

        if ($routeName === 'sg.update') {
            $this->requisitionData = [
                'department' => $validatedData['disc-department'],
                'requested_disc' => $validatedData['requested-disc-name'],
                'requested_disc_type' => $validatedData['requested-disc-type'],
                'requested_disc_code' => $validatedData['requested-disc-code'],
                'student_name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'nusp' => (int) $validatedData['nusp'],
                'course' => $validatedData['course'],
                'result' => $this->input('result'),
                'observations' => $this->input('observations'),
                'result_text' => $this->input('result-text'),
            ];
        } elseif ($routeName === 'student.update') {
            $this->requisitionData = [
                'department' => $validatedData['disc-department'],
                'requested_disc' => $validatedData['requested-disc-name'],
                'requested_disc_type' => $validatedData['requested-disc-type'],
                'requested_disc_code' => $validatedData['requested-disc-code'],
            ];
        }

        $takenDiscCount = $this->input('takenDiscCount');

        $this->disciplinesData = [];

        for ($i = 1; $i <= $takenDiscCount; $i++) {
            $this->disciplinesData["disc$i-name"] = $validatedData["disc$i-name"];
            $this->disciplinesData["disc$i-code"] = $validatedData["disc$i-code"];
            $this->disciplinesData["disc$i-year"] = (int) $validatedData["disc$i-year"];
            $this->disciplinesData["disc$i-grade"] = number_format((float) $validatedData["disc$i-grade"], 2, '.', '');  
            $this->disciplinesData["disc$i-semester"] = $validatedData["disc$i-semester"];
            $this->disciplinesData["disc$i-institution"] = $validatedData["disc$i-institution"]; 
        }  
    }

    public function getRequisitionData() 
    {
        return $this->requisitionData;
    }

    public function getDisciplinesData() 
    {
        return $this->disciplinesData;
    }
}
