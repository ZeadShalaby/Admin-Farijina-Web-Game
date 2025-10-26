<?php



namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string',
            'type_of_game' => 'sometimes|string',
            'name_first_player' => 'sometimes|string',
            'name_second_player' => 'sometimes|string',
            'num_first_player' => 'sometimes|integer',
            'num_second_player' => 'sometimes|integer',
            'num_of_play' => 'sometimes|integer',
            'first_player_no_answer' => 'sometimes|boolean',
            'first_player_al_jleeb' => 'sometimes|boolean',
            'first_player_tow_answer' => 'sometimes|boolean',
            'second_player_no_answer' => 'sometimes|boolean',
            'second_player_al_jleeb' => 'sometimes|boolean',
            'second_player_tow_answer' => 'sometimes|boolean',
            'first_player_vertebrae_one' => 'sometimes|boolean',
            'first_player_vertebrae_two' => 'sometimes|boolean',
            'second_player_vertebrae_one' => 'sometimes|boolean',
            'second_player_vertebrae_two' => 'sometimes|boolean',
            'first_player_points' => 'sometimes|integer',
            'second_player_points' => 'sometimes|integer',
        ];
    }
}
