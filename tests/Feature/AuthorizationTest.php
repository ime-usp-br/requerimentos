<?php

namespace Tests\Feature;

use App\Enums\RoleId;
use Tests\TestCase;
use App\Models\User;
use App\Models\Document;
use App\Models\Requisition;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp() : void 
    {
        parent::setUp();

        $this->seed();
        $this->setUpFaker();
    }

    public function test_student_cant_access_other_students_documents()
    {

        Storage::fake('local');
        $fakeDocumentPath = 'test/document.pdf';
        Storage::disk('local')->put($fakeDocumentPath, 'Fake content');

        $requisitionId = $this->faker->unique()->numberBetween(1, 99999999);

        $fakeRequisition = Requisition::factory()->create([
            'id' => $requisitionId,
            'student_nusp' => $this->faker->unique()->numberBetween(10000000, 99999999),
        ]);

        $documentId = $this->faker()->numberBetween(1, 99999999);

        $fakeDocument = Document::factory()->create([
            'requisition_id' => $requisitionId,
            'id' => $documentId,
            'path' => $fakeDocumentPath,
        ]);

        $userThatShouldNotAccessDocument = User::factory()->create([
            'codpes' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'current_role_id' => RoleId::STUDENT,
        ]);

        $response = $this->actingAs($userThatShouldNotAccessDocument)->get(route('document.show', ['documentId' => $documentId]));

        $this->assertNotEquals($fakeRequisition->student_nusp, $userThatShouldNotAccessDocument->codpes);
        $this->assertEquals($fakeRequisition->id , $fakeDocument->requisition_id);
        $response->assertStatus(404);
    }

    public function test_student_cant_access_sg_list_page()
    {

        $studentUser = User::factory()->create([
            'current_role_id' => RoleId::STUDENT,
        ]);

        $response = $this->actingAs($studentUser)->get(route('sg.list'));

        $response->assertStatus(404);
    } 

    public function test_student_cant_access_reviewer_list_page()
    {
        $studentUser = User::factory()->create([
            'current_role_id' => RoleId::STUDENT,
        ]);

        $response = $this->actingAs($studentUser)->get(route('reviewer.list'));

        $response->assertStatus(404);
    } 
    
    public function test_student_cant_update_its_requisition_when_not_allowed_by_sg()
    {   
        $requisitionId = $this->faker->unique()->numberBetween(1, 99999999);
        $studentUserNUSP = $this->faker->unique()->numberBetween(10000000, 99999999);

        $studentUser = User::factory()->create([
            'current_role_id' => RoleId::STUDENT,
            'codpes' => $studentUserNUSP,
        ]);

        Requisition::factory()->create([
            'result' => 'Sem resultado',
            'id' => $requisitionId,
            'student_nusp' => $studentUserNUSP,
        ]);

        $response = $this->actingAs($studentUser)->get(route('student.edit', ['requisitionId' => $requisitionId]));

        $response->assertStatus(403);
    } 
    
    public function test_student_cant_see_other_students_requisition_detail_page()
    {
        
        $studentUser = User::factory()->create([
            'current_role_id' => RoleId::STUDENT,
            'codpes' => $this->faker->unique()->numberBetween(10000000, 99999999),
        ]);

        $requisitionId = $this->faker->unique()->numberBetween(1, 99999999);

        Requisition::factory()->create([
            'student_nusp' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'id' => $requisitionId
        ]);

        $response = $this->actingAs($studentUser)->get(route('student.show', ['requisitionId' => $requisitionId]));

        $response->assertStatus(404);
    }  
}
