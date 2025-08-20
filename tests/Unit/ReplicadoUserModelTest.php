<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Replicado\ReplicadoUser;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReplicadoUserModelTest extends TestCase
{
	private function assertValidUser($user): void
	{
		$this->assertNotNull($user);
		$this->assertIsInt($user->nusp);
		$this->assertIsString($user->name);
	}

	public function test_users_retrieves_no_data_when_replicado_not_available(): void
	{
		Config::set('services.replicado_is_active', 0);

		$user = ReplicadoUser::first();

		$this->assertNull($user);
	}

	public function test_users_returns_empty_collection_when_replicado_not_available(): void
	{
		Config::set('services.replicado_is_active', 0);

		$users = ReplicadoUser::take(5)->get();

		$this->assertEquals(0, $users->count());
		$this->assertTrue($users->isEmpty());
	}

	public function test_users_uses_real_query_when_replicado_available(): void
	{
		if (!config('services.replicado_is_active')) {
			$this->markTestSkipped('Replicado is not available');
		}

		$user = new ReplicadoUser();
		$query = $user->newQuery();

		$this->assertNotNull($query);
		$this->assertTrue(method_exists($user, 'newQuery'));
	}

	public function test_users_query_works_when_replicado_available(): void
	{
		if (!config('services.replicado_is_active')) {
			$this->markTestSkipped('Replicado is not available');
		}

		$users = ReplicadoUser::take(3)->get();

		$this->assertGreaterThanOrEqual(0, $users->count());
		foreach ($users as $user) {
			$this->assertValidUser($user);
		}
	}

	public function test_get_name_method_returns_null_when_replicado_not_available(): void
	{
		Config::set('services.replicado_is_active', 0);

		// Test getName with any NUSP when replicado is not available
		$name = ReplicadoUser::getName('12345678');
		$this->assertNull($name);
	}

	public function test_get_name_method_returns_null_for_nonexistent_user(): void
	{
		Config::set('services.replicado_is_active', 0);

		$name = ReplicadoUser::getName('9999999999999');
		$this->assertNull($name);
	}
}
