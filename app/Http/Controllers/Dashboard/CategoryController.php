<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ImageProcessing;
use App\Enums\CategoryFilterMode;
use App\Imports\CategoriesImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ImageProcessing;
    public function index(Request $request)
    {
        $categories = Category::query()->with('questions');

        // Apply filters from the request
        // if ($request->has('title') && $request->title != '') {
        //     $categories->where('title', 'like', '%' . $request->title . '%');
        // }
        // if ($request->has('type') && $request->type != '') {
        //     $categories->where('type', $request->type);
        // }
        // if ($request->has('is_active') && $request->is_active != '') {
        //     $categories->where('is_active', $request->is_active);
        // }
        // if ($request->has('views') && $request->views != '') {
        //     $categories->orderBy('views', $request->views == 'most_viewed' ? 'desc' : 'asc');
        // }
        // if ($request->has('questions_count') && in_array($request->questions_count, [200, 400, 600])) {
        //     $categories->whereHas('questions', function ($query) use ($request) {
        //         $query->where('points', $request->questions_count);
        //     });
        // }
        // if ($request->has('start_date') && $request->start_date != '') {
        //     $categories->whereDate('created_at', '>=', $request->start_date);
        // }
        // if ($request->has('end_date') && $request->end_date != '') {
        //     $categories->whereDate('created_at', '<=', $request->end_date);
        // }
        // if ($request->has('end_at') && $request->end_at != '') {
        //     $categories->whereDate('end_at', '<=', $request->end_at);
        // }
        $categories->when($request->filled('title'), function ($query) use ($request) {
            $query->where('title', 'like', '%' . $request->title . '%');
        });

        $categories->when($request->filled('type'), function ($query) use ($request) {
            $query->where('type', $request->type);
        });

        $categories->when($request->filled('is_active'), function ($query) use ($request) {
            $query->where('is_active', $request->is_active);
        });

        $categories->when($request->filled('views'), function ($query) use ($request) {
            $query->orderBy('views', $request->views === 'most_viewed' ? 'desc' : 'asc');
        });

        $categories->when(
            $request->filled('questions_count') && in_array($request->questions_count, [200, 400, 600]),
            function ($query) use ($request) {
                $query->whereHas('questions', function ($q) use ($request) {
                    $q->where('points', $request->questions_count);
                });
            }
        );

        $categories->when($request->filled('start_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '>=', $request->start_date);
        });

        $categories->when($request->filled('end_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '<=', $request->end_date);
        });

        $categories->when($request->filled('end_at'), function ($query) use ($request) {
            $query->whereDate('end_at', '<=', $request->end_at);
        });

        $categories = $categories->orderBy('created_at', 'desc')->paginate(1000);

        // Get category IDs for more efficient queries
        $categoryIds = $categories->pluck('id')->toArray();

        // Pre-calculate question counts for all categories at once
        $questionCounts = [];
        foreach ([200, 400, 600] as $points) {
            $counts = DB::table('questions')
                ->select('category_id', DB::raw('count(*) as count'))
                ->whereIn('category_id', $categoryIds)
                ->where('points', $points)
                ->groupBy('category_id')
                ->pluck('count', 'category_id')
                ->toArray();
            $questionCounts[$points] = $counts;
        }

        // Get view statistics for all users and categories in one optimized query
        // First, let's check if there are any records that might be causing issues
        $problemRecords = DB::table('temp_user_question_views')
            ->select(
                'temp_user_question_views.user_id',
                'temp_user_question_views.category_id',
                'temp_user_question_views.question_id',
                'questions.id as q_id',
                'questions.points'
            )
            ->leftJoin('questions', 'temp_user_question_views.question_id', '=', 'questions.id')
            ->where('temp_user_question_views.user_id', 31)
            ->where('temp_user_question_views.category_id', 39)
            ->whereIn('questions.points', [200, 400, 600])
            ->orWhereNull('questions.id')
            ->get();

        Log::info('Potential problem records:', $problemRecords->toArray());

        // Direct check for user 31, category 39, with 400 points
        $user31Cat39Count = DB::table('temp_user_question_views')
            ->join('questions', 'temp_user_question_views.question_id', '=', 'questions.id')
            ->where('temp_user_question_views.user_id', 31)
            ->where('temp_user_question_views.category_id', 39)
            ->where('questions.points', 400)
            ->count(DB::raw('DISTINCT temp_user_question_views.question_id'));

        Log::info('Direct count for user 31, category 39, 400 points:', ['count' => $user31Cat39Count]);

        $userViewStats = DB::table('temp_user_question_views')
            ->select(
                'temp_user_question_views.user_id',
                'temp_user_question_views.category_id',
                DB::raw('(SELECT COUNT(DISTINCT uqv1.question_id) 
                          FROM temp_user_question_views uqv1 
                          JOIN questions q1 ON uqv1.question_id = q1.id 
                          WHERE uqv1.user_id = temp_user_question_views.user_id 
                          AND uqv1.category_id = temp_user_question_views.category_id 
                          AND q1.points = 200) as viewed_200'),
                DB::raw('(SELECT COUNT(DISTINCT uqv2.question_id) 
                          FROM temp_user_question_views uqv2 
                          JOIN questions q2 ON uqv2.question_id = q2.id 
                          WHERE uqv2.user_id = temp_user_question_views.user_id 
                          AND uqv2.category_id = temp_user_question_views.category_id 
                          AND q2.points = 400) as viewed_400'),
                DB::raw('(SELECT COUNT(DISTINCT uqv3.question_id) 
                          FROM temp_user_question_views uqv3 
                          JOIN questions q3 ON uqv3.question_id = q3.id 
                          WHERE uqv3.user_id = temp_user_question_views.user_id 
                          AND uqv3.category_id = temp_user_question_views.category_id 
                          AND q3.points = 600) as viewed_600')
            )
            ->whereIn('temp_user_question_views.category_id', $categoryIds)
            ->groupBy('temp_user_question_views.user_id', 'temp_user_question_views.category_id')
            ->get();

        // Debug the query results
        Log::info('User view stats query results:', $userViewStats->toArray());

        // Organize view stats by category and user
        $viewsByCategory = [];
        foreach ($userViewStats as $stat) {
            $viewsByCategory[$stat->category_id][$stat->user_id] = [
                200 => $stat->viewed_200,
                400 => $stat->viewed_400,
                600 => $stat->viewed_600
            ];
        }

        // Get all user names for reference
        $userNames = \App\Models\User::pluck('name', 'id')->toArray();

        // Add the warning logic for each category
        foreach ($categories as $category) {
            Log::info("Processing category", ['category_id' => $category->id, 'category_name' => $category->name]);

            // Get question counts for this category
            $questions200 = $questionCounts[200][$category->id] ?? 0;
            $questions400 = $questionCounts[400][$category->id] ?? 0;
            $questions600 = $questionCounts[600][$category->id] ?? 0;

            Log::info("Question counts", [
                'category_id' => $category->id,
                '200' => $questions200,
                '400' => $questions400,
                '600' => $questions600
            ]);

            // Calculate the number of possible games
            $totalPossibleGames = min($questions200, $questions400, $questions600);

            // Initialize warning for the category
            $category->warning = 'green'; // Default warning

            // Find the user who has viewed the most games
            $maxGamesViewed = 0;
            $maxViewingUserId = null;

            // Check view stats for this category
            if (isset($viewsByCategory[$category->id])) {
                foreach ($viewsByCategory[$category->id] as $userId => $pointsData) {
                    // Calculate games viewed by this user
                    $viewedQuestions200 = (int) ($pointsData[200] ?? 0);
                    $viewedQuestions400 = (int) ($pointsData[400] ?? 0);
                    $viewedQuestions600 = (int) ($pointsData[600] ?? 0);

                    Log::info("Raw point data", [
                        'category_id' => $category->id,
                        'user_id' => $userId,
                        'point_data' => $pointsData
                    ]);

                    $gamesViewed = max($viewedQuestions200, $viewedQuestions400, $viewedQuestions600);

                    Log::info("User view stats", [
                        'category_id' => $category->id,
                        'user_id' => $userId,
                        'viewed_200' => $viewedQuestions200,
                        'viewed_400' => $viewedQuestions400,
                        'viewed_600' => $viewedQuestions600,
                        'games_viewed' => $gamesViewed
                    ]);

                    // Update max if this user has viewed more games
                    if ($gamesViewed > $maxGamesViewed) {
                        $maxGamesViewed = $gamesViewed;
                        $maxViewingUserId = $userId;
                    }
                    Log::info("Max games viewed", [
                        'category_id' => $category->id,
                        'max_games_viewed' => $maxGamesViewed,
                        'max_viewing_user_id' => $maxViewingUserId
                    ]);
                }
            }
            $totalPossibleGames = floor($totalPossibleGames / 2);
            $maxGamesViewed = ceil($maxGamesViewed / 2);
            // Set warning based on the max games viewed by any user
            if ($totalPossibleGames > 0) {
                $percentageViewed = ($maxGamesViewed / $totalPossibleGames) * 100;

                Log::info("Category viewing summary", [
                    'category_id' => $category->id,
                    'total_possible_games' => $totalPossibleGames,
                    'max_games_viewed' => $maxGamesViewed,
                    'percentage_viewed' => $percentageViewed,
                ]);

                if ($maxGamesViewed >= $totalPossibleGames) {
                    $category->warning = 'red';
                } elseif ($percentageViewed >= 80) {
                    $category->warning = 'yellow';
                }
            }

            // Add game stats to the category object for display in the view if needed
            $category->totalPossibleGames = $totalPossibleGames;
            $category->maxGamesViewed = $maxGamesViewed;

            if ($maxViewingUserId && isset($userNames[$maxViewingUserId])) {
                $category->maxViewingUserName = $userNames[$maxViewingUserId];
            }

            Log::info("Final category stats", [
                'category_id' => $category->id,
                'warning' => $category->warning,
                'total_games' => $category->totalPossibleGames,
                'max_viewer' => $category->maxViewingUserName ?? 'N/A',
            ]);
        }

        return view('dashboard.categories.index', compact('categories'));
    }

    public function show(Request $request, $categoryId)
    {
        // Fetch the category
        $category = Category::findOrFail($categoryId);

        // Get users who have viewed questions for this category
        $users = User::whereHas('viewedQuestions', function ($query) use ($category) {
            $query->where('temp_user_question_views.category_id', $category->id);
        })
            ->withCount([
                'viewedQuestions as questions_200' => function ($query) use ($category) {
                    $query->where('temp_user_question_views.category_id', $category->id)  // Specify table alias
                        ->where('questions.points', 200);  // Specify table alias
                },
                'viewedQuestions as questions_400' => function ($query) use ($category) {
                    $query->where('temp_user_question_views.category_id', $category->id)  // Specify table alias
                        ->where('questions.points', 400);  // Specify table alias
                },
                'viewedQuestions as questions_600' => function ($query) use ($category) {
                    $query->where('temp_user_question_views.category_id', $category->id)  // Specify table alias
                        ->where('questions.points', 600);  // Specify table alias
                }
            ])
            ->get();

        // Return the category view with users' question stats
        return view('dashboard.categories.show', compact('category', 'users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'required|in:normal,premium',
            'end_at' => 'nullable|date',
            'is_active' => 'nullable|in:0,1',
            'is_almost' => 'nullable|in:0,1'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('_token');

        // Handle boolean fields properly - checkboxes send "1" when checked, nothing when unchecked
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['is_almost'] = $request->has('is_almost') ? 1 : 0;
        $data['no_words'] = $request->has('mode') ? $request->mode : 0;
        unset($data['mode']);
        $lastPosition = Category::max('position');
        $data['position'] = $lastPosition ? $lastPosition + 1 : 1;
      if ($request->hasFile('image')) {
            $data['image'] = 'imagesfp/category/' . $this->saveImage($request->file('image'), 'category');
        }

        Category::create($data);
        session()->flash('Add', 'تم الاضافة بنجاح');
        return redirect()->back();
    }


  public function update(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'title' => 'required|max:255',
          'description' => 'nullable|string',
          'image' => 'nullable',
          'type' => 'required|in:normal,premium',
          'end_at' => 'nullable|date',
          'is_active' => 'nullable|in:0,1',
          'is_almost' => 'nullable|in:0,1',
          'is_draft' => 'nullable|in:0,1',
          'timer_200' => 'nullable|numeric',
          'timer_400' => 'nullable|numeric',
          'timer_600' => 'nullable|numeric',
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
      }

      try {
          $category = Category::findOrFail($request->id);
          $data = $request->except(['_token', '_method']);

          // checkboxes
          $data['is_active'] = $request->has('is_active') ? 1 : 0;
          $data['is_almost'] = $request->has('is_almost') ? 1 : 0;
          $data['is_draft'] = $request->has('is_draft') ? 1 : 0;

          // image
          if ($request->hasFile('image')) {
              $data['image'] = 'imagesfp/category/' . $this->saveImage($request->file('image'), 'category');
          }
          
          $category->update($data);

          session()->flash('edit', 'تم التعديل بنجاح');
          return redirect()->back();

      } catch (\Exception $e) {
          // سجل الخطأ في اللوج
          \Log::error('Category Update Error: '.$e->getMessage());
          // اختياري: ممكن تعرض رسالة للمستخدم
          return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث، سيتم مراجعة المشكلة.');
      }
  }


    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'excel_file.required' => 'يرجى اختيار ملف',
            'excel_file.mimes' => 'يجب أن يكون الملف من نوع: xlsx, xls, csv',
            'excel_file.max' => 'حجم الملف لا يجب أن يتجاوز 2 ميجابايت'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'فشل استيراد الملف');
        }

        try {
            Excel::import(new CategoriesImport, $request->file('excel_file'));

            session()->flash('Add', 'تم استيراد البيانات بنجاح');
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('delete', "حدث خطأ أثناء استيراد الملف: " . $e->getMessage());

            return redirect()->back()->with('error', 'حدث خطأ أثناء استيراد الملف: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $category = Category::findOrFail($request->id);

        // Delete image if exists
        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $category->delete();
        session()->flash('delete', 'تم الحذف بنجاح');
        return redirect()->back();
    }



    // ?todo in one query reorder categories
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:categories,id',
            'order.*.position' => 'required|integer|min:1',
        ]);

        $order = $data['order'];

        // جمع الـ IDs
        $ids = array_column($order, 'id');

        // بناء CASE WHEN
        $caseSql = "CASE id ";
        foreach ($order as $item) {
            $id = (int) $item['id'];
            $pos = (int) $item['position'];
            $caseSql .= "WHEN {$id} THEN {$pos} ";
        }
        $caseSql .= "END";

        // ?todo in one query
        DB::update(
            "
            UPDATE categories
            SET position = {$caseSql}
            WHERE id IN (" . implode(',', $ids) . ")"
        );

        return response()->json(['message' => 'تم تحديث اماكن الفئات بنجاح']);
    }

    // ?todo return view to sort categories
    public function showSort()
    {
        $categoriesnormal = Category::where('type', 'normal')->orderBy('position')->get();
        $categoriespremium = Category::where('type', 'premium')->orderBy('position')->get();
        return view('dashboard.categories.categories-sort', compact('categoriesnormal', 'categoriespremium'));
    }

    public function noWordCategory(Request $request, $mode)
    {
        try {
            $modeEnum = CategoryFilterMode::from($mode);
        } catch (\ValueError $e) {
            abort(404);
        }
        $categories = Category::query();
        $categories = $this->filter($categories, $request, $modeEnum);
        return view('dashboard.categories.no-word', compact('categories', 'mode'));
    }

    private function filter($categories, Request $request, CategoryFilterMode $mode)
    {
        $categories->when($request->filled('title'), fn($q) => $q->where('title', 'like', '%' . $request->title . '%'));
        $categories->when($request->filled('type'), fn($q) => $q->where('type', $request->type));
        $categories->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->is_active));
        $categories->when($request->filled('views'), fn($q) => $q->orderBy('views', $request->views === 'most_viewed' ? 'desc' : 'asc'));
        $categories->when(
            $request->filled('questions_count') && in_array($request->questions_count, [200, 400, 600]),
            fn($q) =>
            $q->whereHas('questions', fn($q2) => $q2->where('points', $request->questions_count))
        );
        $categories->when($request->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->start_date));
        $categories->when($request->filled('end_date'), fn($q) => $q->whereDate('created_at', '<=', $request->end_date));
        $categories->when($request->filled('end_at'), fn($q) => $q->whereDate('end_at', '<=', $request->end_at));

        // فلترة حسب الـ Enum

        $categories->where('no_words', $mode->value);

        return $categories->orderBy('created_at', 'desc')->paginate(20);
    }
}