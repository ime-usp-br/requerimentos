<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\DepartmentSeeder;
use App\Enums\RoleId;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(DepartmentSeeder::class);
    }

    private function asRole($roleId){
        $user = User::factory()->create([
            'codpes' => '999999',
            'current_role_id' => $roleId
        ]);
        $this->actingAs($user);
    }

    public function test_sg_can_add_all_roles()
    {
        $this->asRole(RoleID::SG);

        $user = User::factory()->create([
            'codpes' => '123456'
        ]);

        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'role' => "Serviço de Graduação",
            'department' => ''
        ]);

        $response->assertRedirect();
        $user->refresh(); 
        $this->assertTrue($user->hasRole(RoleID::SG));

        $response = $this->post('/dar-papel', [
            'nusp' =>  $user->codpes,
            'role' => 'Parecerista',
            'department' => ''
        ]);

        $response->assertRedirect();
        $user->refresh(); 
        $this->assertTrue($user->hasRole(RoleID::REVIEWER));

        $response = $this->post('/dar-papel', [
            'nusp' =>  $user->codpes,
            'role' => 'Secretaria',
            'department' => 'MAC'
        ]);

        $response->assertRedirect();
        $user->refresh(); 
        $this->assertTrue($user->departments()->where('code', 'MAC')->exists());        
    }

    public function test_secretary_can_add_reviewer_and_secretary()
    {
        $this->asRole(RoleID::SECRETARY);

        $user = User::factory()->create([
            'codpes' => '123456'
        ]);

        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'role' => 'Parecerista',
            'department' => ''
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue($user->hasRole('Parecerista'));
        
        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'role' => 'Secretaria',
            'department' => 'MAC'
        ]);
        
        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue($user->hasRole('Secretaria'));
        $this->assertTrue($user->departments()->where('code', 'MAC')->exists());
    }

    public function test_secretary_cannot_add_sg()
    {
        $this->asRole(RoleID::SECRETARY);

        $user = User::factory()->create([
            'codpes' => '123456'
        ]);
        
        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'role' => 'Serviço de Graduação',
            'department' => ''
        ]);

        $response->assertForbidden();
    }

    public function test_reviewer_cannot_add_roles(){
        $this->asRole(RoleID::REVIEWER);

        $user = User::factory()->create(['codpes' => '123456']);

        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'role' => 'Secretaria',
            'department' => 'MAC'
        ]);

        $response->assertForbidden();
    }

    public function test_student_cannot_add_roles()
    {
        $this->asRole(RoleID::STUDENT);

        $user = User::factory()->create(['codpes' => '123456']);

        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'role' => 'Secretaria',
            'department' => 'MAC'
        ]);

        $response->assertForbidden();
    }

    public function test_sg_can_remove_all_roles()
    {
        $this->asRole(RoleID::SG);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole('Parecerista');
        $user->assignRole('Serviço de Graduação');
        $user->assignRole('Secretaria');
        $department = Department::where('code', 'MAC')->first();
        $user->departments()->attach($department->id);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'role' => 'Secretaria',
            'department' => 'MAC'
        ]);

        $response->assertNoContent();
        $user->refresh();
        $this->assertFalse($user->hasRole('Secretaria'));
        $this->assertFalse($user->departments()->where('code', 'MAC')->exists());

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'role' => 'Parecerista',
            'department' => ''
        ]);

        $response->assertNoContent();
        $user->refresh();
        $this->assertFalse($user->hasRole('Parecerista'));

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'role' => 'Serviço de Graduação',
            'department' => ''
        ]);

        $response->assertNoContent();
        $user->refresh();
        $this->assertFalse($user->hasRole('Serviço de Graduação'));
    }

    public function test_secretary_can_remove_reviewer_and_secretary_roles()
    {
        $this->asRole(RoleID::SECRETARY);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole('Secretaria');
        $user->assignRole('Parecerista');
        $department = Department::where('code', 'MAC')->first();
        $user->departments()->attach($department->id);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'role' => 'Secretaria',
            'department' => 'MAC'
        ]);

        $response->assertNoContent();
        $user->refresh();
        $this->assertFalse($user->hasRole('Secretaria'));
        $this->assertFalse($user->departments()->where('code', 'MAC')->exists());

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'role' => 'Parecerista',
            'department' => ''
        ]);

        $response->assertNoContent();
        $user->refresh();
        $this->assertFalse($user->hasRole('Parecerista'));
    }

    public function test_secretary_cannot_remove_sg_role()
    {
        $this->asRole(RoleID::SECRETARY);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole('Serviço de Graduação');

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'role' => 'Serviço de Graduação',
            'department' => ''
        ]);

        $response->assertForbidden();
    }


    public function test_reviewer_cannot_remove_roles()
    {
        $this->asRole(RoleID::REVIEWER);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole('Secretaria');
        $department = Department::where('code', 'MAC')->first();
        $user->departments()->attach($department->id);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'role' => 'Secretaria',
            'department' => 'MAC'
        ]);

        $response->assertForbidden();
    }

    public function test_student_cannot_remove_roles()
    {
        $this->asRole(RoleID::STUDENT);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole('Secretaria');
        $department = Department::where('code', 'MAC')->first();
        $user->departments()->attach($department->id);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'role' => 'Secretaria',
            'department' => 'MAC'
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_switch_roles()
    {
        $user = User::factory()->create([
            'codpes' => '123456',
            'current_role_id' => RoleId::SG
        ]);
        $user->assignRole(RoleId::SG);
        $user->assignRole(RoleId::REVIEWER);
        $this->actingAs($user);

        $response = $this->post('/trocar-papel', [
            'role-switch' => RoleId::REVIEWER
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals(RoleId::REVIEWER, $user->current_role_id);

        $response = $this->post('/trocar-papel', [
            'role-switch' => RoleId::SG
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals(RoleId::SG, $user->current_role_id);
    }

    public function test_user_cannot_switch_to_role_they_do_not_own()
    {
        $user = User::factory()->create([
            'codpes' => '123456',
            'current_role_id' => RoleId::SG
        ]);
        $user->assignRole(RoleId::SG);
        $this->actingAs($user);

        $response = $this->post('/trocar-papel', [
            'role-switch' => RoleId::REVIEWER
        ]);

        $response->assertForbidden();
        $user->refresh();
        $this->assertEquals(RoleId::SG, $user->current_role_id);
    }
}