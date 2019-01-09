<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $defaultUsers = [
            [
                'name' => 'John',
                'email' => 'johndoe@gmail.com',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'Anderson',
                'email' => 'ands.alves.nunes@gmail.com',
                'password' => Hash::make('123456'),
            ]
        ];

        foreach ($defaultUsers as $defaultUser) {
            \App\User::query()->create($defaultUser);
        }
    }
}
