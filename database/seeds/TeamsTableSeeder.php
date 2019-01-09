<?php

use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $teams = [
            [
                'id' => 1,
                'name' => 'Brazil',
                'type' => 'national-team'
            ],
            [
                'id' => 2,
                'name' => 'Spain',
                'type' => 'national-team'
            ],
            [
                'id' => 3,
                'name' => 'England',
                'type' => 'national-team'
            ],
            [
                'id' => 4,
                'name' => 'Barcelona',
                'type' => 'club'
            ],
            [
                'id' => 5,
                'name' => 'Liverpool',
                'type' => 'club'
            ]
        ];

        foreach ($teams as $team) {
            \App\Team::query()->updateOrCreate(['id' => $team['id']], $team);
        }
    }
}
