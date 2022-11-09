<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RemoveUsersSeeder extends Seeder
{
    public function run()
    {
        User::where('telegram_id', '<>', null)->delete();
    }
}
