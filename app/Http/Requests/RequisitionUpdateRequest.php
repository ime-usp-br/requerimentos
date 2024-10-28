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

        $requisitionId = $this->route('requisitionId');
        $reqToBeUpdated = Requisition::find($requisitionId);

        if (!$reqToBeUpdated) {
            abort(404);
        }

        $routeName = $this->route()->getName();

        if ($routeName === 'student.update') {
            
            $user = Auth::user();

            // o cast para int foi adicionado porque o banco sqlite3 retorna 
            // $req->student_nusp como uma string no server de produção. Sem esse cast,
            // os testes falham dentro do server
            if ((int) $reqToBeUpdated->student_nusp !== $user->codpes) {
                abort(404);
            }

            // esse "return false" retorna um 403 para o cliente 
            if ($reqToBeUpdated->result !== 'Inconsistência nas informações') {
                return false;
            }
        }

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
                'name' => 'required|max:255',
                'email' => 'required|max:255|email ',
                'nusp' => 'required|numeric|integer',
                'course' => 'required|max:255',
                'requested-disc-name' => 'required|max:255',
                'requested-disc-type' => 'required',
                'requested-disc-code' => 'required|max:255',
                'disc-department' => 'required'
            ];

        } elseif ($routeName === 'student.update') {

            $rules = [
                'course' => 'required|max:255',
                'requested-disc-name' => 'required|max:255',
                'requested-disc-type' => 'required',
                'requested-disc-code' => 'required|max:255',
                // essas regras de validação dos arquivos tem que ser colocadas nessa ordem (com o mimes:pdf no final), senão da ruim
                'taken-disc-record' => 'required|file|max:2048|mimes:pdf',
                'course-record' => 'required|file|max:2560|mimes:pdf',
                'taken-disc-syllabus' => 'required|file|max:2048|mimes:pdf',
                'requested-disc-syllabus' => 'required|file|max:2048|mimes:pdf',
                'disc-department' => 'required'
            ];
        }

        $takenDiscCount = $this->input('takenDiscCount');

        for ($i = 1; $i <= $takenDiscCount; $i++) {
            $rules["disc$i-name"] = 'required|max:255';
            $rules["disc$i-code"] = 'max:255';
            $rules["disc$i-year"] = 'required|numeric|integer';
            $rules["disc$i-grade"] = 'required|numeric';
            $rules["disc$i-semester"] = 'required';
            $rules["disc$i-institution"] = 'required|max:255';
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
                'student_nusp' => (int) $validatedData['nusp'],
                'course' => $validatedData['course'],
                'result' => $this->input('result'),
                'observations' => $this->input('observations'),
                'result_text' => $this->input('result-text'),
            ];
        } elseif ($routeName === 'student.update') {
            $this->requisitionData = [
                'course' => $validatedData['course'],
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
