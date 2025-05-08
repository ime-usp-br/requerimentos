<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\DepartmentSeeder;
use App\Enums\RoleId;
use App\Enums\DepartmentId;
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
            'id' => '123456',
            'codpes' => '123456'
        ]);

        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'roleId' => RoleId::SG,
            'departmentId' => ''
        ]);
        $response->assertRedirect();
        $user->refresh();
        $this->assertDatabaseHas('department_user_roles', [
            'user_id' => $user->id,
            'role_id' => RoleId::SG,
            'department_id' => null
        ]);

        $response = $this->post('/dar-papel', [
            'nusp' =>  $user->codpes,
            'roleId' => RoleId::REVIEWER,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertRedirect();
        $user->refresh(); 
        $this->assertDatabaseHas('department_user_roles', [
            'user_id' => $user->id,
            'role_id' => RoleId::REVIEWER,
            'department_id' => DepartmentId::MAC
        ]);

        $response = $this->post('/dar-papel', [
            'nusp' =>  $user->codpes,
            'roleId' => RoleId::SECRETARY,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertRedirect();
        $user->refresh(); 
        $this->assertDatabaseHas('department_user_roles', [
            'user_id' => $user->id,
            'role_id' => RoleId::SECRETARY,
            'department_id' => DepartmentId::MAC
        ]);
    }

    public function test_secretary_can_add_reviewer_and_secretary()
    {
        $this->asRole(RoleID::SECRETARY);

        $user = User::factory()->create([
            'codpes' => '123456'
        ]);

        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'roleId' => RoleId::REVIEWER,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertDatabaseHas('department_user_roles', [
            'user_id' => $user->id,
            'role_id' => RoleId::REVIEWER,
            'department_id' => DepartmentId::MAC
        ]);
        
        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'roleId' => RoleId::SECRETARY,
            'departmentId' => DepartmentId::MAC
        ]);
        
        $response->assertRedirect();
        $user->refresh();
        $this->assertDatabaseHas('department_user_roles', [
            'user_id' => $user->id,
            'role_id' => RoleId::SECRETARY,
            'department_id' => DepartmentId::MAC
        ]);
    }

    public function test_secretary_cannot_add_sg()
    {
        $this->asRole(RoleID::SECRETARY);

        $user = User::factory()->create([
            'codpes' => '123456'
        ]);
        
        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'roleId' => RoleId::SG,
            'departmentId' => null
        ]);

        $response->assertForbidden();
    }

    public function test_reviewer_cannot_add_roles(){
        $this->asRole(RoleID::REVIEWER);

        $user = User::factory()->create(['codpes' => '123456']);

        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'roleId' => RoleId::SECRETARY,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertForbidden();
    }

    public function test_student_cannot_add_roles()
    {
        $this->asRole(RoleID::STUDENT);

        $user = User::factory()->create(['codpes' => '123456']);

        $response = $this->post('/dar-papel', [
            'nusp' => $user->codpes,
            'roleId' => RoleId::SECRETARY,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertForbidden();
    }

    public function test_sg_can_remove_all_roles()
    {
        $this->asRole(RoleID::SG);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole(RoleId::SECRETARY, DepartmentId::MAC);
        $user->assignRole(RoleId::REVIEWER, DepartmentId::MAC);
        $user->assignRole(RoleId::SG, null);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'roleId' => RoleId::SECRETARY,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertSuccessful();
        $user->refresh();
        $this->assertDatabaseMissing('department_user_roles', [
            'user_id' => $user->id,
            'role_id' => RoleId::SECRETARY,
            'department_id' => DepartmentId::MAC
        ]);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'roleId' => RoleId::REVIEWER,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertSuccessful();
        $user->refresh();
        $this->assertDatabaseMissing('department_user_roles', [
            'user_id' => $user->id,
            'role_id' => RoleId::REVIEWER,
            'department_id' => DepartmentId::MAC
        ]);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'roleId' => RoleId::SG,
            'departmentId' => null
        ]);

        $response->assertSuccessful();
        $user->refresh();
        $this->assertDatabaseMissing('department_user_roles', [
            'user_id' => $user->id,
            'role_id' => RoleId::SG,
            'department_id' => null
        ]);
    }

    public function test_secretary_can_remove_reviewer_and_secretary_roles()
    {
        $this->asRole(RoleID::SECRETARY);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole(RoleId::SECRETARY, DepartmentId::MAC);
        $user->assignRole(RoleId::REVIEWER, DepartmentId::MAC);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'roleId' => RoleId::SECRETARY,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertSuccessful();
        $user->refresh();
        $this->assertFalse($user->hasRole(RoleId::SECRETARY, DepartmentId::MAC));

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'roleId' => RoleId::REVIEWER,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertSuccessful();
        $user->refresh();
        $this->assertFalse($user->hasRole(RoleId::REVIEWER, DepartmentId::MAC));
    }

    public function test_secretary_cannot_remove_sg_role()
    {
        $this->asRole(RoleID::SECRETARY);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole(RoleId::SG, null);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'roleId' => RoleId::SG,
            'departmentId' => ''
        ]);

        $response->assertForbidden();
    }


    public function test_reviewer_cannot_remove_roles()
    {
        $this->asRole(RoleID::REVIEWER);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole(RoleId::SECRETARY, DepartmentId::MAC);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'roleId' => RoleId::SECRETARY,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertForbidden();
    }

    public function test_student_cannot_remove_roles()
    {
        $this->asRole(RoleID::STUDENT);

        $user = User::factory()->create(['codpes' => '123456']);
        $user->assignRole(RoleId::SECRETARY, DepartmentId::MAC);

        $response = $this->post('/remover-papel', [
            'nusp' => '123456',
            'roleId' => RoleId::SECRETARY,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_switch_roles()
    {
        $user = User::factory()->create([
            'codpes' => '123456',
            'current_role_id' => RoleId::SG
        ]);
        $user->assignRole(RoleId::SG, null);
        $user->assignRole(RoleId::REVIEWER, DepartmentId::MAC);
        $this->actingAs($user);

        $response = $this->post('/trocar-papel', [
            'roleId' => RoleId::REVIEWER,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals(RoleId::REVIEWER, $user->current_role_id);
        $this->assertEquals(DepartmentId::MAC, $user->current_department_id);

        $response = $this->post('/trocar-papel', [
            'roleId' => RoleId::SG,
            'departmentId' => "",
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals(RoleId::SG, $user->current_role_id);
        $this->assertNull($user->current_department_id);
    }

    public function test_user_cannot_switch_to_role_they_do_not_own()
    {
        $user = User::factory()->create([
            'codpes' => '123456',
            'current_role_id' => RoleId::SG
        ]);
        $user->assignRole(RoleId::SG, null);
        $this->actingAs($user);

        $response = $this->post('/trocar-papel', [
            'roleId' => RoleId::REVIEWER,
            'departmentId' => DepartmentId::MAC
        ]);

        $response->assertForbidden();
        $user->refresh();
        $this->assertEquals(RoleId::SG, $user->current_role_id);
    }
}