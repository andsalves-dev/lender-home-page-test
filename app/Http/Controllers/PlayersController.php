<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlayerRequest;
use App\Player;
use App\Service\PlayersService;
use App\TeamPlayer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlayersController extends Controller {

    /** @var PlayersService */
    protected $playersService;

    public function __construct(PlayersService $playersService) {
        $this->playersService = $playersService;
    }

    public function store(PlayerRequest $request) {
        if ($request->getValidator()->fails()) {
            return response($request->getValidator()->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            return $this->playersService->createPlayer($request->all(), $request->post('teams', []));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage() . "\n - " . $exception->getTraceAsString());

            return response(['message' => 'Error while creating player'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index() {
        return Player::query()->get()->all();
    }

    public function show($id) {
        return Player::query()->find($id) ?? response(['message' => 'Player not found'], Response::HTTP_NOT_FOUND);
    }

    public function update(Player $player, Request $request) {
        $data = $request->all();
        $errorResponse = response(['message' => 'Player not updated'], Response::HTTP_INTERNAL_SERVER_ERROR);

        $validator = Validator::make($data, TeamPlayer::teamPlayerValidationRules());

        if ($validator->fails()) {
            return response($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if ($this->playersService->updatePlayer($player, $data, $data['teams'] ?? null)) {
                return $player;
            } else {
                return $errorResponse;
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage() . " - \n " . $e->getTraceAsString());
            return $errorResponse;
        }
    }

    /**
     * @param Player $player
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Player $player) {
        if ($player->delete()) {
            return response()->json(['message' => 'Player removed successfully']);
        } else {
            return response()->json(['message' => 'Error while removing player'])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
