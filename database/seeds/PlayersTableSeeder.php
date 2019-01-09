<?php

use Illuminate\Database\Seeder;

class PlayersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $players = [
            [
                'id' => 1,
                'first_name' => 'Alisson',
                'last_name' => 'Ramses',
                'player_teams' => [
                    '1' => ['position' => 'GK', 'number' => 1],
                    '5' => ['position' => 'GK', 'number' => 1],
                ]
            ],
            [
                'id' => 2,
                'first_name' => 'Danilo',
                'last_name' => 'Luiz',
                'player_teams' => [
                    '1' => ['position' => 'DF', 'number' => 2],
                ]
            ],
            [
                'id' => 3,
                'first_name' => 'Rafinha',
                'last_name' => '',
                'player_teams' => [
                    '1' => ['position' => 'MF', 'number' => 11],
                    '4' => ['position' => 'MF', 'number' => 11],
                ]
            ],
            [
                'id' => 4,
                'first_name' => 'Roberto',
                'last_name' => 'Firmino',
                'player_teams' => [
                    '1' => ['position' => 'FW', 'number' => 20],
                    '5' => ['position' => 'FW', 'number' => 20],
                ]
            ],
            [
                'id' => 5,
                'first_name' => 'Neymar',
                'last_name' => 'Junior',
                'player_teams' => [
                    '1' => ['position' => 'FW', 'number' => 10],
                ]
            ],

            [
                'id' => 6,
                'first_name' => 'Iker',
                'last_name' => 'Casillas',
                'player_teams' => [
                    '2' => ['position' => 'GK', 'number' => 1],
                ]
            ],
            [
                'id' => 7,
                'first_name' => 'Jordi',
                'last_name' => 'Alba',
                'player_teams' => [
                    '2' => ['position' => 'DF', 'number' => 18],
                    '4' => ['position' => 'DF', 'number' => 4],
                ]
            ],
            [
                'id' => 8,
                'first_name' => 'Sergi',
                'last_name' => 'Roberto',
                'player_teams' => [
                    '2' => ['position' => 'MF', 'number' => 7],
                    '4' => ['position' => 'MF', 'number' => 77],
                ]
            ],
            [
                'id' => 9,
                'first_name' => 'Marco',
                'last_name' => 'Asensio',
                'player_teams' => [
                    '2' => ['position' => 'FW', 'number' => 20],
                ]
            ],

            [
                'id' => 10,
                'first_name' => 'Jordan',
                'last_name' => 'Pickford',
                'player_teams' => [
                    '3' => ['position' => 'GK', 'number' => 1],
                ]
            ],
            [
                'id' => 11,
                'first_name' => 'Joe',
                'last_name' => 'Gomez',
                'player_teams' => [
                    '3' => ['position' => 'DF', 'number' => 5],
                    '5' => ['position' => 'DF', 'number' => 5],
                ]
            ],
            [
                'id' => 12,
                'first_name' => 'Ross',
                'last_name' => 'Barkley',
                'player_teams' => [
                    '3' => ['position' => 'MF', 'number' => 7],
                ]
            ],
            [
                'id' => 13,
                'first_name' => 'Harry',
                'last_name' => 'Kane',
                'player_teams' => [
                    '3' => ['position' => 'FW', 'number' => 9],
                ]
            ],
        ];

        foreach ($players as $item) {
            \App\Player::query()->updateOrCreate(['id' => $item['id']], [
                'id' => $item['id'],
                'first_name' => $item['first_name'],
                'last_name' => $item['last_name'],
            ]);

            foreach ($item['player_teams'] as $teamId => $playerTeam) {
                \App\TeamPlayer::query()->updateOrCreate(
                    [
                        'team_id' => $teamId,
                        'player_id' => $item['id'],
                        'number' => $playerTeam['number'],
                    ],
                    array_merge(['team_id' => $teamId], $playerTeam)
                );
            }
        }
    }
}
