<?php

namespace App\Service;

use App\Player;
use App\TeamPlayer;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @author andsalves
 */
class PlayersService {

    /** @var DatabaseManager */
    protected $databaseManager;

    public function __construct(DatabaseManager $databaseManager) {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @param array $playerData
     * @param array|null $teamsData
     * @return Player|null
     * @throws \Exception
     */
    public function createPlayer(array $playerData, array $teamsData = null) {
        $this->databaseManager->beginTransaction();

        try {
            /** @var Player $player */
            $player = Player::query()->create($playerData);

            foreach ($teamsData ?? [] as $playerTeamDetail) {
                $attributes = [
                    'team_id' => $playerTeamDetail['team_id'],
                    'player_id' => $player->id,
                ];

                TeamPlayer::query()->updateOrInsert($attributes, [
                    'position' => $playerTeamDetail['position'],
                    'number' => $playerTeamDetail['number']
                ]);
            }

            $this->databaseManager->commit();
            $player->refresh();

            return $player;
        } catch (\Exception $exception) {
            $this->databaseManager->rollBack();
            throw $exception;
        }
    }

    /**
     * @param Player $player
     * @param array $playerData
     * @param array|null $teamsData
     * @return boolean
     * @throws \Exception
     */
    public function updatePlayer(Player $player, array $playerData, array $teamsData = null) {
        $this->databaseManager->beginTransaction();

        try {
            if (!$player->update($playerData)) {
                return false;
            }

            foreach ($teamsData ?? [] as $playerTeamDetail) {
                $attributes = [
                    'team_id' => $playerTeamDetail['team_id'],
                    'player_id' => $player->id,
                ];

                if (!TeamPlayer::query()->updateOrInsert($attributes, [
                    'position' => $playerTeamDetail['position'],
                    'number' => $playerTeamDetail['number']
                ])) {
                    throw new \Exception('Unable to insert or update player team relation');
                }
            }

            if (is_array($teamsData)) {
                $deleteQuery = TeamPlayer::query()->where('player_id', '=', $player->id);

                if (!empty($teamsData)) {
                    $deleteQuery->whereNotIn('team_id', array_pluck($teamsData, 'team_id'));
                }

                $deleteQuery->delete();
            }

            $this->databaseManager->commit();
            $player->refresh();

            return true;
        } catch (\Exception $exception) {
            $this->databaseManager->rollBack();
            throw $exception;
        }
    }
}
