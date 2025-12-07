<?php

namespace App\Models\Replicado;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReplicadoSubject extends Model
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

            $query = parent::newQuery()->fromSub($this->fakeQuery(), 'subtable');
        } else {
            $this->connection = "replicado";
            $query = parent::newQuery()->fromSub(function ($query) {
                $query->select(
                    'coddis AS code',
                    DB::raw('MAX(nomdis) AS name')
                )
                    ->whereNull('dtadtvdis')
                    ->whereNotNull('dtaatvdis')
                    ->from('DISCIPLINAGR')
                    ->groupBy('coddis');
            }, 'dummy');
        }

        return $query;
    }


    private function fakeQuery()
    {
        $fakeData = [
            [
                "code" => "MAC0110",
                "name" => "Introdução à Computação"
            ],
            [
                "code" => "MAT0111",
                "name" => "Matemática I"
            ],
            [
                "code" => "MAT0112",
                "name" => "Matemática II"
            ],
            [
                "code" => "MAT0113",
                "name" => "Matemática III"
            ],
            [
                "code" => "MAT0114",
                "name" => "Matemática IV"
            ],
            [
                "code" => "MAT0115",
                "name" => "Matemática V"
            ]
        ];

        $query = collect($fakeData)->map(function ($row) {
            return "SELECT '{$row['code']}' as code,
                            '{$row['name']}' as name
                            ";
        })->implode(' UNION ALL ');

        return $query;
    }
}
