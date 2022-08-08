<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    public function run()
    {
        DB::table('cities')->truncate();

        DB::table('cities')->insert([
            [
                'country_id' => 233,
                'state_id' =>  4380,
                'name' => 'Kyiv',
                'country_code' => 'UA'
            ],
            [
                'country_id' => 233,
                'state_id' =>  4380,
                'name' => 'Not Kyiv',
                'country_code' => 'UA'
            ]
        ]);
    }
}
