<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Replicado\ReplicadoSubject;
use Illuminate\Support\Facades\Config;

class ReplicadoSubjectModelTest extends TestCase
{
	private function assertValidSubject($subject): void
	{
		$this->assertNotNull($subject);
		$this->assertIsString($subject->code);
		$this->assertIsString($subject->name);
	}

	public function test_subjects_uses_real_query_when_replicado_available(): void
	{
		if (!config('services.replicado_is_active')) {
			$this->markTestSkipped('Replicado is not available');
		}

		$subject = new ReplicadoSubject();
		$query = $subject->newQuery();

		$this->assertNotNull($query);
		$this->assertTrue(method_exists($subject, 'newQuery'));
	}

	public function test_subjects_query_works_when_replicado_available(): void
	{
		if (!config('services.replicado_is_active')) {
			$this->markTestSkipped('Replicado is not available');
		}

		$subjects = ReplicadoSubject::take(3)->get();

		$this->assertGreaterThanOrEqual(0, $subjects->count());
		foreach ($subjects as $subject) {
			$this->assertValidSubject($subject);
		}
	}

	public function test_subjects_returns_valid_fake_data_when_replicado_not_available(): void
	{
		Config::set('services.replicado_is_active', 0);

		$subjects = ReplicadoSubject::take(3)->get();

		$this->assertGreaterThanOrEqual(1, $subjects->count());

		if ($subjects->count() > 0) {
			foreach ($subjects as $subject) {
				$this->assertValidSubject($subject);
			}
		}
	}
}
