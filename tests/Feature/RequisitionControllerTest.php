<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Requisition;
use App\Models\Document;
use App\Models\TakenDisciplines;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\DatabaseSeeder;
use App\Enums\RoleId;
use App\Enums\DocumentType;
use App\Enums\EventType;
use App\Enums\DepartmentId;

class RequisitionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    private function asRole($roleId)
    {
        $user = User::factory()->create([
            'codpes' => '999999',
            'name' => 'test',
            'current_role_id' => $roleId
        ]);
        $this->actingAs($user);
    }

    public function test_show_requisition()
    {
        $user = User::factory()->create([
            'codpes' => '999999',
            'name' => 'test',
            'current_role_id' => RoleId::STUDENT
        ]);
        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
        ]);

        $response = $this->get("/detalhe/{$requisition->id}");

        $response->assertStatus(200);
    }

    public function test_show_requisition_returns_404_if_not_found()
    {
        $this->asRole(RoleId::STUDENT);

        $response = $this->get("/detalhe/999999");

        $response->assertStatus(404);
    }

    public function test_show_requisition_returns_403_if_student_tries_to_access_not_owned_requisition()
    {
        $user = User::factory()->create([
            'codpes' => '999999',
            'current_role_id' => RoleId::STUDENT
        ]);

        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '888888',
            'student_name' => 'Another Student',
            'email' => 'another@student.com',
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
        ]);

        $response = $this->get("/detalhe/{$requisition->id}");

        $response->assertStatus(403);
    }

    public function test_show_requisition_allows_sg_user_to_access_any_requisition()
    {
        $user = User::factory()->create([
            'codpes' => '999999',
            'current_role_id' => RoleId::SG
        ]);

        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '888888',
            'student_name' => 'Another Student',
            'email' => 'another@student.com',
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
        ]);

        $response = $this->get("/detalhe/{$requisition->id}");

        $response->assertStatus(200);
    }

    public function test_create_requisition()
    {
        Storage::fake('local');

        $this->asRole(RoleId::STUDENT);

        $response = $this->post('/novo-requerimento', [
            'course' => 'Bacharelado em Ciência da Computação',
            'requestedDiscName' => 'Disciplina Teste',
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscCode' => 'TEST123',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações de teste',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada'],
            'takenDiscCodes' => ['CURS123'],
            'takenDiscYears' => [2022],
            'takenDiscGrades' => [9.5],
            'takenDiscSemesters' => ['Primeiro'],
            'takenDiscInstitutions' => ['Instituição Teste'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseHas('requisitions', [
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
        ]);

        $this->assertDatabaseHas('documents', [
            'type' => DocumentType::TAKEN_DISCS_RECORD,
        ]);

        $this->assertDatabaseHas('documents', [
            'type' => DocumentType::CURRENT_COURSE_RECORD,
        ]);

        $this->assertDatabaseHas('documents', [
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
        ]);

        $this->assertDatabaseHas('documents', [
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => 9.5,
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::SENT_TO_SG,
            'author_name' => 'test',
            'author_nusp' => '999999',
        ]);
    }

    public function test_create_requisition_return_error_if_incomplete_data()
    {
        Storage::fake('local');

        $this->asRole(RoleId::STUDENT);

        $response = $this->post('/novo-requerimento', [
            'course' => 'Bacharelado em Ciência da Computação',
            'requestedDiscName' => 'Disciplina Teste',
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscCode' => 'TEST123',
            'requestedDiscDepartment' => 'MAC',
            // Missing 'takenDiscRecord'
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada'],
            'takenDiscCodes' => ['CURS123'],
            'takenDiscYears' => [2022],
            'takenDiscGrades' => [9.5],
            'takenDiscSemesters' => ['Primeiro'],
            'takenDiscInstitutions' => ['Instituição Teste'],
        ]);

        $response->assertSessionHasErrors(['takenDiscRecord']);
    }

    public function test_create_requisition_with_two_taken_disciplines()
    {
        Storage::fake('local');

        $this->asRole(RoleId::STUDENT);

        $response = $this->post('/novo-requerimento', [
            'course' => 'Bacharelado em Ciência da Computação',
            'requestedDiscName' => 'Disciplina Teste',
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscCode' => 'TEST123',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações de teste',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 2,
            'takenDiscNames' => ['Disciplina Cursada 1', 'Disciplina Cursada 2'],
            'takenDiscCodes' => ['CURS123', 'CURS456'],
            'takenDiscYears' => [2022, 2023],
            'takenDiscGrades' => [9.5, 8.5],
            'takenDiscSemesters' => ['Primeiro', 'Segundo'],
            'takenDiscInstitutions' => ['Instituição Teste 1', 'Instituição Teste 2'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseHas('requisitions', [
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
        ]);

        $this->assertDatabaseHas('documents', [
            'type' => DocumentType::TAKEN_DISCS_RECORD,
        ]);

        $this->assertDatabaseHas('documents', [
            'type' => DocumentType::CURRENT_COURSE_RECORD,
        ]);

        $this->assertDatabaseHas('documents', [
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
        ]);

        $this->assertDatabaseHas('documents', [
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => 'Disciplina Cursada 1',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => 9.5,
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste 1',
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'name' => 'Disciplina Cursada 2',
            'code' => 'CURS456',
            'year' => 2023,
            'grade' => 8.5,
            'semester' => 'Segundo',
            'institution' => 'Instituição Teste 2',
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::SENT_TO_SG,
            'author_name' => 'test',
            'author_nusp' => '999999',
        ]);
    }

    public function test_create_requisition_as_student_retrieves_data_from_auth_user()
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'codpes' => '999999',
            'name' => 'Test Student',
            'email' => 'test@student.com',
            'current_role_id' => RoleId::STUDENT
        ]);

        $this->actingAs($user);

        $response = $this->post('/novo-requerimento', [
            'course' => 'Bacharelado em Ciência da Computação',
            'requestedDiscName' => 'Disciplina Teste',
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscCode' => 'TEST123',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações de teste',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada'],
            'takenDiscCodes' => ['CURS123'],
            'takenDiscYears' => [2022],
            'takenDiscGrades' => [9.5],
            'takenDiscSemesters' => ['Primeiro'],
            'takenDiscInstitutions' => ['Instituição Teste'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseHas('requisitions', [
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::SENT_TO_SG,
            'author_name' => 'Test Student',
            'author_nusp' => '999999',
        ]);
    }

    public function test_create_requisition_as_sg_retrieves_data_from_post_request()
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'codpes' => '999999',
            'name' => 'Test SG',
            'email' => 'test@sg.com',
            'current_role_id' => RoleId::SG
        ]);

        $this->actingAs($user);

        $response = $this->post('/novo-requerimento', [
            'student_nusp' => '888888',
            'student_name' => 'test_name',
            'email' => 'test@test.com',
            'course' => 'Bacharelado em Ciência da Computação',
            'requestedDiscName' => 'Disciplina Teste',
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscCode' => 'TEST123',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações de teste',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada'],
            'takenDiscCodes' => ['CURS123'],
            'takenDiscYears' => [2022],
            'takenDiscGrades' => [9.5],
            'takenDiscSemesters' => ['Primeiro'],
            'takenDiscInstitutions' => ['Instituição Teste'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseHas('requisitions', [
            'student_nusp' => '888888',
            'student_name' => 'test_name',
            'email' => 'test@test.com',
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::SENT_TO_SG,
            'author_name' => 'Test SG',
            'author_nusp' => '999999',
        ]);
    }

    public function test_update_requisition_page()
    {
        $user = User::factory()->create([
            'codpes' => '999999',
            'name' => 'test',
            'current_role_id' => RoleId::STUDENT
        ]);
        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
            'editable' => true,
        ]);

        $response = $this->get("/atualizar-requerimento/{$requisition->id}");

        $response->assertStatus(200);
    }

    public function test_update_requisition_page_returns_404_if_not_found()
    {
        $this->asRole(RoleId::STUDENT);

        $response = $this->get("/atualizar-requerimento/999999");

        $response->assertStatus(404);
    }

    public function test_update_requisition_page_returns_403_if_student_tries_to_access_not_owned_requisition()
    {
        $user = User::factory()->create([
            'codpes' => '999999',
            'current_role_id' => RoleId::STUDENT
        ]);

        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '888888',
            'student_name' => 'Another Student',
            'email' => 'another@student.com',
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
        ]);

        $response = $this->get("/atualizar-requerimento/{$requisition->id}");

        $response->assertStatus(403);
    }

    public function test_update_requisition_page_returns_403_if_not_editable()
    {
        $this->asRole(RoleId::STUDENT);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '888888',
            'student_name' => 'Another Student',
            'email' => 'another@student.com',
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
            'editable' => false,
        ]);

        $response = $this->get("/atualizar-requerimento/{$requisition->id}");

        $response->assertStatus(403);
    }

    public function test_update_requisition_page_is_always_accessible_by_sg()
    {
        $user = User::factory()->create([
            'codpes' => '999999',
            'current_role_id' => RoleId::SG
        ]);

        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '888888',
            'student_name' => 'Another Student',
            'email' => 'another@student.com',
            'requested_disc' => 'Disciplina Teste',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'TEST123',
            'department' => 'MAC',
            'observations' => 'Observações de teste',
            'editable' => false
        ]);

        $response = $this->get("/atualizar-requerimento/{$requisition->id}");

        $response->assertStatus(200);
    }

    public function test_update_requisition_returns_404_if_not_found()
    {
        $this->asRole(RoleId::STUDENT);

        $response = $this->post('/atualizar-requerimento', [
            'requisitionId' => '999999',
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações atualizadas',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada'],
            'takenDiscCodes' => ['CURS456'],
            'takenDiscYears' => [2023],
            'takenDiscGrades' => [8.5],
            'takenDiscSemesters' => ['Segundo'],
            'takenDiscInstitutions' => ['Instituição Atualizada'],
        ]);

        $response->assertStatus(404);
    }

    public function test_update_requisition_returns_403_if_student_does_not_own_requisition()
    {
        $this->asRole(RoleId::STUDENT);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '888888',
            'student_name' => 'Another Student',
            'email' => 'another@student.com',
            'requested_disc' => 'Disciplina Antiga',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'OLD123',
            'department' => 'MAP',
            'observations' => 'Observações antigas',
            'latest_version' => 1,
            'editable' => true,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações atualizadas',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada'],
            'takenDiscCodes' => ['CURS456'],
            'takenDiscYears' => [2023],
            'takenDiscGrades' => [8.5],
            'takenDiscSemesters' => ['Segundo'],
            'takenDiscInstitutions' => ['Instituição Atualizada'],
        ]);

        $response->assertStatus(403);
    }

    public function test_update_requisition_returns_403_if_not_editable()
    {
        $this->asRole(RoleId::STUDENT);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina Antiga',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'OLD123',
            'department' => 'MAP',
            'observations' => 'Observações antigas',
            'latest_version' => 1,
            'editable' => false,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações atualizadas',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada'],
            'takenDiscCodes' => ['CURS456'],
            'takenDiscYears' => [2023],
            'takenDiscGrades' => [8.5],
            'takenDiscSemesters' => ['Segundo'],
            'takenDiscInstitutions' => ['Instituição Atualizada'],
        ]);

        $response->assertStatus(403);
    }

    public function test_update_requisition_allows_sg_user_to_edit_any_requisition()
    {
        $this->asRole(RoleId::SG);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina Antiga',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'OLD123',
            'department' => 'MAP',
            'observations' => 'Observações antigas',
            'latest_version' => 1,
            'editable' => false
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => 9.5,
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_RECORD,
            'path' => 'takenDiscRecord.pdf',
            'hash' => 'takenDiscRecord.pdf',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::CURRENT_COURSE_RECORD,
            'path' => 'courseRecord.pdf',
            'hash' => 'courseRecord.pdf',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
            'path' => 'takenDiscSyllabus.pdf',
            'hash' => 'takenDiscSyllabus.pdf',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
            'path' => 'requestedDiscSyllabus.pdf',
            'hash' => 'requestedDiscSyllabus.pdf',
            'version' => 1,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações atualizadas',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada'],
            'takenDiscCodes' => ['CURS456'],
            'takenDiscYears' => [2023],
            'takenDiscGrades' => [8.5],
            'takenDiscSemesters' => ['Segundo'],
            'takenDiscInstitutions' => ['Instituição Atualizada'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'department' => 'MAC',
            'observations' => 'Observações atualizadas',
            'latest_version' => 2,
        ]);
    }

    public function test_update_requisition_rejects_incomplete_requests()
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'codpes' => '999999',
            'current_role_id' => RoleId::STUDENT
        ]);
        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina Antiga',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'OLD123',
            'department' => 'MAP',
            'observations' => 'Observações antigas',
            'latest_version' => 1,
            'editable' => true,
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => 9.5,
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_RECORD,
            'path' => 'takenDiscRecord.pdf',
            'hash' => 'takenDiscRecord.pdf', // not a real hash for testing purposes
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::CURRENT_COURSE_RECORD,
            'path' => 'courseRecord.pdf',
            'hash' => 'courseRecord.pdf', // not a real hash for testing purposes
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
            'path' => 'takenDiscSyllabus.pdf',
            'hash' => 'takenDiscSyllabus.pdf', // not a real hash for testing purposes
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
            'path' => 'requestedDiscSyllabus.pdf',
            'hash' => 'requestedDiscSyllabus.pdf', // not a real hash for testing purposes
            'version' => 1,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'requestedDiscName' => 'Disciplina Atualizada',
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscCode' => 'NEW123',
            'course' => 'Bacharelado em Ciência da Computação',
            'observations' => 'Observações atualizadas',
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada'],
            'takenDiscCodes' => ['CURS456'],
            'takenDiscYears' => [2023],
            'takenDiscGrades' => [8.5],
            'takenDiscSemesters' => ['Segundo'],
            'takenDiscInstitutions' => ['Instituição Atualizada'],
        ]);

        $response->assertSessionHasErrors(['requestedDiscDepartment']);
    }

    public function test_update_requisition()
    {
        Storage::fake('local');

        $this->asRole(RoleId::STUDENT);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'MAP123',
            'department' => 'MAP',
            'observations' => 'Observações antigas',
            'latest_version' => 1,
            'editable' => true
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => "9.5",
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
            'version' => 1,
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada 2',
            'code' => 'CURS456',
            'year' => 2023,
            'grade' => "7.5",
            'semester' => 'Segundo',
            'institution' => 'Instituição Teste',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_RECORD,
            'path' => 'takenDiscRecord.pdf',
            'hash' => 'takenDiscRecord.pdf', // not a real hash for testing purposes
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::CURRENT_COURSE_RECORD,
            'path' => 'courseRecord.pdf',
            'hash' => 'courseRecord.pdf', // not a real hash for testing purposes
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
            'path' => 'takenDiscSyllabus.pdf',
            'hash' => 'takenDiscSyllabus.pdf', // not a real hash for testing purposes
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
            'path' => 'requestedDiscSyllabus.pdf',
            'hash' => 'requestedDiscSyllabus.pdf', // not a real hash for testing purposes
            'version' => 1,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações atualizadas',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada'],
            'takenDiscCodes' => ['CURS456'],
            'takenDiscYears' => [2023],
            'takenDiscGrades' => [8.5],
            'takenDiscSemesters' => ['Segundo'],
            'takenDiscInstitutions' => ['Instituição Atualizada'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'department' => 'MAC',
            'observations' => 'Observações atualizadas',
            'latest_version' => 2,
        ]);

        $this->assertDatabaseHas('requisitions_versions', [
            'requisition_id' => $requisition->id,
            'version' => 1,
            'taken_disciplines_version' => 1,
            'taken_disc_record_version' => 1,
            'course_record_version' => 1,
            'taken_disc_syllabus_version' => 1,
            'requested_disc_syllabus_version' => 1
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada Atualizada',
            'code' => 'CURS456',
            'year' => 2023,
            'grade' => 8.5,
            'semester' => 'Segundo',
            'institution' => 'Instituição Atualizada',
            'version' => 2,
        ]);

        $this->assertDatabaseHas('documents', [
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_RECORD,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('documents', [
            'requisition_id' => $requisition->id,
            'type' => DocumentType::CURRENT_COURSE_RECORD,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('documents', [
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('documents', [
            'requisition_id' => $requisition->id,
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::UPDATED_BY_STUDENT,
            'requisition_id' => $requisition->id,
            'author_nusp' => '999999',
            'version' => 2,
        ]);
    }

    public function test_update_requisition_does_not_change_core_info()
    {
        Storage::fake('local');

        $this->asRole(RoleId::STUDENT);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina Antiga',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'OLD123',
            'department' => 'MAP',
            'observations' => 'Observações antigas',
            'latest_version' => 1,
            'editable' => true
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => "9.5",
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_RECORD,
            'path' => 'takenDiscRecord.pdf',
            'hash' => 'takenDiscRecord.pdf',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::CURRENT_COURSE_RECORD,
            'path' => 'courseRecord.pdf',
            'hash' => 'courseRecord.pdf',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
            'path' => 'takenDiscSyllabus.pdf',
            'hash' => 'takenDiscSyllabus.pdf',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
            'path' => 'requestedDiscSyllabus.pdf',
            'hash' => 'requestedDiscSyllabus.pdf',
            'version' => 1,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'student_nusp' => '111111',
            'student_name' => 'New Test Student',
            'email' => 'newtest@student.com',
            'requestedDiscName' => 'Disciplina Atualizada',
            'requestedDiscType' => 'Extracurricular',
            'requestedDiscCode' => 'NEW123',
            'course' => 'Bacharelado em Ciência da Computação',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações atualizadas',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada'],
            'takenDiscCodes' => ['CURS456'],
            'takenDiscYears' => [2023],
            'takenDiscGrades' => [8.5],
            'takenDiscSemesters' => ['Segundo'],
            'takenDiscInstitutions' => ['Instituição Atualizada'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseMissing('requisitions', [
            'student_nusp' => '111111',
        ]);
        $this->assertDatabaseMissing('requisitions', [
            'student_name' => 'New Test Student',
        ]);
        $this->assertDatabaseMissing('requisitions', [
            'email' => 'newtest@student.com',
        ]);
        $this->assertDatabaseMissing('requisitions', [
            'requested_disc' => 'Disciplina Atualizada',
        ]);
        $this->assertDatabaseMissing('requisitions', [
            'requested_disc_code' => 'NEW123',
        ]);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina Antiga',
            'requested_disc_type' => 'Extracurricular',
            'requested_disc_code' => 'OLD123',
        ]);

        $this->assertDatabaseHas('requisitions_versions', [
            'requisition_id' => $requisition->id,
            'version' => 1,
            'taken_disciplines_version' => 1,
            'taken_disc_record_version' => 1,
            'course_record_version' => 1,
            'taken_disc_syllabus_version' => 1,
            'requested_disc_syllabus_version' => 1
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada Atualizada',
            'code' => 'CURS456',
            'year' => 2023,
            'grade' => 8.5,
            'semester' => 'Segundo',
            'institution' => 'Instituição Atualizada',
            'version' => 2,
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => "9.5",
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
            'version' => 1,
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::UPDATED_BY_STUDENT,
            'requisition_id' => $requisition->id,
            'author_nusp' => '999999',
            'version' => 2,
        ]);
    }

    public function test_update_requisition_does_not_update_taken_disciplines_if_same()
    {
        Storage::fake('local');

        $this->asRole(RoleId::STUDENT);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'department' => 'MAP',
            'observations' => 'Observações antigas',
            'latest_version' => 1,
            'editable' => true
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => "9.5",
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_RECORD,
            'path' => 'takenDiscRecord.pdf',
            'hash' => 'takenDiscRecord.pdf',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::CURRENT_COURSE_RECORD,
            'path' => 'courseRecord.pdf',
            'hash' => 'courseRecord.pdf',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
            'path' => 'takenDiscSyllabus.pdf',
            'hash' => 'takenDiscSyllabus.pdf',
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
            'path' => 'requestedDiscSyllabus.pdf',
            'hash' => 'requestedDiscSyllabus.pdf',
            'version' => 1,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações atualizadas',
            'takenDiscRecord' => UploadedFile::fake()->create('takenDiscRecord.pdf', 100),
            'courseRecord' => UploadedFile::fake()->create('courseRecord.pdf', 100),
            'takenDiscSyllabus' => UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100),
            'requestedDiscSyllabus' => UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100),
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada'],
            'takenDiscCodes' => ['CURS123'],
            'takenDiscYears' => [2022],
            'takenDiscGrades' => ["9.5"],
            'takenDiscSemesters' => ['Primeiro'],
            'takenDiscInstitutions' => ['Instituição Teste'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseMissing('taken_disciplines', [
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => "9.5",
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
            'version' => 2,
        ]);

        $this->assertDatabaseHas('documents', [
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_RECORD,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('documents', [
            'requisition_id' => $requisition->id,
            'type' => DocumentType::CURRENT_COURSE_RECORD,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('documents', [
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('documents', [
            'requisition_id' => $requisition->id,
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'department' => 'MAC',
            'observations' => 'Observações atualizadas',
            'latest_version' => 2,
        ]);

        $this->assertDatabaseHas('requisitions_versions', [
            'requisition_id' => $requisition->id,
            'version' => 1,
            'taken_disciplines_version' => 1,
            'taken_disc_record_version' => 1,
            'course_record_version' => 1,
            'taken_disc_syllabus_version' => 1,
            'requested_disc_syllabus_version' => 1
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::UPDATED_BY_STUDENT,
            'requisition_id' => $requisition->id,
            'author_nusp' => '999999',
            'version' => 2,
        ]);
    }

    public function test_update_requisition_does_not_update_documents_if_same()
    {
        Storage::fake('local');

        $this->asRole(RoleId::STUDENT);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'department' => 'MAP',
            'observations' => 'Observações antigas',
            'latest_version' => 1,
            'editable' => true
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => "9.5",
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
            'version' => 1,
        ]);

        $takenDiscRecord = UploadedFile::fake()->create('takenDiscRecord.pdf', 100);
        $courseRecord = UploadedFile::fake()->create('courseRecord.pdf', 100);
        $takenDiscSyllabus = UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100);
        $requestedDiscSyllabus = UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_RECORD,
            'path' => $takenDiscRecord->store('local'),
            'hash' => hash_file('sha256', $takenDiscRecord->getRealPath()),
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::CURRENT_COURSE_RECORD,
            'path' => $courseRecord->store('local'),
            'hash' => hash_file('sha256', $courseRecord->getRealPath()),
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
            'path' => $takenDiscSyllabus->store('local'),
            'hash' => hash_file('sha256', $takenDiscSyllabus->getRealPath()),
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
            'path' => $requestedDiscSyllabus->store('local'),
            'hash' => hash_file('sha256', $requestedDiscSyllabus->getRealPath()),
            'version' => 1,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações atualizadas',
            'takenDiscRecord' => $takenDiscRecord,
            'courseRecord' => $courseRecord,
            'takenDiscSyllabus' => $takenDiscSyllabus,
            'requestedDiscSyllabus' => $requestedDiscSyllabus,
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Nova Disciplina Cursada'],
            'takenDiscCodes' => ['CURS123'],
            'takenDiscYears' => [2022],
            'takenDiscGrades' => ["9.5"],
            'takenDiscSemesters' => ['Primeiro'],
            'takenDiscInstitutions' => ['Instituição Teste'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseMissing('documents', [
            'requisition_id' => $requisition->id,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'requisition_id' => $requisition->id,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'department' => 'MAC',
            'observations' => 'Observações atualizadas',
            'latest_version' => 2,
        ]);

        $this->assertDatabaseHas('requisitions_versions', [
            'requisition_id' => $requisition->id,
            'version' => 1,
            'taken_disciplines_version' => 1,
            'taken_disc_record_version' => 1,
            'course_record_version' => 1,
            'taken_disc_syllabus_version' => 1,
            'requested_disc_syllabus_version' => 1
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::UPDATED_BY_STUDENT,
            'requisition_id' => $requisition->id,
            'author_nusp' => '999999',
            'version' => 2,
        ]);
    }

    public function test_update_requisition_correctly_reference_versions_on_requisition_versions()
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'codpes' => '999999',
            'current_role_id' => RoleId::STUDENT
        ]);
        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999999',
            'student_name' => 'Test Student',
            'email' => 'test@student.com',
            'requested_disc' => 'Disciplina',
            'requested_disc_type' => 'Obrigatória',
            'requested_disc_code' => 'DISC123',
            'department' => 'MAC',
            'observations' => 'Observações',
            'latest_version' => 1,
            'editable' => true
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisition->id,
            'name' => 'Disciplina Cursada',
            'code' => 'CURS123',
            'year' => 2022,
            'grade' => "9.5",
            'semester' => 'Primeiro',
            'institution' => 'Instituição Teste',
            'version' => 1,
        ]);

        $takenDiscRecord = UploadedFile::fake()->create('takenDiscRecord.pdf', 100);
        $courseRecord = UploadedFile::fake()->create('courseRecord.pdf', 100);
        $takenDiscSyllabus = UploadedFile::fake()->create('takenDiscSyllabus.pdf', 100);
        $requestedDiscSyllabus = UploadedFile::fake()->create('requestedDiscSyllabus.pdf', 100);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_RECORD,
            'path' => $takenDiscRecord->store('local'),
            'hash' => hash_file('sha256', $takenDiscRecord->getRealPath()),
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::CURRENT_COURSE_RECORD,
            'path' => $courseRecord->store('local'),
            'hash' => hash_file('sha256', $courseRecord->getRealPath()),
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::TAKEN_DISCS_SYLLABUS,
            'path' => $takenDiscSyllabus->store('local'),
            'hash' => hash_file('sha256', $takenDiscSyllabus->getRealPath()),
            'version' => 1,
        ]);

        Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => DocumentType::REQUESTED_DISC_SYLLABUS,
            'path' => $requestedDiscSyllabus->store('local'),
            'hash' => hash_file('sha256', $requestedDiscSyllabus->getRealPath()),
            'version' => 1,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'takenDiscRecord' => $takenDiscRecord,
            'courseRecord' => $courseRecord,
            'takenDiscSyllabus' => $takenDiscSyllabus,
            'requestedDiscSyllabus' => $requestedDiscSyllabus,
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada'],
            'takenDiscCodes' => ['CURS123'],
            'takenDiscYears' => [2022],
            'takenDiscGrades' => ["9.5"],
            'takenDiscSemesters' => ['Primeiro'],
            'takenDiscInstitutions' => ['Instituição Teste'],
        ]);

        $response->assertRedirect(route('list'));

        $this->assertDatabaseMissing('documents', [
            'requisition_id' => $requisition->id,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'requisition_id' => $requisition->id,
            'name' => "Disciplina Cursada Atualizada",
            'version' => 2,
        ]);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'latest_version' => 2,
        ]);

        $this->assertDatabaseHas('requisitions_versions', [
            'requisition_id' => $requisition->id,
            'version' => 1,
            'taken_disciplines_version' => 1,
            'taken_disc_record_version' => 1,
            'course_record_version' => 1,
            'taken_disc_syllabus_version' => 1,
            'requested_disc_syllabus_version' => 1
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::UPDATED_BY_STUDENT,
            'requisition_id' => $requisition->id,
            'author_nusp' => '999999',
            'version' => 2,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'takenDiscRecord' => $takenDiscRecord,
            'courseRecord' => $courseRecord,
            'takenDiscSyllabus' => $takenDiscSyllabus,
            'requestedDiscSyllabus' => $requestedDiscSyllabus,
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada de Novo'],
            'takenDiscCodes' => ['CURS123'],
            'takenDiscYears' => [2022],
            'takenDiscGrades' => ["9.5"],
            'takenDiscSemesters' => ['Primeiro'],
            'takenDiscInstitutions' => ['Instituição Teste'],
        ]);

        $this->assertDatabaseMissing('documents', [
            'requisition_id' => $requisition->id,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('taken_disciplines', [
            'requisition_id' => $requisition->id,
            'name' => "Disciplina Cursada Atualizada de Novo",
            'version' => 3,
        ]);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'latest_version' => 3,
        ]);

        $this->assertDatabaseHas('requisitions_versions', [
            'requisition_id' => $requisition->id,
            'version' => 2,
            'taken_disciplines_version' => 2,
            'taken_disc_record_version' => 1,
            'course_record_version' => 1,
            'taken_disc_syllabus_version' => 1,
            'requested_disc_syllabus_version' => 1
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::UPDATED_BY_STUDENT,
            'requisition_id' => $requisition->id,
            'author_nusp' => '999999',
            'version' => 3,
        ]);

        $response = $this->post("/atualizar-requerimento", [
            'requisitionId' => $requisition->id,
            'takenDiscRecord' => $takenDiscRecord,
            'courseRecord' => $courseRecord,
            'takenDiscSyllabus' => $takenDiscSyllabus,
            'requestedDiscSyllabus' => $requestedDiscSyllabus,
            'requestedDiscType' => 'Obrigatória',
            'requestedDiscDepartment' => 'MAC',
            'observations' => 'Observações atualizadas',
            'takenDiscCount' => 1,
            'takenDiscNames' => ['Disciplina Cursada Atualizada de Novo'],
            'takenDiscCodes' => ['CURS123'],
            'takenDiscYears' => [2022],
            'takenDiscGrades' => ["9.5"],
            'takenDiscSemesters' => ['Primeiro'],
            'takenDiscInstitutions' => ['Instituição Teste'],
        ]);

        $this->assertDatabaseMissing('documents', [
            'requisition_id' => $requisition->id,
            'version' => 2,
        ]);

        $this->assertDatabaseMissing('taken_disciplines', [
            'requisition_id' => $requisition->id,
            'version' => 4,
        ]);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'latest_version' => 4,
        ]);

        $this->assertDatabaseHas('requisitions_versions', [
            'requisition_id' => $requisition->id,
            'version' => 3,
            'taken_disciplines_version' => 3,
            'taken_disc_record_version' => 1,
            'course_record_version' => 1,
            'taken_disc_syllabus_version' => 1,
            'requested_disc_syllabus_version' => 1
        ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::UPDATED_BY_STUDENT,
            'requisition_id' => $requisition->id,
            'author_nusp' => '999999',
            'version' => 4,
        ]);
    }

    public function test_set_requisition_result_forbidden_for_non_sg_roles()
    {
        $nonAllowedRoles = [RoleId::STUDENT, RoleId::SECRETARY, RoleId::REVIEWER];

        foreach ($nonAllowedRoles as $roleIndex => $role) {
            $user = User::factory()->create([
                'codpes' => '111' . $roleIndex,
                'name' => 'Test User ' . $roleIndex,
                'email' => 'user' . $roleIndex . '@test.com',
                'current_role_id' => $role,
            ]);
            $this->actingAs($user);
            $requisition = Requisition::factory()->create([
                'student_nusp' => '111' . $roleIndex,
                'student_name' => 'Test User ' . $roleIndex,
                'email' => 'user' . $roleIndex . '@test.com',
                'editable' => true,
                'result' => 'Sem resultado',
                'latest_version' => 1,
            ]);
            $payload = [
                'requisitionId' => $requisition->id,
                'result' => 'Deferido',
                'result_text' => 'Approved by test'
            ];
            $response = $this->post('/dar-resultado-ao-requerimento', $payload);
            $response->assertStatus(403);
        }
    }

    public function test_set_requisition_result_success_for_sg()
    {
        $sgUser = User::factory()->create([
            'codpes' => '555555',
            'name' => 'Test SG New',
            'email' => 'sg_new@test.com',
            'current_role_id' => RoleId::SG,
        ]);
        $this->actingAs($sgUser);

        $studentUser = User::factory()->create([
            'codpes' => '666666',
        ]);

        $requisition = Requisition::factory()->create([
            'student_nusp' => $studentUser->codpes,
            'student_name' => 'Another Student New',
            'email' => 'another_new@test.com',
            'editable' => true,
            'result' => 'Sem resultado',
            'latest_version' => 1,
        ]);
        $payload = [
            'requisitionId' => $requisition->id,
            'result' => 'Deferido',
            'result_text' => 'Approved by test from SG'
        ];
        $response = $this->post('/dar-resultado-ao-requerimento', $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'result' => 'Deferido',
            'result_text' => 'Approved by test from SG',
            'situation' => EventType::ACCEPTED,
            'internal_status' => EventType::ACCEPTED,
        ]);
        $this->assertDatabaseHas('events', [
            'requisition_id' => $requisition->id,
            'type' => EventType::ACCEPTED,
            'author_nusp' => '555555',
            'version' => $requisition->latest_version,
        ]);
    }

    public function test_set_requisition_result_indeferido_with_empty_text_returns_error()
    {
        $user = User::factory()->create([
            'codpes' => '876543',
            'name' => 'Test SG User',
            'email' => 'sg_test_user@test.com',
            'current_role_id' => RoleId::SG,
        ]);
        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999876',
            'student_name' => 'Student Test',
            'email' => 'student_test@test.com',
            'editable' => true,
            'result' => 'Sem resultado',
            'result_text' => null,
            'latest_version' => 1,
        ]);

        $payload = [
            'requisitionId' => $requisition->id,
            'result' => 'Indeferido',
            'result_text' => ''  // Empty text
        ];

        $response = $this->post('/dar-resultado-ao-requerimento', $payload);
        $response->assertSessionHasErrors(['result_text']);

        $this->assertDatabaseMissing('events', [
            'requisition_id' => $requisition->id,
            'type' => EventType::REJECTED,
        ]);
    }

    public function test_mark_as_registered_success_for_sg()
    {
        $user = User::factory()->create([
            'codpes' => '123456',
            'name' => 'Test SG User',
            'email' => 'sg_user@test.com',
            'current_role_id' => RoleId::SG,
        ]);
        $this->actingAs($user);

        $requisition = Requisition::factory()->create([
            'student_nusp' => '999876',
            'student_name' => 'Student Test',
            'email' => 'student_test@test.com',
            'editable' => false,
            'department' => 'MAC',
            'result' => 'Deferido',
            'latest_version' => 1,
        ]);

        $payload = [
            'requisitionId' => $requisition->id
        ];

        $response = $this->post('/cadastrado', $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'internal_status' => "Registrado no Jupiter por Test SG User",
            'situation' => "Aguardando avaliação da CG",
        ]);

        $this->assertDatabaseHas('events', [
            'requisition_id' => $requisition->id,
            'type' => EventType::REGISTERED,
            'author_nusp' => '123456',
            'version' => $requisition->latest_version,
        ]);
    }

    public function test_mark_as_registered_success_for_sg_any_department()
    {
        // Use a department different from the requisition to test SG can mark any department
        $sgUser = User::factory()->create([
            'codpes' => '654321',
            'name' => 'Test SG User',
            'email' => 'sg_user_test@test.com',
            'current_role_id' => RoleId::SG,
        ]);

        $this->actingAs($sgUser);

        // Create a requisition with MAC department
        $requisition = Requisition::factory()->create([
            'student_nusp' => '999876',
            'student_name' => 'Student Test',
            'email' => 'student_test@test.com',
            'editable' => false,
            'department' => 'MAC',
            'result' => 'Deferido',
            'latest_version' => 1,
        ]);

        $payload = [
            'requisitionId' => $requisition->id
        ];

        $response = $this->post('/cadastrado', $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisition->id,
            'internal_status' => "Registrado no Jupiter por Test SG User",
            'situation' => "Aguardando avaliação da CG",
        ]);

        $this->assertDatabaseHas('events', [
            'requisition_id' => $requisition->id,
            'type' => EventType::REGISTERED,
            'author_nusp' => '654321',
            'version' => $requisition->latest_version,
        ]);

        // Now test with a different department to ensure SG can handle any department
        $requisitionMAE = Requisition::factory()->create([
            'student_nusp' => '888777',
            'student_name' => 'MAE Student',
            'email' => 'mae_student@test.com',
            'editable' => false,
            'department' => 'MAE', // Different department
            'result' => 'Deferido',
            'latest_version' => 1,
        ]);

        $payload = [
            'requisitionId' => $requisitionMAE->id
        ];

        $response = $this->post('/cadastrado', $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('requisitions', [
            'id' => $requisitionMAE->id,
            'internal_status' => "Registrado no Jupiter por Test SG User",
            'situation' => "Aguardando avaliação da CG",
        ]);
    }

    public function test_mark_as_registered_forbidden_for_secretary_of_different_department()
    {
        $userDepartment = DepartmentId::MAC;
        $requisitionDepartment = 'MAE';

        // Create a secretary user with MAC department role
        $user = User::factory()->create([
            'codpes' => '654321',
            'name' => 'Test Secretary User',
            'email' => 'secretary_user@test.com',
            'current_role_id' => RoleId::SECRETARY,
            'current_department_id' => $userDepartment
        ]);

        // Assign secretary role to MAC department
        $user->assignRole(RoleId::SECRETARY, $userDepartment);

        $this->actingAs($user);

        // Create a requisition with MAE department (different from secretary's department)
        $requisition = Requisition::factory()->create([
            'student_nusp' => '999876',
            'student_name' => 'Student Test',
            'email' => 'student_test@test.com',
            'editable' => false,
            'department' => $requisitionDepartment, // Different department (MAE) from the secretary
            'result' => 'Deferido',
            'latest_version' => 1,
        ]);

        $payload = [
            'requisitionId' => $requisition->id
        ];

        // Secretary should not be able to mark a requisition from a different department as registered
        $response = $this->post('/cadastrado', $payload);
        $response->assertStatus(403);

        // Verify that no event was created for marking as registered
        $this->assertDatabaseMissing('events', [
            'requisition_id' => $requisition->id,
            'type' => EventType::REGISTERED,
        ]);
    }
}
