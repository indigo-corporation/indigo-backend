<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RemoveUsersSeeder extends Seeder
{
    public function run()
    {
        User::whereIsNotNull('telegram_id')->delete();
    }
}
