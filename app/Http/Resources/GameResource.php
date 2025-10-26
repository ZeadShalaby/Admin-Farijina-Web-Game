<?php

namespace App\Http\Resources;

use App\Models\UserQuestionView;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type_of_game' => $this->type_of_game,
            'num_of_play' => $this->num_of_play,
            'first_player' => [
                'name' => $this->name_first_player,
                'number' => $this->num_first_player,
                'points' => $this->first_player_points,
                'no_answer' => $this->first_player_no_answer,
                'al_jleeb' => $this->first_player_al_jleeb,
                'tow_answer' => $this->first_player_tow_answer,
                'first_player_vertebrae_one' => $this->first_player_vertebrae_one,
                'first_player_vertebrae_two' => $this->first_player_vertebrae_two,
            ],
            'second_player' => [
                'name' => $this->name_second_player,
                'number' => $this->num_second_player,
                'points' => $this->second_player_points,
                'no_answer' => $this->second_player_no_answer,
                'al_jleeb' => $this->second_player_al_jleeb,
                'tow_answer' => $this->second_player_tow_answer,
                'second_player_vertebrae_one' => $this->second_player_vertebrae_one,
                'second_player_vertebrae_two' => $this->second_player_vertebrae_two,
            ],
            'categories' => CategoryGameResource::collection($this->categories->map(function ($gameCategory) {
                $gameCategory->category['game_id'] = $this->id;
                return $gameCategory->category;
            })),
            'created_at' => $this->created_at,
        ];
    }
}
