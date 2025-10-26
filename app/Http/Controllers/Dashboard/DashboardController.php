<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactUs;
use App\Models\MyGame;
use App\Models\Question;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\UserQuestionView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function main()
    {
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

        return view('dashboard.home.index', compact(
            // Basic counts
            'totalUsers', 'totalCategories', 'totalQuestions', 'totalInquiries', 
            'totalGames', 'totalTransactions', 'totalCoupons',
            
            // User statistics
            'activeUsers', 'premiumUsers', 'freeUsers', 'maleUsers', 'femaleUsers',
            'newUsersThisMonth', 'usersWithGames', 'topPlayers',
            
            // Financial statistics
            'totalRevenue', 'monthlyRevenue', 'successfulTransactions', 
            'pendingTransactions', 'failedTransactions', 'averageTransactionAmount',
            
            // Game statistics
            'completedGames', 'freeGames', 'paidGames', 'averageGameDuration', 'totalGamePlays',
            
            // Question statistics
            'activeQuestions', 'freeQuestions', 'premiumQuestions', 'totalQuestionViews',
            'averageQuestionViews', 'yamaatQuestions', 'horrorQuestions', 
            'vertebraeQuestions', 'luckQuestions', 'topQuestions',
            
            // Category statistics
            'activeCategories', 'premiumCategories', 'normalCategories', 
            'totalCategoryViews', 'topCategories',
            
            // Coupon statistics
            'activeCoupons', 'usedCoupons', 'totalCouponUsages', 
            'discountCoupons', 'freeGamesCoupons',
            
            // Chart data
            'monthlyData', 'dailyStats',
            
            // Growth rates
            'userGrowthRate', 'revenueGrowthRate',
            
            // Performance metrics
            'averageQuestionsPerCategory', 'averageGamesPerUser', 
            'conversionRate', 'gameCompletionRate'
        ));
    }

    public function deleteStaticsUser()
    {
        $mygame = MyGame::where('user_id', 1)->delete();
        return redirect()->back()->with('success', 'تم حذف الإحصائيات بنجاح');
    }
}
