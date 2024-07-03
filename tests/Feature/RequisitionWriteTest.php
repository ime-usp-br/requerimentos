<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\TakenDisciplines;
use Tests\TestCase;
use App\Models\User;
use App\Enums\RoleId;
use App\Enums\EventType;
use App\Enums\DocumentType;
use App\Models\Requisition;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequisitionWriteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp() : void 
    {
        parent::setUp();

        $this->seed();
        $this->setUpFaker();
    }

    public function test_requisition_was_successfully_created_on_sg_creation_page()
    {
        // arquivos fake
        $takenDiscRecord = UploadedFile::fake()->create('takenDiscRecord.pdf', 500);
        $courseRecord = UploadedFile::fake()->create('courseRecord.pdf', 2000);
        $takenDiscSyllabus = UploadedFile::fake()->create('takenDiscSyllabus.pdf', 1000);
        $requestedDiscSyllabus = UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 200);

        $postData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'nusp' => $this->faker->numberBetween(10000000, 99999999),
            'course' => $this->faker->randomElement(['Bacharelado em Matemática', 'Bacharelado em Estatística', 'Bacharelado em Ciência da Computação', 'Bacharelado em Matemática', 'Bacharelado em Matemática Aplicada e Computacional', 'Bacharelado em Matemática Aplicada', 'Licenciatura em Matemática']),
            'disc1-name' => $this->faker->sentence(3),
            'disc1-institution' => $this->faker->sentence(3),
            'disc1-code' => $this->faker->word(),
            'disc1-year' => $this->faker->year(),
            'disc1-grade' => $this->faker->randomFloat(2, 0, 10),
            'disc1-semester' => $this->faker->randomElement(['Primeiro', 'Segundo']),
            'disc2-name' => $this->faker->sentence(3),
            'disc2-institution' => $this->faker->sentence(3),
            'disc2-code' => $this->faker->word(),
            'disc2-year' => $this->faker->year(),
            'disc2-grade' => $this->faker->randomFloat(2, 0, 10),
            'disc2-semester' => $this->faker->randomElement(['Primeiro', 'Segundo']),
            'requested-disc-name' => $this->faker->sentence(3),
            'requested-disc-type' => $this->faker->randomElement(['Obrigatória', 'Extracurricular', 'Optativa Livre', 'Optativa Eletiva']),
            'requested-disc-code' => $this->faker->word(),
            'disc-department' => $this->faker->randomElement(['MAC', 'MAE', 'MAT', 'MAP', 'Disciplina de fora do IME']),
            'takenDiscCount' => '2',
            'observations' => $this->faker->sentence(30),
            'taken-disc-record' => $takenDiscRecord,
            'course-record' => $courseRecord,
            'taken-disc-syllabus' => $takenDiscSyllabus,
            'requested-disc-syllabus' => $requestedDiscSyllabus,
        ];

        $userFromSG = User::factory()->create([
            'current_role_id' => RoleId::SG,
        ]);

        $response = $this->followingRedirects()->actingAs($userFromSG)->post(route('sg.create'), $postData);

        $response->assertStatus(200);

        $response->assertSee(['Requerimento criado', 'O requerimento foi criado com sucesso. Acompanhe o andamento pela página inicial.']);

        $newRequisitionFields = [
            'department' => $postData['disc-department'], 
            'requested_disc' => $postData['requested-disc-name'],
            'requested_disc_type' => $postData['requested-disc-type'],
            'requested_disc_code' => $postData['requested-disc-code'],
            'student_name' => $postData['name'],
            'email' => $postData['email'],
            'nusp' => $postData['nusp'],
            'course' => $postData['course'],
            'result' => 'Sem resultado',
            'observations' => $postData['observations'],
            'result_text' => null,
            'situation' => EventType::SENT_TO_SG,
            'internal_status' => EventType::SENT_TO_SG,
            'validated' => False,
            'latest_version' => 1
        ];

        $this->assertDatabaseHas('requisitions', $newRequisitionFields);

        $requisition = Requisition::where($newRequisitionFields)->first();

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => $postData['disc1-name'],
            'code' => $postData['disc1-code'],
            'year' => $postData['disc1-year'],
            'semester' => $postData['disc1-semester'],
            'grade' => $postData['disc1-grade'],
            'institution' => $postData['disc1-institution'],
            'requisition_id' => $requisition->id,
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => $postData['disc2-name'],
            'code' => $postData['disc2-code'],
            'year' => $postData['disc2-year'],
            'semester' => $postData['disc2-semester'],
            'grade' => $postData['disc2-grade'],
            'institution' => $postData['disc2-institution'],
            'requisition_id' => $requisition->id,
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::SENT_TO_SG,
            'message' => null,
            'requisition_id' => $requisition->id,
            'version' => 1,
            'author_name' => $userFromSG->name,
            'author_nusp' => $userFromSG->codpes
        ]);

        $documentTypes = [DocumentType::TAKEN_DISCS_RECORD, DocumentType::CURRENT_COURSE_RECORD, DocumentType::TAKEN_DISCS_SYLLABUS, DocumentType::REQUESTED_DISC_SYLLABUS];

        foreach ($documentTypes as $documentType) {
            $this->assertDatabaseHas('documents', [
                'requisition_id' => $requisition->id,
                'type' => $documentType,
            ]);
        }
    }

    public function test_requisition_was_successfully_created_on_student_creation_page()
    {
        // arquivos fake
        $takenDiscRecord = UploadedFile::fake()->create('takenDiscRecord.pdf', 500);
        $courseRecord = UploadedFile::fake()->create('courseRecord.pdf', 2000);
        $takenDiscSyllabus = UploadedFile::fake()->create('takenDiscSyllabus.pdf', 1000);
        $requestedDiscSyllabus = UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 200);

        $postData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'nusp' => $this->faker->numberBetween(10000000, 99999999),
            'course' => $this->faker->randomElement(['Bacharelado em Matemática', 'Bacharelado em Estatística', 'Bacharelado em Ciência da Computação', 'Bacharelado em Matemática', 'Bacharelado em Matemática Aplicada e Computacional', 'Bacharelado em Matemática Aplicada', 'Licenciatura em Matemática']),
            'disc1-name' => $this->faker->sentence(3),
            'disc1-institution' => $this->faker->sentence(3),
            'disc1-code' => $this->faker->word(),
            'disc1-year' => $this->faker->year(),
            'disc1-grade' => $this->faker->randomFloat(2, 0, 10),
            'disc1-semester' => $this->faker->randomElement(['Primeiro', 'Segundo']),
            'disc2-name' => $this->faker->sentence(3),
            'disc2-institution' => $this->faker->sentence(3),
            'disc2-code' => $this->faker->word(),
            'disc2-year' => $this->faker->year(),
            'disc2-grade' => $this->faker->randomFloat(2, 0, 10),
            'disc2-semester' => $this->faker->randomElement(['Primeiro', 'Segundo']),
            'requested-disc-name' => $this->faker->sentence(3),
            'requested-disc-type' => $this->faker->randomElement(['Obrigatória', 'Extracurricular', 'Optativa Livre', 'Optativa Eletiva']),
            'requested-disc-code' => $this->faker->word(),
            'disc-department' => $this->faker->randomElement(['MAC', 'MAE', 'MAP', 'MAP', 'Disciplina de fora do IME']),
            'takenDiscCount' => '2',
            'observations' => $this->faker->sentence(30),
            'taken-disc-record' => $takenDiscRecord,
            'course-record' => $courseRecord,
            'taken-disc-syllabus' => $takenDiscSyllabus,
            'requested-disc-syllabus' => $requestedDiscSyllabus,
        ];

        $studentUser = User::factory()->create([
            'current_role_id' => RoleId::STUDENT,
        ]);

        $response = $this->followingRedirects()->actingAs($studentUser)->post(route('student.create'), $postData);

        $response->assertStatus(200);

        $response->assertSee(['Requerimento criado', "O requerimento foi criado com sucesso. Acompanhe o andamento pelo campo 'situação' na página inicial."]);

        $newRequisitionFields = [
            'department' => $postData['disc-department'], 
            'requested_disc' => $postData['requested-disc-name'],
            'requested_disc_type' => $postData['requested-disc-type'],
            'requested_disc_code' => $postData['requested-disc-code'],
            'student_name' => $studentUser->name,
            'email' => $studentUser->email,
            'nusp' => $studentUser->codpes,
            'course' => $postData['course'],
            'result' => 'Sem resultado',
            'observations' => $postData['observations'],
            'result_text' => null,
            'situation' => EventType::SENT_TO_SG,
            'internal_status' => EventType::SENT_TO_SG,
            'validated' => False,
            'latest_version' => 1
        ];

        $this->assertDatabaseHas('requisitions', $newRequisitionFields);

        $requisition = Requisition::where($newRequisitionFields)->first();

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => $postData['disc1-name'],
            'code' => $postData['disc1-code'],
            'year' => $postData['disc1-year'],
            'semester' => $postData['disc1-semester'],
            'grade' => $postData['disc1-grade'],
            'institution' => $postData['disc1-institution'],
            'requisition_id' => $requisition->id,
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => $postData['disc2-name'],
            'code' => $postData['disc2-code'],
            'year' => $postData['disc2-year'],
            'semester' => $postData['disc2-semester'],
            'grade' => $postData['disc2-grade'],
            'institution' => $postData['disc2-institution'],
            'requisition_id' => $requisition->id,
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::SENT_TO_SG,
            'message' => null,
            'requisition_id' => $requisition->id,
            'version' => 1,
            'author_name' => $studentUser->name,
            'author_nusp' => $studentUser->codpes
        ]);

        $documentTypes = [DocumentType::TAKEN_DISCS_RECORD, DocumentType::CURRENT_COURSE_RECORD, DocumentType::TAKEN_DISCS_SYLLABUS, DocumentType::REQUESTED_DISC_SYLLABUS];

        foreach ($documentTypes as $documentType) {
            $this->assertDatabaseHas('documents', [
                'requisition_id' => $requisition->id,
                'type' => $documentType,
            ]);
        }
    }

    public function test_requisition_was_successfully_updated_on_student_update_page()
    {
        $requisitionId = $this->faker->numberBetween(1, 99999999);
        $userNUSP = $this->faker->numberBetween(10000000, 99999999);
        $studentUser = User::factory()->create([
            'current_role_id' => RoleId::STUDENT,
            'codpes' => $userNUSP,
        ]);
        
        $req = Requisition::factory()->create([
            'id' => $requisitionId,
            'result' => 'Inconsistência nas informações',
            'nusp' => $userNUSP,
        ]);

        $firstDiscId = $this->faker->numberBetween(1, 99999999);
        $firstDisc = TakenDisciplines::factory()->create([
            'requisition_id' => $requisitionId,
            'id' => $firstDiscId,
        ]);

        $secondDiscId = $this->faker->numberBetween(1, 99999999);
        $secondDisc = TakenDisciplines::factory()->create([
            'requisition_id' => $requisitionId,
            'id' => $secondDiscId
        ]);

        // documentos falsos
        $takenDiscRecord = UploadedFile::fake()->create('takenDiscRecord.pdf', 500);
        $courseRecord = UploadedFile::fake()->create('courseRecord.pdf', 2000);
        $takenDiscSyllabus = UploadedFile::fake()->create('takenDiscSyllabus.pdf', 1000);
        $requestedDiscSyllabus = UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 200);

        $postData = [
            'course' => $this->faker->randomElement(['Bacharelado em Matemática', 'Bacharelado em Estatística', 'Bacharelado em Ciência da Computação', 'Bacharelado em Matemática', 'Bacharelado em Matemática Aplicada e Computacional', 'Bacharelado em Matemática Aplicada', 'Licenciatura em Matemática']),
            'disc1-name' => $this->faker->sentence(3),
            'disc1-institution' => $this->faker->sentence(3),
            'disc1-code' => $this->faker->word(),
            'disc1-year' => $this->faker->year(),
            'disc1-grade' => $this->faker->randomFloat(2, 0, 10),
            'disc1-semester' => $this->faker->randomElement(['Primeiro', 'Segundo']),
            'disc1-id' => $firstDiscId,
            'disc2-name' => $this->faker->sentence(3),
            'disc2-institution' => $this->faker->sentence(3),
            'disc2-code' => $this->faker->word(),
            'disc2-year' => $this->faker->year(),
            'disc2-grade' => $this->faker->randomFloat(2, 0, 10),
            'disc2-semester' => $this->faker->randomElement(['Primeiro', 'Segundo']),
            'disc2-id' => $secondDiscId,
            'requested-disc-name' => $this->faker->sentence(3),
            'requested-disc-type' => $this->faker->randomElement(['Obrigatória', 'Extracurricular', 'Optativa Livre', 'Optativa Eletiva']),
            'requested-disc-code' => $this->faker->word(),
            'disc-department' => $this->faker->randomElement(['MAC', 'MAE', 'MAT', 'MAP', 'Disciplina de fora do IME']),
            'takenDiscCount' => '2',
            'observations' => $this->faker->sentence(30),
            'button' => null,
            'taken-disc-record' => $takenDiscRecord,
            'course-record' => $courseRecord,
            'taken-disc-syllabus' => $takenDiscSyllabus,
            'requested-disc-syllabus' => $requestedDiscSyllabus,
        ];

        $response = $this->followingRedirects()->actingAs($studentUser)->post(route('student.update', ['requisitionId' => $requisitionId]), $postData);

        $response->assertStatus(200);

        $response->assertSee(['Requerimento salvo', 'As novas informações do requerimento foram salvas com sucesso']);

        $updatedReq = Requisition::where(['id' => $requisitionId])->first();

        $this->assertDatabaseHas('events', [
            'type' => EventType::RESEND_BY_STUDENT,
            'requisition_id' => $requisitionId,
            'author_name' => $studentUser->name,
            'author_nusp' => $studentUser->codpes,
            'version' => $updatedReq->latest_version, 
        ]);

        $this->assertDatabaseHas('requisitions', [
            'department' => $postData['disc-department'], 
            'requested_disc' => $postData['requested-disc-name'],
            'requested_disc_type' => $postData['requested-disc-type'],
            'requested_disc_code' => $postData['requested-disc-code'],
            'course' => $postData['course'],
            'observations' => $postData['observations'],
            'situation' => EventType::RESEND_BY_STUDENT,
            'internal_status' => EventType::RESEND_BY_STUDENT,
            'latest_version' => $updatedReq->latest_version,
        ]);
        
        $reqFields = $req->toArray();
        unset($reqFields['latest_version'], 
              $reqFields['id'], 
              $reqFields['created_at'], 
              $reqFields['updated_at'],
              $reqFields['situation'], 
              $reqFields['internal_status'], 
              $reqFields['validated']);
        $this->assertDatabaseHas('requisitions_versions', $reqFields);

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => $postData['disc1-name'],
            'code' => $postData['disc1-code'],
            'year' => $postData['disc1-year'],
            'semester' => $postData['disc1-semester'],
            'grade' => $postData['disc1-grade'],
            'institution' => $postData['disc1-institution'],
            'requisition_id' => $requisitionId,
        ]);

        $firstDiscFields = $firstDisc->toArray();
        unset($firstDiscFields['latest_version'], 
              $firstDiscFields['id'], 
              $firstDiscFields['created_at'], 
              $firstDiscFields['updated_at']);
        $this->assertDatabaseHas('taken_disciplines_versions', $firstDiscFields);

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => $postData['disc2-name'],
            'code' => $postData['disc2-code'],
            'year' => $postData['disc2-year'],
            'semester' => $postData['disc2-semester'],
            'grade' => $postData['disc2-grade'],
            'institution' => $postData['disc2-institution'],
            'requisition_id' => $requisitionId,
        ]);

        $secondDiscFields = $secondDisc->toArray();
        unset($secondDiscFields['latest_version'], 
              $secondDiscFields['id'], 
              $secondDiscFields['created_at'], 
              $secondDiscFields['updated_at']);
        $this->assertDatabaseHas('taken_disciplines_versions', $secondDiscFields);

        $documentTypes = [DocumentType::TAKEN_DISCS_RECORD, DocumentType::CURRENT_COURSE_RECORD, DocumentType::TAKEN_DISCS_SYLLABUS, DocumentType::REQUESTED_DISC_SYLLABUS];

        foreach ($documentTypes as $documentType) {
            $this->assertDatabaseHas('documents', [
                'requisition_id' => $requisitionId,
                'type' => $documentType,
            ]);
        }
    }

    public function test_requisition_was_successfully_updated_on_sg_requisition_detail_page()
    {   
        $requisitionId = $this->faker->numberBetween(1, 99999999);
        $userNUSP = $this->faker->numberBetween(10000000, 99999999);
        $userFromSG = User::factory()->create([
            'current_role_id' => RoleId::SG,
            'codpes' => $userNUSP,
        ]);
        
        $req = Requisition::factory()->create([
            'id' => $requisitionId,
            'result' => 'Inconsistência nas informações',
            'nusp' => $userNUSP,
        ]);

        $firstDiscId = $this->faker->numberBetween(1, 99999999);
        $firstDisc = TakenDisciplines::factory()->create([
            'requisition_id' => $requisitionId,
            'id' => $firstDiscId,
        ]);

        $secondDiscId = $this->faker->numberBetween(1, 99999999);
        $secondDisc = TakenDisciplines::factory()->create([
            'requisition_id' => $requisitionId,
            'id' => $secondDiscId
        ]);

        $documentTypes = [DocumentType::TAKEN_DISCS_RECORD, DocumentType::CURRENT_COURSE_RECORD, DocumentType::TAKEN_DISCS_SYLLABUS, DocumentType::REQUESTED_DISC_SYLLABUS];

        foreach ($documentTypes as $documentType) {
            Document::factory()->create([
                'requisition_id' => $requisitionId,
                'type' => $documentType,
            ]);
        }

        $buttonPressed = $this->faker->randomElement(['save', 'reviewer', 'department']);
        // $buttonPressed = 'department';
        $result = $this->faker->randomElement(['Sem resultado', 'Inconsistência nas informações', 'Deferido', 'Indeferido']);

        $postData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'nusp' => $this->faker->numberBetween(10000000, 99999999),
            'course' => $this->faker->randomElement(['Bacharelado em Matemática', 'Bacharelado em Estatística', 'Bacharelado em Ciência da Computação', 'Bacharelado em Matemática', 'Bacharelado em Matemática Aplicada e Computacional', 'Bacharelado em Matemática Aplicada', 'Licenciatura em Matemática']),
            'disc1-name' => $this->faker->sentence(3),
            'disc1-institution' => $this->faker->sentence(3),
            'disc1-code' => $this->faker->word(),
            'disc1-year' => $this->faker->year(),
            'disc1-grade' => $this->faker->randomFloat(2, 0, 10),
            'disc1-semester' => $this->faker->randomElement(['Primeiro', 'Segundo']),
            'disc1-id' => $firstDiscId,
            'disc2-name' => $this->faker->sentence(3),
            'disc2-institution' => $this->faker->sentence(3),
            'disc2-code' => $this->faker->word(),
            'disc2-year' => $this->faker->year(),
            'disc2-grade' => $this->faker->randomFloat(2, 0, 10),
            'disc2-semester' => $this->faker->randomElement(['Primeiro', 'Segundo']),
            'disc2-id' => $secondDiscId,
            'requested-disc-name' => $this->faker->sentence(3),
            'requested-disc-type' => $this->faker->randomElement(['Obrigatória', 'Extracurricular', 'Optativa Livre', 'Optativa Eletiva']),
            'requested-disc-code' => $this->faker->word(),
            'disc-department' => $this->faker->randomElement(['MAC', 'MAE', 'MAT', 'MAP', 'Disciplina de fora do IME']),
            'takenDiscCount' => '2',
            'observations' => $this->faker->sentence(30),
            "result" => $result,
            "result-text" => $this->faker->sentence(40),
            "button" => $buttonPressed,
        ];

        $response = $this->followingRedirects()->actingAs($userFromSG)->post(route('sg.update', ['requisitionId' => $requisitionId]), $postData);

        $response->assertStatus(200);

        if ($buttonPressed === 'department') {
            $response->assertSee('Requerimento enviado');
        } elseif ($buttonPressed === 'save') {
            $response->assertSee('Requerimento salvo');
        } elseif ($buttonPressed === 'reviewer') {
            $response->assertSee('Envio do requerimento');
        }

        $updatedReq = Requisition::where(['id' => $requisitionId])->first();

        if ($req->result !== $updatedReq->result) {
            if ($updatedReq->result === 'Inconsistência nas informações') {
                $type = EventType::BACK_TO_STUDENT;
            } elseif ($updatedReq->result === 'Deferido') {
                $type = EventType::ACCEPTED;
            } elseif ($updatedReq->result === 'Indeferido') {
                $type = EventType::REJECTED;
            } elseif ($updatedReq->result === 'Sem resultado') {
                $type = EventType::IN_REVALUATION;
            }
            
            $this->assertDatabaseHas('events', [
                'type' => $type,
                'requisition_id' => $requisitionId,
                'author_name' => $userFromSG->name,
                'author_nusp' => $userFromSG->codpes,
                'version' => $updatedReq->latest_version, 
            ]);
        }

        if ($buttonPressed === 'department') {
            $this->assertDatabaseHas('events', [
                'type' => EventType::SENT_TO_DEPARTMENT,
                'requisition_id' => $requisitionId,
                'author_name' => $userFromSG->name,
                'author_nusp' => $userFromSG->codpes,
                'version' => $updatedReq->latest_version, 
            ]);

            $this->assertDatabaseHas('requisitions', [
                'department' => $postData['disc-department'], 
                'requested_disc' => $postData['requested-disc-name'],
                'requested_disc_type' => $postData['requested-disc-type'],
                'requested_disc_code' => $postData['requested-disc-code'],
                'student_name' => $postData['name'],
                'email' => $postData['email'],
                'nusp' => $postData['nusp'],
                'course' => $postData['course'],
                'result' => $postData['result'],
                'observations' => $postData['observations'],
                'result_text' => $postData['result-text'],
                'situation' => EventType::SENT_TO_DEPARTMENT,
                'internal_status' => EventType::SENT_TO_DEPARTMENT,
                'validated' => True,
                'latest_version' => $updatedReq->latest_version,
            ]);
        } else {
            $this->assertDatabaseHas('requisitions', [
                'department' => $postData['disc-department'], 
                'requested_disc' => $postData['requested-disc-name'],
                'requested_disc_type' => $postData['requested-disc-type'],
                'requested_disc_code' => $postData['requested-disc-code'],
                'student_name' => $postData['name'],
                'email' => $postData['email'],
                'nusp' => $postData['nusp'],
                'course' => $postData['course'],
                'result' => $postData['result'],
                'observations' => $postData['observations'],
                'result_text' => $postData['result-text'],
                'latest_version' => $updatedReq->latest_version,
            ]);
        }

        $fields = $req->toArray();
        unset($fields['latest_version'], 
              $fields['id'], 
              $fields['created_at'], 
              $fields['updated_at'],
              $fields['situation'], 
              $fields['internal_status'], 
              $fields['validated']);
        $this->assertDatabaseHas('requisitions_versions', $fields);
        

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => $postData['disc1-name'],
            'code' => $postData['disc1-code'],
            'year' => $postData['disc1-year'],
            'semester' => $postData['disc1-semester'],
            'grade' => $postData['disc1-grade'],
            'institution' => $postData['disc1-institution'],
            'requisition_id' => $requisitionId,
        ]);

        $firstDiscFields = $firstDisc->toArray();
        unset($firstDiscFields['latest_version'], 
              $firstDiscFields['id'], 
              $firstDiscFields['created_at'], 
              $firstDiscFields['updated_at']);
        $this->assertDatabaseHas('taken_disciplines_versions', $firstDiscFields);

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => $postData['disc2-name'],
            'code' => $postData['disc2-code'],
            'year' => $postData['disc2-year'],
            'semester' => $postData['disc2-semester'],
            'grade' => $postData['disc2-grade'],
            'institution' => $postData['disc2-institution'],
            'requisition_id' => $requisitionId,
        ]);

        $secondDiscFields = $secondDisc->toArray();
        unset($secondDiscFields['latest_version'], 
              $secondDiscFields['id'], 
              $secondDiscFields['created_at'], 
              $secondDiscFields['updated_at']);
        $this->assertDatabaseHas('taken_disciplines_versions', $secondDiscFields);
    }
}
