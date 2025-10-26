<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Coupon;
use App\Models\MyGame;
use App\Models\Category;
use App\Models\Question;
use App\Models\ContactUs;
use App\Models\CouponUsage;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\TempUserQuestionView;
use BaconQrCode\Renderer\ImageRenderer;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;


class QuestionController extends Controller
{
    use \App\Traits\ImageProcessing;

    public function show(Request $request)
    {
        // Create validation
        Log::info('Validating request parameters', $request->all());
        $request->validate([
            'points' => 'required|numeric|min:200|max:600',
            'category_id' => 'required|integer|exists:categories,id',
            "my_game_id" => 'required|integer|exists:my_games,id',
            'numper' => 'nullable|integer|min:1|max:16',
            'postion' => 'required|string|in:top,bottom',
        ]);

        // Log the validated parameters
        Log::info('Validation passed', [
            'points' => $request->points,
            'category_id' => $request->category_id,
            'my_game_id' => $request->my_game_id,
            'numper' => $request->numper,
            'postion' => $request->postion
        ]);

        $user = $request->user;
        $userId = $user->id;

        // Log user details
        Log::info('User details', ['user_id' => $userId]);

        $myGame = MyGame::where('id', $request->my_game_id)->first();

        // Log the game details
        Log::info('MyGame details', ['my_game_id' => $myGame->id, 'is_free' => $myGame->is_free]);

        // Make sure category_id is treated as an integer
        $categoryId = (int) $request->category_id;
        $points = (int) $request->points;

        // Log the category and points after conversion
        Log::info('Converted parameters', ['category_id' => $categoryId, 'points' => $points]);
        if ($myGame->type_of_game == 'luck') {
            // Check if the question is in TempUserQuestionView table or not
            $tempQuestion = TempUserQuestionView::where('user_id', $userId)
                ->where('my_game_id', $request->my_game_id)
                ->where('category_id', $categoryId)
                ->whereNotIn('question_id', function ($query) use ($userId, $request, $categoryId) {
                    $query->select('question_id')
                        ->from('user_question_views')
                        ->where('user_id', $userId)
                        ->where('my_game_id', $request->my_game_id)
                        ->where('category_id', $categoryId);
                })
                ->inRandomOrder()->first();
        } else {
            // Check if the question is in TempUserQuestionView table or not
            $tempQuestion = TempUserQuestionView::where('user_id', $userId)
                ->where('my_game_id', $request->my_game_id)
                ->where('postion', $request->postion)
                ->where('category_id', $categoryId) // Use the integer value
                ->where('points', $points) // Use the integer value
                ->where('numper', $request->numper)
                ->first();
        }



        // Log the query result for temp question
        Log::info('Checking TempUserQuestionView', [
            'user_id' => $userId,
            'my_game_id' => $request->my_game_id,
            'postion' => $request->postion,
            'category_id' => $categoryId,
            'points' => $points,
            'numper' => $request->numper,
            'temp_question_found' => $tempQuestion ? true : false
        ]);

        if ($tempQuestion) {
            $question = Question::where('id', $tempQuestion->question_id)->first();
            $question['category_name'] = $question->category_id ? $question->category->title : null;

            // Debug information for found question
            Log::info('Found question in temp views', [
                'question_id' => $question->id,
                'category_id' => $question->category_id,
                'requested_category' => $categoryId
            ]);

            $question['request'] = $request->all();
            return successResponse($question);
        }

        // Build the base query
        $query = Question::where('is_active', 1)
            ->where('is_free', $myGame->is_free ? 1 : 0)
            ->where('points', $points)
            ->where('category_id', $categoryId); // Use the integer value

        // Log the query parameters
        Log::info('Query parameters for question search', [
            'is_free' => $myGame->is_free ? 1 : 0,
            'points' => $points,
            'category_id' => $categoryId,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        // Exclude questions the user has already seen
        $query->whereNotIn('id', function ($subquery) use ($userId) {
            $subquery->select('question_id')
                ->from('user_question_views')
                ->where('user_id', $userId);
        })->whereNotIn('id', function ($subquery) use ($userId) {
            $subquery->select('question_id')
                ->from('temp_user_question_views')
                ->where('user_id', $userId);
        });
        // Get a random question
        $question = $query->first(); //inRandomOrder()->
        $question['category_name'] = $question->category_id ? $question->category->title : null;

        if (!$question) {
            // Log when no question is found
            Log::info('No question found for the selected criteria', [
                'category_id' => $categoryId,
                'points' => $points
            ]);
            return errorResponse('No new questions available for the selected criteria.');
        }

        // Log the selected question
        Log::info('Selected question', [
            'question_id' => $question->id,
            'category_id' => $question->category_id,
            'requested_category' => $categoryId
        ]);

        // Store the question view in temporary table
        TempUserQuestionView::create([
            'user_id' => $userId,
            'question_id' => $question->id,
            'postion' => $request->postion,
            'points' => $points,
            'numper' => $request->numper,
            'my_game_id' => $request->my_game_id,
            'category_id' => $categoryId,
        ]);

        // Log the storage of the question view
        Log::info('Stored question view in temp_user_question_views', [
            'user_id' => $userId,
            'question_id' => $question->id,
            'category_id' => $categoryId
        ]);

        $question['request'] = $request->all();
        return successResponse($question);
    }

    public function showQuestionHrorr(Request $request)
    {
        // create validation
        $request->validate([
            'points' => 'required|numeric|min:200|max:600',
            "my_game_id" => 'required|integer|exists:my_games,id',
        ]);
        $user = $request->user;
        $userId = $user->id;
        // Get a random question that the user has not seen before
        $question = Question::where('is_active', 1)
            ->where('points', $request->points)
            ->where('type', "horror")
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('question_id')
                    ->from('user_question_views')
                    ->where('user_id', $userId);
            })
            ->inRandomOrder()
            ->first();
        // If a question is found, record that the user has viewed it
        if ($question) {
            return successResponse($question);
        }

        // If no questions are found
        return errorResponse('No new questions available for the selected criteria.');
    }
    public function storeQuestionView(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'my_game_id' => 'required|integer',
            'postion' => 'required',
            'numper' => 'nullable|integer|min:1|max:16',
            'category_id' => 'required|integer|exists:categories,id',

        ]);

        // Insert data into the database
        DB::table('user_question_views')->insert([
            'user_id' => $request->user->id,
            'question_id' => $validated['question_id'],
            'numper' => $validated['numper'],
            'postion' => $validated['postion'],
            'my_game_id' => $validated['my_game_id'],
            'category_id' => $validated['category_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Question view stored successfully'], 201);
    }

    public function main()
    {
        try {
            // Basic Counts
            $totalUsers = User::count();
            $totalCategories = Category::count();
            $totalQuestions = Question::count();
            $totalInquiries = ContactUs::count();
            $totalGames = MyGame::count();
            $totalTransactions = Transaction::count();
            $totalCoupons = Coupon::count();

            // User Statistics
            $activeUsers = User::where('status', true)->count();
            $premiumUsers = User::where('is_free', false)->count();
            $freeUsers = User::where('is_free', true)->count();
            $maleUsers = User::where('gander', 'male')->count();
            $femaleUsers = User::where('gander', 'female')->count();
            $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();
            $usersWithGames = User::has('games')->count();

            // Financial Statistics
            $totalRevenue = Transaction::where('status', 'success')->sum('amount');
            $monthlyRevenue = Transaction::where('status', 'success')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount');
            $successfulTransactions = Transaction::where('status', 'success')->count();
            $pendingTransactions = Transaction::where('status', 'pending')->count();
            $failedTransactions = Transaction::where('status', 'failed')->count();
            $averageTransactionAmount = Transaction::where('status', 'success')->avg('amount') ?? 0;

            // Game Statistics
            $completedGames = MyGame::where('num_of_play', '>', 1)->count();
            $freeGames = MyGame::where('is_free', true)->count();
            $paidGames = MyGame::where('is_free', false)->count();
            $averageGameDuration = MyGame::avg('num_of_play') ?? 0;
            $totalGamePlays = MyGame::sum('num_of_play');

            // Most active players
            $topPlayers = User::withCount('games')
                ->orderBy('games_count', 'desc')
                ->limit(5)
                ->get();

            // Question Statistics
            $activeQuestions = Question::where('is_active', true)->count();
            $freeQuestions = Question::where('is_free', true)->count();
            $premiumQuestions = Question::where('is_free', false)->count();
            $totalQuestionViews = Question::sum('views');
            $averageQuestionViews = Question::avg('views') ?? 0;

            // Questions by type
            $yamaatQuestions = Question::where('type', 'yamaat')->count();
            $horrorQuestions = Question::where('type', 'horror')->count();
            $vertebraeQuestions = Question::where('type', 'vertebrae')->count();
            $luckQuestions = Question::where('type', 'luck')->count();

            // Most viewed questions
            $topQuestions = Question::orderBy('views', 'desc')->limit(5)->get();

            // Category Statistics
            $activeCategories = Category::where('is_active', true)->count();
            $premiumCategories = Category::where('type', 'premium')->count();
            $normalCategories = Category::where('type', 'normal')->count();
            $totalCategoryViews = Category::sum('views');

            // Most popular categories
            $topCategories = Category::withCount('questions')
                ->orderBy('views', 'desc')
                ->limit(5)
                ->get();

            // Coupon Statistics
            $activeCoupons = Coupon::where('active', true)->count();
            $usedCoupons = CouponUsage::distinct('coupon_id')->count();
            $totalCouponUsages = CouponUsage::count();
            $discountCoupons = Coupon::where('type', 'discount')->count();
            $freeGamesCoupons = Coupon::where('type', 'free_games')->count();

            // Monthly Data for Charts (Last 12 months)
            $monthlyData = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthName = $date->format('M Y');

                $monthlyData[] = [
                    'month' => $monthName,
                    'users' => User::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count(),
                    'games' => MyGame::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count(),
                    'revenue' => Transaction::where('status', 'success')
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->sum('amount'),
                    'questions' => Question::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count(),
                ];
            }

            // Daily statistics for current month
            $dailyStats = [];
            $daysInMonth = Carbon::now()->daysInMonth;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::now()->startOfMonth()->addDays($day - 1);
                $dailyStats[] = [
                    'day' => $day,
                    'users' => User::whereDate('created_at', $date)->count(),
                    'games' => MyGame::whereDate('created_at', $date)->count(),
                    'revenue' => Transaction::where('status', 'success')
                        ->whereDate('created_at', $date)
                        ->sum('amount'),
                ];
            }

            // Growth Rates
            $lastMonthUsers = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->count();
            $userGrowthRate = $lastMonthUsers > 0 ? (($newUsersThisMonth - $lastMonthUsers) / $lastMonthUsers) * 100 : 0;

            $lastMonthRevenue = Transaction::where('status', 'success')
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->sum('amount');
            $revenueGrowthRate = $lastMonthRevenue > 0 ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

            // System Performance Metrics
            $averageQuestionsPerCategory = $totalCategories > 0 ? round($totalQuestions / $totalCategories, 2) : 0;
            $averageGamesPerUser = $totalUsers > 0 ? round($totalGames / $totalUsers, 2) : 0;
            $conversionRate = $totalUsers > 0 ? round(($premiumUsers / $totalUsers) * 100, 2) : 0;
            $gameCompletionRate = $totalGames > 0 ? round(($completedGames / $totalGames) * 100, 2) : 0;

            return response()->json([
                'status' => 'success',
                'data' => [
                    // Basic counts
                    'totalUsers' => $totalUsers,
                    'totalCategories' => $totalCategories,
                    'totalQuestions' => $totalQuestions,
                    'totalInquiries' => $totalInquiries,
                    'totalGames' => $totalGames,
                    'totalTransactions' => $totalTransactions,
                    'totalCoupons' => $totalCoupons,

                    // User statistics
                    'activeUsers' => $activeUsers,
                    'premiumUsers' => $premiumUsers,
                    'freeUsers' => $freeUsers,
                    'maleUsers' => $maleUsers,
                    'femaleUsers' => $femaleUsers,
                    'newUsersThisMonth' => $newUsersThisMonth,
                    'usersWithGames' => $usersWithGames,
                    'topPlayers' => $topPlayers,

                    // Financial statistics
                    'totalRevenue' => $totalRevenue,
                    'monthlyRevenue' => $monthlyRevenue,
                    'successfulTransactions' => $successfulTransactions,
                    'pendingTransactions' => $pendingTransactions,
                    'failedTransactions' => $failedTransactions,
                    'averageTransactionAmount' => $averageTransactionAmount,

                    // Game statistics
                    'completedGames' => $completedGames,
                    'freeGames' => $freeGames,
                    'paidGames' => $paidGames,
                    'averageGameDuration' => $averageGameDuration,
                    'totalGamePlays' => $totalGamePlays,

                    // Question statistics
                    'activeQuestions' => $activeQuestions,
                    'freeQuestions' => $freeQuestions,
                    'premiumQuestions' => $premiumQuestions,
                    'totalQuestionViews' => $totalQuestionViews,
                    'averageQuestionViews' => $averageQuestionViews,
                    'yamaatQuestions' => $yamaatQuestions,
                    'horrorQuestions' => $horrorQuestions,
                    'vertebraeQuestions' => $vertebraeQuestions,
                    'luckQuestions' => $luckQuestions,
                    'topQuestions' => $topQuestions,

                    // Category statistics
                    'activeCategories' => $activeCategories,
                    'premiumCategories' => $premiumCategories,
                    'normalCategories' => $normalCategories,
                    'totalCategoryViews' => $totalCategoryViews,
                    'topCategories' => $topCategories,

                    // Coupon statistics
                    'activeCoupons' => $activeCoupons,
                    'usedCoupons' => $usedCoupons,
                    'totalCouponUsages' => $totalCouponUsages,
                    'discountCoupons' => $discountCoupons,
                    'freeGamesCoupons' => $freeGamesCoupons,

                    // Chart data
                    'monthlyData' => $monthlyData,
                    'dailyStats' => $dailyStats,

                    // Growth rates
                    'userGrowthRate' => $userGrowthRate,
                    'revenueGrowthRate' => $revenueGrowthRate,

                    // Performance metrics
                    'averageQuestionsPerCategory' => $averageQuestionsPerCategory,
                    'averageGamesPerUser' => $averageGamesPerUser,
                    'conversionRate' => $conversionRate,
                    'gameCompletionRate' => $gameCompletionRate,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }

    }


    public function categories(Request $request)
    {
        try {
            $user = $request->user();
         
            // جلب كل الـ Permissions اللي guard_name = 'categories'
            $categoryPermissions = $user->getAllPermissions()
                ->where('guard_name', 'web')
                ->where('group', 'categories')
                ->pluck('name') // ناخد أسماء الفئات فقط
                ->toArray();


            // جلب الفئات اللي عنده صلاحية عليها فقط
            $categories = Category::where('is_active', 1)
                ->whereIn('title', $categoryPermissions)
                ->paginate(10);


            return response()->json([
                'status' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ غير متوقع'
            ], 500);
        }
    }
    public function storeQuestion(Request $request)
    {
        try {
            $questions = $request->input('questions');

            if (!is_array($questions) || count($questions) === 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يوجد أسئلة للإضافة'
                ], 400);
            }

            $savedQuestions = [];

            foreach ($questions as $index => $q) {
                // Validation لكل سؤال
                $validator = Validator::make($q, [
                    'points' => 'required|integer|min:0|in:200,400,600',
                    'question' => 'required|string',
                    'answer' => 'required|string',
                    'link_type' => 'sometimes|required|in:video,image,voice,text',
                    'link_answer_type' => 'sometimes|required|in:video,image,voice,text',
                    'category_id' => 'required|exists:categories,id',
                    'is_active' => 'sometimes|boolean',
                    'is_free' => 'sometimes|boolean',
                    'notes' => 'sometimes|string|min:3|max:17',
                    'direction' => 'sometimes|string|min:3|max:17',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => "خطأ في السؤال رقم " . ($index + 1),
                        'errors' => $validator->errors()
                    ], 422);
                }

                // ملفات الأسئلة والإجابات (لو موجودة)
                $questionFile = $request->file("questions.$index.link_question");
                $answerFile = $request->file("questions.$index.link_answer");
                $questionFilePath = $questionFile ? $this->saveFile($questionFile, 'questions') : null;
                $answerFilePath = $answerFile ? $this->saveFile($answerFile, 'answers') : null;
                // إنشاء السؤال
                $savedQuestions[] = Question::create([
                    'points' => $q['points'],
                    'question' => $q['question'],
                    'answer' => $q['answer'],
                    'link_question' => $questionFilePath ? 'questions/' . $questionFilePath : null,
                    'link_answer' => $answerFilePath ? 'answers/' . $answerFilePath : null,
                    'link_type' => $q['link_type'] ?? 'text',
                    'link_answer_type' => $q['link_answer_type'],
                    'category_id' => $q['category_id'],
                    'is_active' => $q['is_active'] ?? 0,
                    'is_free' => $q['is_free'] ?? 0,
                    'notes' => $q['notes'] ?? null,
                    'direction' => $q['direction'] ?? null,
                ]);
            }

            return response()->json([
                'status' => true,
                'msg' => 'تمت إضافة الأسئلة بنجاح',
                'data' => $savedQuestions
            ], 200);

        } catch (\Exception $e) {
            // سجل الخطأ في اللوج
            \Log::error('StoreQuestion Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء حفظ الأسئلة'
            ], 500);
        }
    }

    public function qrCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question_id' => 'required|exists:questions,id',
            ], [
                'question_id.required' => 'يرجى اختيار السؤال',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                    'msg' => 'فشلت عملية الإضافة'
                ], 422);
            }

            $question = Question::findOrFail($request->question_id);

            // Encrypt the ID securely
            $encryptedId = encrypt($question->id);

            // Generate secure URL to the Blade view
            $secureLink = url('/question/preview/' . $encryptedId);

            // Generate QR Code
            $qrCode = (new \BaconQrCode\Writer(
                new ImageRenderer(
                    new RendererStyle(300),
                    new SvgImageBackEnd()
                )
            ))->writeString($secureLink);

            // Save QR code temporarily in storage
            $filename = 'qr_' . time() . '.svg';
            $path = public_path('public/tmp/' . $filename);

            // تأكد إن المجلد موجود
            if (!file_exists(public_path('public/tmp'))) {
                mkdir(public_path('public/tmp'), 0755, true);
            }

            file_put_contents($path, $qrCode);

            // Generate public URL
            $publicUrl = asset('public/tmp/' . $filename);

            return response()->json([
                'status' => true,
                'msg' => 'تم توليد الكود بنجاح',
                'data' => [
                    'qr_code_url' => $publicUrl,
                    'link' => $secureLink
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }



}