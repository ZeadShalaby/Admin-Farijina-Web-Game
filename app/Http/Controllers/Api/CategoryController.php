<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Question;
use App\Models\UserQuestionView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request as  ٌRequest;

class CategoryController extends Controller
{
    public function index(ٌRequest $request)
    {
        $categoriesNormal = Category::where('is_active', 1)
            ->where('type', 'normal')
            ->with('questions') // Eager load questions
            ->orderBy('position')
            ->get();

        $categoriesPremium = Category::where('is_active', 1)
            ->where('type', 'premium')
            ->with('questions') // Eager load questions
            ->orderBy('position')
            ->get();
        $user =  Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'categoriesNormal' => CategoryResource::collection($categoriesNormal),
                'categoriesPremium' => CategoryResource::collection($categoriesPremium),
                'activate_paragraphs' => false,
                'user' => null,
                "req" => $request->all(),

            ]);
        }

        $userId = $user->id;
        $totalQuestions_400 = Question::where('category_id', 2)->where('points', 400)->where('is_free', $user->is_free)->count();
        $totalQuestions_600 = Question::where('category_id', 2)->where('points', 600)->where('is_free', $user->is_free)->count();
        $userQuestionViews = UserQuestionView::where('category_id', 2)
            ->where('user_id',  $userId)
            ->get();

        // Count viewed questions by point value instead of summing points
        $viewedQuestions_400 = 0;
        $viewedQuestions_600 = 0;

        foreach ($userQuestionViews as $userQuestionView) {
            $question = Question::find($userQuestionView->question_id);
            if ($question->points == 400) {
                $viewedQuestions_400++;
            } elseif ($question->points == 600) {
                $viewedQuestions_600++;
            }
        }

        // Calculate remaining questions by point value
        $remainingQuestions_400 = $totalQuestions_400 - $viewedQuestions_400;
        $remainingQuestions_600 = $totalQuestions_600 - $viewedQuestions_600;
        $isParagraphsActive = $remainingQuestions_400 >= 2 && $remainingQuestions_600 >= 2 ? true : false;
        return response()->json([
            'categoriesNormal' => CategoryResource::collection($categoriesNormal),
            'categoriesPremium' => CategoryResource::collection($categoriesPremium),
            'activate_paragraphs' =>   $isParagraphsActive,
            'user' => $user,
            "test" => [
                "totalQuestions_400" => $totalQuestions_400,
                "totalQuestions_600" => $totalQuestions_600,
                "viewedQuestions_400" => $viewedQuestions_400,
                "viewedQuestions_600" => $viewedQuestions_600,
                "remainingQuestions_400" => $remainingQuestions_400,
                "remainingQuestions_600" => $remainingQuestions_600,
                "req" => $request->all(),
            ]
        ]);
    }
}
