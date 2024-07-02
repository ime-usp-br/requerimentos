<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Enums\RoleId;
use App\Models\Review;
use App\Models\Requisition;
use App\Models\TakenDisciplines;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequisitionReadTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $frontEndTablePageSize = 10;

    protected function setUp() : void 
    {
        parent::setUp();

        $this->seed();
        $this->setUpFaker();

    }

    public function test_new_requisitions_are_shown_on_student_list_page()
    {

        $studentUser = User::factory()->create([
            'current_role_id' => RoleId::STUDENT,
        ]);
        
        Requisition::factory()->count($this->frontEndTablePageSize)->create([
            'student_name' =>  $studentUser->name,
            'nusp' => $studentUser->codpes
        ]);

        $requisitions = Requisition::all();

        TakenDisciplines::factory()
                        ->count(3 * $this->frontEndTablePageSize)
                        ->create(['requisition_id' => $this->faker->randomElement($requisitions)->id,
                        ]);

        $response = $this->actingAs($studentUser)->get(route('student.list')); 

        $response->assertStatus(200);
        
        foreach ($requisitions as $requisition) {
            $takenDisciplines = TakenDisciplines::where('requisition_id', $requisition->id);

            foreach ($takenDisciplines as $takenDiscipline) {
                $response->assertSee($takenDiscipline->name);
            }
            
            $response->assertSee($requisition->situation);
            $response->assertSee($requisition->requested_disc);
        }
    }

    public function test_new_requisitions_are_shown_on_sg_list_page()
    {
        Requisition::factory()->count($this->frontEndTablePageSize)->create();

        $userFromSG = User::factory()->create([
            'current_role_id' => RoleId::SG,
        ]);

        $response = $this->actingAs($userFromSG)->get(route('sg.list')); 

        $response->assertStatus(200);

        $requisitions = Requisition::all();
        foreach ($requisitions as $requisition) {
            $response->assertSee($requisition->student_name); 
            $response->assertSee($requisition->nusp);
            $response->assertSee($requisition->internal_status);
            $response->assertSee($requisition->department);
        }
    }

    public function test_new_requisitions_are_shown_on_reviewer_list_page()
    {

        Requisition::factory()->count($this->frontEndTablePageSize)->create();

        $reviewer = User::factory()->create([
            'current_role_id' => RoleId::REVIEWER,
        ]);

        $requisitions = Requisition::all();

        Review::factory()->count($this->frontEndTablePageSize)->create([
            'requisition_id' => $this->faker->randomElement($requisitions)->id,
            'reviewer_nusp' => $reviewer->codpes,
            'reviewer_name' => $reviewer->name
        ]);

        $response = $this->actingAs($reviewer)->get(route('reviewer.list')); 

        $response->assertStatus(200);

        $reviews = Review::all();

        foreach ($reviews as $review) {
            $requisition = Requisition::where('id', $review->requisition_id)->first();

            $response->assertSee($requisition->student_name); 
            $response->assertSee($requisition->nusp);
            $response->assertSee($requisition->requested_disc);
        }
    }
}
