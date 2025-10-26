
<?php

use Illuminate\Foundation\Http\FormRequest;

class DuplicateGameRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'game_id' => 'required|exists:my_games,id',
            'name_first_player' => 'required|string|max:255',
            'name_second_player' => 'required|string|max:255',
            'num_first_player' => 'required|integer|min:0',
            'num_second_player' => 'required|integer|min:0',
        ];
    }
}
