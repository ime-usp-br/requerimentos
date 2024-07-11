<?php

namespace Tests\Feature;

use App\Models\Event;
use Tests\TestCase;
use App\Models\User;
use App\Enums\RoleId;
use App\Models\Requisition;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecordTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $tablePageSize = 10;

    protected function setUp() : void 
    {
        parent::setUp();

        $this->seed();
        $this->setUpFaker();

    }
    public function test_requisition_record_is_showing_requisition_events_correctly()
    {
        $requisition = Requisition::factory()->create();

        Event::factory()->count($this->tablePageSize)->create([
            'requisition_id' => $requisition->id,
        ]);

        $notStudentUser = User::factory()->create([
            'current_role_id' => $this->faker->randomElement([RoleId::SG, RoleId::MAC_SECRETARY, RoleId::MAE_SECRETARY, RoleId::MAP_SECRETARY, RoleId::MAT_SECRETARY, RoleId::REVIEWER]),
        ]);

        $response = $this->actingAs($notStudentUser)->get(route('record.requisition', $requisition->id)); 

        $response->assertStatus(200);

        $events = Event::all();
        foreach ($events as $event) {
            $response->assertSee($event->message); 
            $response->assertSee($event->author_name);
            $response->assertSee($event->author_nusp);
        }
    }
}
