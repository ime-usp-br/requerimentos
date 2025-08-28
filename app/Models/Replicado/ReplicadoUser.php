<?php

namespace App\Models\Replicado;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReplicadoUser extends Model
{
	protected $connection = null;
	protected $table = "dummy";
	public $timestamps = false;

	public function newQuery()
	{
		if (!config('services.replicado_is_active')) {
			if (app()->environment('production')) {
				throw new \Exception('You need replicado connection to run in production.');
			}

			Log::warning('Unavailable replicado credentials - this will not run in production');

			$query = parent::newQuery()->fromSub('SELECT NULL as nusp, NULL as name WHERE 1=0', 'subtable');
		} else {
			$this->connection = "replicado";
			$query = parent::newQuery()->fromSub(function ($query) {
				$query->select(
					'codpes AS nusp',
					'nompes AS name'
				)
					->from('VINCULOPESSOAUSP')
					->whereNull('dtafimvin');
			}, 'subtable');
		}

		return $query;
	}

	/**
	 * Get the name of a user by their NUSP
	 *
	 * @param string $nusp The NUSP identifier
	 * @return string|null The user's name or null if not found
	 */
	public static function getName($nusp)
	{
		$user = static::where('nusp', $nusp)->first();
		return $user ? $user->name : null;
	}
}
