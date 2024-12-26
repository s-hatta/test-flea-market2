<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = new DateTime();
        $params = [
            [
                'condition' => '良好',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'condition' => '目立った傷や汚れなし',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'condition' => 'やや傷や汚れあり',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'condition' => '状態が悪い',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('conditions')->insert($params);
    }
}
