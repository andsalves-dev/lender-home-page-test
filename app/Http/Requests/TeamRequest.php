<?php

namespace App\Http\Requests;

use App\Team;
use App\TeamPlayer;
use Illuminate\Validation\Rule;

class TeamRequest extends BaseRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return array_merge([
            'name' => 'required|unique:teams',
            'type' => ['required', Rule::in(Team::$validTypes)],
        ], TeamPlayer::teamPlayerValidationRules('players', false, true));
    }

    public function messages() {
        $positionsStr = implode(', ', TeamPlayer::$validPositions);
        $typesStr = implode(', ', Team::$validTypes);

        return [
            'players.*.position.in' => "Position should be one of the following: {$positionsStr}",
            'type.in' => "'type' field should be one of the following: {$typesStr}",
        ];
    }
}
