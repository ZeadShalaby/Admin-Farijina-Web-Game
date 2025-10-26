<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Models\Category;
use App\Models\MyGame;
use App\Models\MyGameCategory;
use App\Models\Question;
use App\Models\TempUserQuestionView;
use App\Models\UserQuestionView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyGameController extends Controller
{ // إضافة متغير لتخزين الأرقام المتاحة لكل لعبة


    /**
     * إنشاء لعبة جديدة
     */

    public function store(Request $request)
    {
        $validatedData = $this->validateGameData($request);

        DB::beginTransaction();

        try {
            $user = $request->user;

            // التحقق من توفر الأسئلة للفئات المطلوبة
            $this->checkCategoryAvailability($validatedData['categories'], $user);

            // التحقق من رصيد المستخدم وتحديثه
            $isFree = $this->updateUserGameBalance($user);

            // إنشاء اللعبة
            $game = $this->createGame($validatedData, $user, $isFree);

            // إضافة الفئات والأسئلة للعبة
            $this->attachCategoriesToGame($game, $validatedData['categories'], $user);

            DB::commit();

            return $this->getGameResponse($game, $user);
        } catch (GameException $e) {
            DB::rollBack();
            return errorResponse($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('خطأ في إنشاء اللعبة: ' . $e->getMessage(), 500);
        }
    }

    /**
     * التحقق من صحة بيانات اللعبة
     */
    private function validateGameData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'name_first_player' => 'required|string|max:100',
            'name_second_player' => 'required|string|max:100',
            'num_first_player' => 'required|integer|min:0',
            'num_second_player' => 'required|integer|min:0',
            'type_of_game' => 'required|string',
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|exists:categories,id',
        ]);
    }

    /**
     * التحقق من توفر الأسئلة للفئات المطلوبة
     */
    private function checkCategoryAvailability(array $categoryIds, $user): void
    {
        // التحقق من فئة الرعب (ID = 1) إذا كانت مطلوبة
        if (in_array(1, $categoryIds)) {
            $isHorrorAvailable = $this->checkHorrorCategoryAvailability($user);

            if (!$isHorrorAvailable) {
                throw new GameException("ستجدد قريبا", 500);
            }
        }
    }

    /**
     * التحقق من توفر أسئلة فئة الرعب
     */
    private function checkHorrorCategoryAvailability($user): bool
    {
        $pointLevels = [200, 400, 600];
        $requiredQuestionsPerLevel = 8;

        foreach ($pointLevels as $points) {
            $availableQuestions = $this->getAvailableQuestionsCount(1, $points, $user->id);

            if ($availableQuestions < $requiredQuestionsPerLevel) {
                return false;
            }
        }

        return true;
    }

    /**
     * حساب عدد الأسئلة المتاحة لفئة ونقاط معينة
     */
    private function getAvailableQuestionsCount(int $categoryId, int $points, int $userId): int
    {
        $totalQuestions = Question::where('category_id', $categoryId)
            ->where('points', $points)
            ->where('is_active', 1)
            ->count();

        $viewedQuestions = TempUserQuestionView::where('category_id', $categoryId)
            ->where('user_id', $userId)
            ->whereHas('question', function ($query) use ($points) {
                $query->where('points', $points);
            })
            ->count();

        return $totalQuestions - $viewedQuestions;
    }

    /**
     * تحديث رصيد ألعاب المستخدم
     */
    private function updateUserGameBalance($user): int
    {
        $isFree = 0;

        // التحقق من اللعبة المجانية
        if ($user->is_free) {
            $isFree = 1;
            $user->is_free = false;
        } else {
            // التحقق من رصيد الألعاب
            if ($user->num_of_games < 1) {
                throw new GameException("حصالة الألعاب لا تكفي", 500);
            }
        }
        $user->num_of_games -= 1;
        $user->save();

        return $isFree;
    }

    /**
     * إنشاء اللعبة الجديدة
     */
    private function createGame(array $validatedData, $user, int $isFree): MyGame
    {
        return MyGame::create([
            'user_id' => $user->id,
            'name' => $validatedData['name'],
            'name_first_player' => $validatedData['name_first_player'],
            'name_second_player' => $validatedData['name_second_player'],
            'num_first_player' => $validatedData['num_first_player'],
            'num_second_player' => $validatedData['num_second_player'],
            'type_of_game' => $validatedData['type_of_game'],
            'is_free' => $isFree,
        ]);
    }

    /**
     * ربط الفئات باللعبة وإنشاء الأسئلة المؤقتة
     */
    private function attachCategoriesToGame(MyGame $game, array $categoryIds, $user): void
    {
        $categoryData = [];

        foreach ($categoryIds as $categoryId) {
            // تحديث عدد المشاهدات للفئة
            Category::where('id', $categoryId)->increment('views');

            if ($game->type_of_game == 'horror') {
                // إنشاء الأسئلة المؤقتة لفئة الرعب
                $this->createTempQuestionsForCategoryHorror($game, $categoryId, $user);
            } else {
                // إنشاء الأسئلة المؤقتة للفئات الأخرى
                $this->createTempQuestionsForCategory($game, $categoryId, $user);
            }

            $categoryData[] = [
                'my_game_id' => $game->id,
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        MyGameCategory::insert($categoryData);
    }

    /**
     * إنشاء الأسئلة المؤقتة لفئة معينة
     */
    private function createTempQuestionsForCategory(MyGame $game, int $categoryId, $user): void
    {
        $availableNumbers = [1, 2, 3, 4, 5, 6];
        $pointLevels = [200, 400, 600];
        $positions = ['top', 'bottom'];

        foreach ($pointLevels as $points) {
            $questions = $this->getRandomQuestionsForCategory($categoryId, $points, $user->id, $game->is_free);

            foreach ($questions as $index => $question) {
                TempUserQuestionView::create([
                    'user_id' => $user->id,
                    'question_id' => $question->id,
                    'postion' => $game->type_of_game == 'luck' ? 'top' : $positions[$index],
                    'points' => $question->points,
                    'numper' =>  $this->createNumberForType($game->type_of_game, $availableNumbers),
                    'my_game_id' => $game->id,
                    'category_id' => $question->category_id,
                ]);
            }
        }
    }
    private function createNumberForType($type, &$availableNumbers): int
    {
        if ($type == 'vertebrae' || $type == 'yamaat') {
            return 1;
        } elseif ($type == 'luck') {
            return array_shift($availableNumbers);
        } elseif ($type == 'horror') {
            return rand(1, 4);
        } else {
            return 1;
        }
    }
    private function createTempQuestionsForCategoryHorror(MyGame $game, int $categoryId, $user): void
    {
        $pointLevels = [200, 400, 600];

        foreach ($pointLevels as $points) {
            // احصل على 8 أسئلة عشوائية لهذا المستوى من النقاط
            $questions = $this->getRandomQuestionsForCategory($categoryId, $points, $user->id, $game->is_free, 8);

            // تحقق أنه يوجد 8 أسئلة متاحة
            if ($questions->count() < 8) {
                throw new \Exception("Not enough questions available for points level $points.");
            }

            // أرقام من 1 إلى 8 بدون تكرار وبترتيب عشوائي
            $availableNumbers = collect(range(1, 8))->shuffle()->values();

            foreach ($questions as $index => $question) {
                TempUserQuestionView::create([
                    'user_id' => $user->id,
                    'question_id' => $question->id,
                    'postion' => 'top',
                    'points' => $question->points,
                    'numper' => $availableNumbers[$index],
                    'my_game_id' => $game->id,
                    'category_id' => $question->category_id,
                ]);
            }
        }
    }

    /*
     * الحصول على أسئلة عشوائية لفئة معينة
     */
    private function getRandomQuestionsForCategory(int $categoryId, int $points, int $userId, int $isFree, $limit = 2)
    {
        if ($isFree) {
            return  Question::where('is_active', 1)->where('is_free', $isFree)
                ->where('points', $points)
                ->where('category_id', $categoryId)
                ->whereNotExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('temp_user_question_views')
                        ->whereColumn('temp_user_question_views.question_id', 'questions.id')
                        ->where('temp_user_question_views.user_id', $userId);
                })
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        } else {
            return Question::where('is_active', 1)
                ->where('points', $points)
                ->where('category_id', $categoryId)
                ->whereNotExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('temp_user_question_views')
                        ->whereColumn('temp_user_question_views.question_id', 'questions.id')
                        ->where('temp_user_question_views.user_id', $userId);
                })
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }
    }

    /**
     * إرجاع استجابة اللعبة مع البيانات المطلوبة
     */
    private function getGameResponse(MyGame $game, $user)
    {
        $gameWithRelations = $user->games()
            ->with([
                'categories.category',
                'userQuestionViews.question'
            ])
            ->findOrFail($game->id);

        return successResponse(new GameResource($gameWithRelations));
    }

    /**
     * عرض جميع الألعاب للمستخدم
     */
    public function index(Request $request)
    {
        $user = $request->user;
        $type = $request->type;
        if ($type) {
            $games = $user->games()
                ->with(['categories.category', 'userQuestionViews.question'])
                ->where('type_of_game', $type)->orderBy('created_at', 'desc')
                ->get();
            return successResponse(GameResource::collection($games));
        }
        $games = $user->games()->where('type_of_game', '!=', 'horror')
            ->with(['categories.category', 'userQuestionViews.question'])->orderBy('created_at', 'desc')
            ->get();

        return successResponse(GameResource::collection($games));
    }
    public function show(Request $request, $gameId)
    {
        try {
            $user = $request->user;

            $game = $user->games()
                ->with(['categories.category', 'userQuestionViews.question'])
                ->where('id', $gameId)
                ->firstOrFail();

            return successResponse(new GameResource($game));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Game not found',
                'status_code' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving game: ' . $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
    public function updateGame(UpdateGameRequest $request, $gameId)
    {
        try {
            DB::beginTransaction();

            $game = MyGame::where('id', $gameId)
                // ->where('user_id', $request->user->id)                        
                ->firstOrFail();

            $game->update($request->validated());
            $newGame = MyGame::with(['categories.category', 'userQuestionViews.question'])->where("id", "=", $game->id)->first();
            DB::commit();

            return response()->json([
                'message' => 'Game updated successfully',
                'data' => $newGame,
                'status_code' => 200
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Game not found',
                'status_code' => 404
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating game: ' . $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
    public function duplicateGame(Request $request)
    {
        try {
            DB::beginTransaction();
            $gameId = $request->game_id;

            // Find original game
            $originalGame = MyGame::with(['categories'])
                ->where('id', $gameId)
                ->where('user_id', $request->user->id)
                ->first();


            // Create new game with updated team information
            $newGame = MyGame::create([
                'user_id' => $request->user->id,
                'name' => $originalGame->name,
                'type_of_game' => $originalGame->type_of_game,
                'name_first_player' => $request->name_first_player,
                'name_second_player' => $request->name_second_player,
                'num_first_player' => $request->num_first_player,
                'num_second_player' => $request->num_second_player,
                'num_of_play' => ($originalGame->num_of_play + 1),
                'is_free' =>  $originalGame->is_free,
                'first_player_points' => 0,
                'second_player_points' => 0,
                'first_player_no_answer' => 0,
                'first_player_al_jleeb' => 0,
                'first_player_tow_answer' => 0,
                'second_player_no_answer' => 0,
                'second_player_al_jleeb' => 0,
                'second_player_tow_answer' => 0,
                'second_player_vertebrae_one' => 0,
                'second_player_vertebrae_two' => 0,
                'first_player_vertebrae_one' => 0,
                'first_player_vertebrae_two' => 0,
            ]);

            $tempQuestion = TempUserQuestionView::where('my_game_id', $gameId)
                ->where('user_id', $request->user->id)
                ->get();
            foreach ($tempQuestion as $question) {
                TempUserQuestionView::create([
                    'user_id' => $question->user_id,
                    'question_id' => $question->question_id,
                    'postion' => $question->postion,
                    'points' => $question->points,
                    'numper' => $question->numper,
                    'my_game_id' => $newGame->id,
                    'category_id' => $question->category_id,
                ]);
            }

            // Duplicate categories
            foreach ($originalGame->categories as $category) {
                $newGame->categories()->create([
                    'category_id' => $category->category_id,
                ]);
            }

            // Delete original game
            $originalGame->delete();

            // Load relationships for response
            $newGame->load(['categories.category']);

            DB::commit();

            return successResponse(new GameResource($newGame), 200, 'Game duplicated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return errorResponse('Game not found' . $e->getMessage() . ' test error', 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Error duplicating game: ' . $e->getMessage(), 500);
        }
    }
}
// استثناء مخصص للألعاب
class GameException extends \Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
