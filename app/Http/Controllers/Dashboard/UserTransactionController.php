<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserTransactionController extends Controller
{
    public function index(Request $request)
    {
        // Build query with filters
        $query = Transaction::with('user','couponUsage.coupon');

        // Apply filters
        if ($request->filled('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_name . '%');
            });
        }

        if ($request->filled('user_phone')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->user_phone . '%');
            });
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('amount_from')) {
            $query->where('amount', '>=', $request->amount_from);
        }

        if ($request->filled('amount_to')) {
            $query->where('amount', '<=', $request->amount_to);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('games_count')) {
            $query->where('num_of_games_he_pay', $request->games_count);
        }

        if ($request->filled('repeat_customers')) {
            if ($request->repeat_customers == '1') {
                // Only repeat customers
                $repeatCustomerIds = Transaction::select('user_id')
                    ->groupBy('user_id')
                    ->havingRaw('COUNT(*) > 1')
                    ->pluck('user_id');
                $query->whereIn('user_id', $repeatCustomerIds);
            } elseif ($request->repeat_customers == '0') {
                // Only first-time customers
                $firstTimeCustomerIds = Transaction::select('user_id')
                    ->groupBy('user_id')
                    ->havingRaw('COUNT(*) = 1')
                    ->pluck('user_id');
                $query->whereIn('user_id', $firstTimeCustomerIds);
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate
        $transactions = $query->paginate(20);

        // Calculate comprehensive statistics
        $stats = $this->calculateStatistics();
        
        // Get repeat customer analysis
        $repeatCustomerStats = $this->getRepeatCustomerAnalysis();
        
        // Get filter options
        $paymentTypes = Transaction::select('payment_type')->distinct()->whereNotNull('payment_type')->pluck('payment_type');
        $gamesCounts = Transaction::select('num_of_games_he_pay')->distinct()->orderBy('num_of_games_he_pay')->pluck('num_of_games_he_pay');

        // Payment method statistics
        $payment_methods = Transaction::select('payment_type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total_amount'))
            ->where('status', 'success')
            ->groupBy('payment_type')
            ->get();
            
        // Monthly revenue chart data
        $monthly_revenue = Transaction::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(amount) as total')
        )
        ->where('status', 'success')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();
        
        // Recent transactions for quick view
        $recent_transactions = Transaction::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Top customers by spending
        $topCustomers = $this->getTopCustomers();

        // Daily revenue trend (last 30 days)
        $dailyRevenue = $this->getDailyRevenueTrend();

        // Customer lifetime value analysis
        $customerLifetimeValue = $this->getCustomerLifetimeValue();
        return view('dashboard.transaction.index', compact(
            'transactions', 
            'stats', 
            'repeatCustomerStats',
            'payment_methods', 
            'monthly_revenue',
            'recent_transactions',
            'paymentTypes',
            'gamesCounts',
            'topCustomers',
            'dailyRevenue',
            'customerLifetimeValue'
        ));
    }

    private function calculateStatistics()
    {
        $totalTransactions = Transaction::count();
        $successfulTransactions = Transaction::where('status', 'success')->count();
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $failedTransactions = Transaction::where('status', 'failed')->count();
        
        $totalRevenue = Transaction::where('status', 'success')->sum('amount');
        $todayRevenue = Transaction::where('status', 'success')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
        $thisMonthRevenue = Transaction::where('status', 'success')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');
        $lastMonthRevenue = Transaction::where('status', 'success')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('amount');
        
        $avgTransactionAmount = Transaction::where('status', 'success')->avg('amount');
        
        // Growth calculations
        $monthlyGrowth = $lastMonthRevenue > 0 
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        // Success rate
        $successRate = $totalTransactions > 0 
            ? ($successfulTransactions / $totalTransactions) * 100 
            : 0;

        // Average games purchased per transaction
        $avgGamesPerTransaction = Transaction::where('status', 'success')->avg('num_of_games_he_pay');

        // Total games sold
        $totalGamesSold = Transaction::where('status', 'success')->sum('num_of_games_he_pay');

        return [
            'total_transactions' => $totalTransactions,
            'successful_transactions' => $successfulTransactions,
            'pending_transactions' => $pendingTransactions,
            'failed_transactions' => $failedTransactions,
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'this_month_revenue' => $thisMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'monthly_growth' => $monthlyGrowth,
            'avg_transaction_amount' => $avgTransactionAmount,
            'success_rate' => $successRate,
            'avg_games_per_transaction' => $avgGamesPerTransaction,
            'total_games_sold' => $totalGamesSold,
        ];
    }

    private function getRepeatCustomerAnalysis()
    {
        // Get customer transaction counts
        $customerTransactionCounts = Transaction::select('user_id', DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('user_id')
            ->get();

        $totalCustomers = $customerTransactionCounts->count();
        $firstTimeCustomers = $customerTransactionCounts->where('transaction_count', 1)->count();
        $repeatCustomers = $customerTransactionCounts->where('transaction_count', '>', 1)->count();
        
        $repeatCustomerRate = $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0;

        // Most frequent customers
        $mostFrequentCustomers = Transaction::select(
                'user_id',
                DB::raw('COUNT(*) as purchase_count'),
                DB::raw('SUM(amount) as total_spent'),
                DB::raw('AVG(amount) as avg_spent'),
                DB::raw('MAX(created_at) as last_purchase')
            )
            ->where('status', 'success')
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('purchase_count', 'desc')
            ->limit(10)
            ->get();

        // Purchase frequency distribution
        $frequencyDistribution = Transaction::select(DB::raw('COUNT(*) as purchase_count'), DB::raw('COUNT(DISTINCT user_id) as customer_count'))
            ->groupBy('user_id')
            ->get()
            ->groupBy('purchase_count')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortKeys();

        // Customer retention analysis (customers who made purchases in different months)
        $customerRetention = DB::select("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(DISTINCT user_id) as unique_customers,
                COUNT(*) as total_transactions
            FROM transactions 
            WHERE status = 'success'
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month DESC
            LIMIT 12
        ");

        return [
            'total_customers' => $totalCustomers,
            'first_time_customers' => $firstTimeCustomers,
            'repeat_customers' => $repeatCustomers,
            'repeat_customer_rate' => $repeatCustomerRate,
            'most_frequent_customers' => $mostFrequentCustomers,
            'frequency_distribution' => $frequencyDistribution,
            'customer_retention' => collect($customerRetention),
        ];
    }

    private function getTopCustomers()
    {
        return Transaction::select(
                'user_id',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_spent'),
                DB::raw('AVG(amount) as avg_spent'),
                DB::raw('SUM(num_of_games_he_pay) as total_games'),
                DB::raw('MAX(created_at) as last_purchase'),
                DB::raw('MIN(created_at) as first_purchase')
            )
            ->where('status', 'success')
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();
    }

    private function getDailyRevenueTrend()
    {
        return Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->where('status', 'success')
            ->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    private function getCustomerLifetimeValue()
    {
        $customers = Transaction::select(
                'user_id',
                DB::raw('SUM(amount) as lifetime_value'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('DATEDIFF(MAX(created_at), MIN(created_at)) as customer_lifespan_days'),
                DB::raw('MIN(created_at) as first_purchase'),
                DB::raw('MAX(created_at) as last_purchase')
            )
            ->where('status', 'success')
            ->with('user')
            ->groupBy('user_id')
            ->get();

        $avgLifetimeValue = $customers->avg('lifetime_value');
        $avgCustomerLifespan = $customers->avg('customer_lifespan_days');
        $avgTransactionsPerCustomer = $customers->avg('total_transactions');

        return [
            'avg_lifetime_value' => $avgLifetimeValue,
            'avg_customer_lifespan' => $avgCustomerLifespan,
            'avg_transactions_per_customer' => $avgTransactionsPerCustomer,
            'top_value_customers' => $customers->sortByDesc('lifetime_value')->take(5),
        ];
    }

    public function destroy(Request $request)
    {
        $transaction = Transaction::findOrFail($request->id);
        $transaction->delete();
        
        session()->flash('delete', 'تم حذف المعاملة بنجاح');
        return redirect()->route('transaction.index')->with('success', 'تم حذف المعاملة بنجاح');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'لم يتم تحديد أي عناصر للحذف']);
        }

        Transaction::whereIn('id', $ids)->delete();
        
        return response()->json(['success' => true, 'message' => 'تم حذف المعاملات المحددة بنجاح']);
    }

    public function exportData(Request $request)
    {
        // This method can be implemented to export transaction data
        // For now, we'll return a JSON response
        $transactions = Transaction::with('user')->get();
        
        return response()->json([
            'success' => true,
            'data' => $transactions,
            'message' => 'تم تصدير البيانات بنجاح'
        ]);
    }
}
