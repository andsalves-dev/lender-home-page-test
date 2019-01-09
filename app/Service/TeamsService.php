<?php

namespace App\Service;

use App\Team;
use App\TeamPlayer;
use Illuminate\Database\DatabaseManager;

class TeamsService {

    /** @var DatabaseManager */
    protected $databaseManager;

    public function __construct(DatabaseManager $databaseManager) {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @param array $teamData
     * @param array|null $playersData
     * @return Team|null
     * @throws \Exception
     */
    public function createTeam(array $teamData, array $playersData = null) {
        $this->databaseManager->beginTransaction();

        try {
            /** @var Team $team */
            $team = Team::query()->create($teamData);

            foreach ($playersData ?? [] as $playerDetail) {
                $attributes = [
                    'team_id' => $team->id,
                    'player_id' => $playerDetail['player_id'],
                ];

                TeamPlayer::query()->updateOrInsert($attributes, [
                    'position' => $playerDetail['position'],
                    'number' => $playerDetail['number']
                ]);
            }

            $this->databaseManager->commit();
            $team->refresh();

            return $team;
        } catch (\Exception $exception) {
            $this->databaseManager->rollBack();
            throw $exception;
        }
    }

    /**
     * @param Team $team
     * @param array $teamData
     * @param array|null $playersData
     * @return boolean
     * @throws \Exception
     */
    public function updateTeam(Team $team, array $teamData, array $playersData = null) {
        $this->databaseManager->beginTransaction();

        try {
            if (!$team->update($teamData)) {
                return false;
            }

            foreach ($playersData ?? [] as $playerTeamDetail) {
                $attributes = [
                    'team_id' => $team->id,
                    'player_id' => $playerTeamDetail['player_id'],
                ];

                if (!TeamPlayer::query()->updateOrInsert($attributes, [
                    'position' => $playerTeamDetail['position'],
                    'number' => $playerTeamDetail['number']
                ])) {
                    throw new \Exception('Unable to insert or update player team relation');
                }
            }

            if (is_array($playersData)) {
                $deleteQuery = TeamPlayer::query()->where('team_id', '=', $team->id);

                if (!empty($playersData)) {
                    $deleteQuery->whereNotIn('player_id', array_pluck($playersData, 'player_id'));
                }

                $deleteQuery->delete();
            }

            $this->databaseManager->commit();
            $team->refresh();

            return true;
        } catch (\Exception $exception) {
            $this->databaseManager->rollBack();
            throw $exception;
        }
    }
}
