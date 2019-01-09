<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class TeamPlayer extends Model {

    protected $table = 'team_players';

    protected $fillable = ['team_id', 'player_id', 'position', 'number'];

    public function usesTimestamps() {
        return false;
    }

    public function player() {
        return $this->hasOne(Player::class, 'id', 'player_id')->without(['teams']);
    }

    public function team() {
        return $this->hasOne(Team::class, 'id', 'team_id')->without(['players']);
    }

    public static $validPositions = ['GK', 'DF', 'MF', 'FW'];

    /**
     * Represents validation for a set of a team's players, or a player's teams
     *
     * @param string $fieldName
     * @param bool $requireTeam
     * @param bool $requirePlayer
     * @return array
     */
    public static function teamPlayerValidationRules($fieldName = 'teams', $requireTeam = true, $requirePlayer = false) {
        return [
            "{$fieldName}" => 'nullable|array',
            "{$fieldName}.*.team_id" => [$requireTeam ? 'required' : 'nullable', 'exists:teams,id'],
            "{$fieldName}.*.player_id" => [$requirePlayer ? 'required' : 'nullable', 'exists:players,id'],
            "{$fieldName}.*.number" => 'required|numeric|min:1',
            "{$fieldName}.*.position" => ['required', Rule::in(self::$validPositions)]
        ];
    }
}
