<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author andsalves
 *
 * @property integer $id
 * @property string $name
 * @property Player[] $players
 */
class Team extends Model {

    protected $table = 'teams';

    protected $fillable = ['name', 'type'];

    protected $with = ['players'];

    public function players() {
        return $this->hasManyThrough(Player::class, TeamPlayer::class, 'team_id', 'id', 'id', 'player_id');
    }

    public static $validTypes = ['national-team', 'club'];
}
