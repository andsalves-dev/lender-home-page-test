<?php

namespace App\Http\Requests;

use App\TeamPlayer;

class PlayerRequest extends BaseRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return array_merge([
            'first_name' => 'required',
            'last_name' => 'required',
        ], TeamPlayer::teamPlayerValidationRules());
    }

    public function messages() {
        $positionsStr = implode(', ', TeamPlayer::$validPositions);

        return [
            'teams.*.position.in' => "Position should be one of the following: {$positionsStr}",
        ];
    }
}
