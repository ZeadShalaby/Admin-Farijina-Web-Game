<?php

namespace App\Http\Resources;

use App\Models\UserQuestionView;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

// CategoryGameResource.php
class CategoryGameResource extends JsonResource
{
    public function toArray($request)
    {
        // Get viewed questions points for this category in this game
        $viewedQuestionPoints = UserQuestionView::select('questions.points', 'user_question_views.postion', 'user_question_views.numper')
            ->join('questions', 'user_question_views.question_id', '=', 'questions.id')
            ->where('user_question_views.user_id', $request->user->id)
            ->where('user_question_views.my_game_id', $this->game_id)
            ->where('user_question_views.category_id', $this->id)
            ->get()
            ->map(function ($item) {
                $item->points = (int) $item->points;
                return $item;
            });

        return [
            'id' => $this->id,
            'title' => $this->title,
            'end_at' => $this->end_at,
            'description' => $this->description,
            'image' => url($this->image),
            'type' => $this->type,
            'viewed_question_points' => $viewedQuestionPoints->pluck('points')->toArray(),
            'viewed_question' => $viewedQuestionPoints,
        ];
    }
}
