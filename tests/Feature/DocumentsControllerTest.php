<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Requisition;
use App\Models\Document;
use App\Enums\RoleId;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentsControllerTest extends TestCase
{
	use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

		$this->seed(DatabaseSeeder::class);
    }

    private function createDocumentForUser($user, $type = 'Ementas das disciplinas cursadas')
    {
        $requisition = Requisition::factory()->create([
            'student_nusp' => $user->codpes,
            'owner_role_id' => RoleId::SG
        ]);
        $file = UploadedFile::fake()->create('test.pdf', 10, 'application/pdf');
        Storage::fake('local');
        $path = $file->store('test');
        return Document::factory()->create([
            'requisition_id' => $requisition->id,
            'type' => $type,
            'path' => $path,
        ]);
    }

    public function test_owner_student_can_access_document()
    {
        $student = User::factory()->create(['current_role_id' => RoleId::STUDENT]);
        $document = $this->createDocumentForUser($student);
        $this->actingAs($student);
        $response = $this->get("/documents/{$document->id}/view");
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_other_student_cannot_access_document()
    {
        $owner = User::factory()->create(['current_role_id' => RoleId::STUDENT]);
        $other = User::factory()->create(['current_role_id' => RoleId::STUDENT]);
        $document = $this->createDocumentForUser($owner);
        $this->actingAs($other);
        $response = $this->get("/documents/{$document->id}/view");
        $response->assertForbidden();
    }

    public function test_sg_can_access_any_document()
    {
        $owner = User::factory()->create(['current_role_id' => RoleId::STUDENT]);
        $sg = User::factory()->create(['current_role_id' => RoleId::SG]);
        $document = $this->createDocumentForUser($owner);
        $this->actingAs($sg);
        $response = $this->get("/documents/{$document->id}/view");
        $response->assertOk();
    }

    public function test_reviewer_can_access_any_document()
    {
        $owner = User::factory()->create(['current_role_id' => RoleId::STUDENT]);
        $reviewer = User::factory()->create(['current_role_id' => RoleId::REVIEWER]);
        $document = $this->createDocumentForUser($owner);
        $this->actingAs($reviewer);
        $response = $this->get("/documents/{$document->id}/view");
        $response->assertOk();
    }

    public function test_secretary_can_access_any_document()
    {
        $owner = User::factory()->create(['current_role_id' => RoleId::STUDENT]);
        $secretary = User::factory()->create(['current_role_id' => RoleId::SECRETARY]);
        $document = $this->createDocumentForUser($owner);
        $this->actingAs($secretary);
        $response = $this->get("/documents/{$document->id}/view");
        $response->assertOk();
    }
}
