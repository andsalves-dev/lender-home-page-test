<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author andsalves
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property Team[] $playerTeams
 */
class Player extends Model {

    protected $table = 'players';

    protected $fillable = ['first_name', 'last_name'];

    protected $with = ['playerTeams'];

    public function usesTimestamps() {
        return false;
    }

    public function playerTeams() {
        return $this->hasMany(TeamPlayer::class, 'player_id', 'id')->with(['team']);
    }


}
