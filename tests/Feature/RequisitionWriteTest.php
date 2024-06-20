<?php

namespace Tests\Feature;

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
            'disc-department' => $this->faker->randomElement(['MAC', 'MAE', 'MAP', 'MAP', 'Disciplina de fora do IME']),
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
}
