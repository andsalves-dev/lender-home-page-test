<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Service\TeamsService;
use App\Team;
use App\TeamPlayer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TeamsController extends Controller {

    /** @var TeamsService */
    protected $teamsService;

    public function __construct(TeamsService $teamsService) {
        $this->teamsService = $teamsService;
    }

    public function store(TeamRequest $request) {
        if ($request->getValidator()->fails()) {
            return response($request->getValidator()->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            return $this->teamsService->createTeam($request->all(), $request->post('players', []));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage() . "\n - " . $exception->getTraceAsString());

            return response(['message' => 'Error while creating team'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index() {
        return Team::query()->get()->all();
    }

    public function show($id) {
        return Team::query()->find($id) ?? response(['message' => 'Team not found'], Response::HTTP_NOT_FOUND);
    }

    public function update(Team $team, Request $request) {
        $data = $request->all();
        $errorResponse = response(['message' => 'Team not updated'], Response::HTTP_INTERNAL_SERVER_ERROR);

        $validator = Validator::make($data, TeamPlayer::teamPlayerValidationRules('players', false, true));

        if ($validator->fails()) {
            return response($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if ($this->teamsService->updateTeam($team, $data, $data['players'] ?? null)) {
                return $team;
            } else {
                return $errorResponse;
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage() . " - \n " . $e->getTraceAsString());
            return $errorResponse;

        }
    }

    /**
     * @param Team $team
     * @return bool|null
     * @throws \Exception
     */
    public function destroy(Team $team) {
        if ($team->delete()) {
            return response()->json(['message' => 'Team removed successfully']);
        } else {
            return response()->json(['message' => 'Error while removing team'])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
