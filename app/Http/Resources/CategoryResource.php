<?php

namespace App\Http\Resources;

use App\Models\Question;
use App\Models\TempUserQuestionView;
use App\Models\UserQuestionView;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = Auth::guard('sanctum')->user();
        // Get total questions count for this category
        $totalQuestions = $this->questions()->where('is_active', 1)->count();
        $totalQuestions_200 = $this->questions()->where('points', 200)->count();
        $totalQuestions_400 = $this->questions()->where('points', 400)->count();
        $totalQuestions_600 = $this->questions()->where('points', 600)->count();


        if (!$user) {
            // Handle unauthenticated case
            return [
                'id' => $this->id,
                'title' => $this->title,
                'description' => $this->description,
                'image' => url($this->image),
                'type' => $this->type,
                'end_at' => $this->end_at,
                'views' => $this->views,
                'is_active' => $this->is_active,
                'is_almost' => $this->is_almost,
                'is_draft' => $this->is_draft,
                'created_at' => $this->created_at,
                'total_questions' => $totalQuestions,
                'remaining_questions' => $totalQuestions,
                "number_games" => floor(min($totalQuestions_200, $totalQuestions_400, $totalQuestions_600) / 2),
            ];
        }

        $userId = $user->id;
        $userQuestionViews = TempUserQuestionView::where('category_id', $this->id)
            ->where('user_id', $user->id)
            ->get();

        // Count viewed questions by point value instead of summing points
        $viewedQuestions_200 = 0;
        $viewedQuestions_400 = 0;
        $viewedQuestions_600 = 0;

        foreach ($userQuestionViews as $userQuestionView) {
            $question = Question::find($userQuestionView->question_id);
            if ($question->points == 200) {
                $viewedQuestions_200++;
            } elseif ($question->points == 400) {
                $viewedQuestions_400++;
            } elseif ($question->points == 600) {
                $viewedQuestions_600++;
            }
        }

        $possibleGames = floor(min($totalQuestions_200, $totalQuestions_400, $totalQuestions_600) / 2);
        // Find the minimum available questions across all point categories
        $numGamesPlayed = ceil(max($viewedQuestions_200, $viewedQuestions_400, $viewedQuestions_600) / 2);
        // Get count of questions the user has already seen
        $seenQuestions = UserQuestionView::where('user_id', $userId)
            ->whereIn('question_id', $this->questions()->where('is_active', 1)->pluck('id'))
            ->count();

        // Calculate remaining questions
        $remainingQuestions = $totalQuestions - $seenQuestions;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'type' => $this->type,
            'end_at' => $this->end_at,
            'views' => $this->views,
            'is_active' => $this->is_active,
            'is_draft' => $this->is_draft,
            'created_at' => $this->created_at,
            'total_questions' => $totalQuestions,
            'remaining_questions' => $remainingQuestions,
            "number_games" => $possibleGames - $numGamesPlayed,
        ];
    }
}
