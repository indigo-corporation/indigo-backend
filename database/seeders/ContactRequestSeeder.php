<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserContactRequest;
use Illuminate\Database\Seeder;

class ContactRequestSeeder extends Seeder
{
    public function run()
    {
        $contact = User::find(2);

        foreach (User::whereNot('id', $contact->id)->get() as $user) {
            UserContactRequest::firstOrCreate([
                'user_id' => $user->id,
                'contact_id' => $contact->id,
            ]);
        }
    }
}
