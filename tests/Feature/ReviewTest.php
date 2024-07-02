<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Enums\RoleId;
use App\Models\Review;
use App\Enums\EventType;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Models\Requisition;
use App\Models\TakenDisciplines;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $req;

    protected function setUp() : void 
    {
        parent::setUp();

        $this->seed();
        $this->setUpFaker();

        // criando um requerimento que será enviado para o parecerista
        $requisitionId = $this->faker->numberBetween(1, 99999999);
        
        $this->req = Requisition::factory()->create([
            'id' => $requisitionId,
            'result' => 'Inconsistência nas informações',
            'nusp' => $this->faker->numberBetween(10000000, 99999999),
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisitionId,
            'id' => $this->faker->numberBetween(1, 99999999),
        ]);

        TakenDisciplines::factory()->create([
            'requisition_id' => $requisitionId,
            'id' => $this->faker->numberBetween(1, 99999999)
        ]);

        $documentTypes = [DocumentType::TAKEN_DISCS_RECORD, DocumentType::CURRENT_COURSE_RECORD, DocumentType::TAKEN_DISCS_SYLLABUS, DocumentType::REQUESTED_DISC_SYLLABUS];

        foreach ($documentTypes as $documentType) {
            Document::factory()->create([
                'requisition_id' => $requisitionId,
                'type' => $documentType,
            ]);
        }
    }
    
    public function test_review_was_successfully_created_on_reviewer_pick_page()
    {

        $requisitionId = $this->req->id;

        // criando um usuário para enviar o requerimento para o parecerista
        $currentRoleId = $this->faker->randomElement([RoleId::SG, RoleId::MAC_SECRETARY, RoleId::MAE_SECRETARY, RoleId::MAP_SECRETARY, RoleId::MAT_SECRETARY]);
        $senderUser = User::factory()->create([
            'current_role_id' => $currentRoleId,
            'codpes' => $this->faker->numberBetween(10000000, 99999999),
        ]);

        // criando um parecerista 
        $reviewerNUSP = $this->faker->numberBetween(10000000, 99999999);
        $reviewerName = $this->faker->name();
        User::factory()->create([
            'current_role_id' => RoleId::REVIEWER,
            'name' => $reviewerName,
            'codpes' => $reviewerNUSP,
        ]);

        $postData = [
            'nusp' => $reviewerNUSP,
            'name' => $reviewerName
        ];

        $response = $this->actingAs($senderUser)->post(route('reviewer.sendToReviewer', $requisitionId), $postData);

        $response->assertStatus(204);

        $this->assertDatabaseHas('events', [
            'type' => EventType::SENT_TO_REVIEWERS,
            'message' => "Enviado para o parecerista " . $reviewerName,
            'requisition_id' => $requisitionId,
            'version' => $this->req->latest_version,
            'author_name' => $senderUser->name,
            'author_nusp' => $senderUser->codpes
        ]);

        $this->assertDatabaseHas('requisitions', [
            'internal_status' => "Enviado para o parecerista " . $reviewerName,
            'situation' => EventType::SENT_TO_REVIEWERS,
            'validated' => true,
            'id' => $requisitionId,
        ]);

        $this->assertDatabaseHas('reviews', [
            'reviewer_nusp' => $reviewerNUSP,
            'requisition_id' => $requisitionId,
            'reviewer_decision' => 'Sem decisão',
            'justification' => null,
            'reviewer_name' => $reviewerName
        ]);

    }
}
