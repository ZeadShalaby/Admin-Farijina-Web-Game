<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\User;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContactUsController extends Controller
{
    public function index(Request $request)
    {
        // Build query with relationships
        $query = ContactUs::with(['question.category', 'user']);

        // Apply filters
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        if ($request->filled('question_type')) {
            $query->whereHas('question', function ($q) use ($request) {
                $q->where('type', $request->question_type);
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('question', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('question_points')) {
            $query->whereHas('question', function ($q) use ($request) {
                $q->where('points', $request->question_points);
            });
        }

        if ($request->filled('has_user')) {
            if ($request->has_user == '1') {
                $query->whereNotNull('user_id');
            } else {
                $query->whereNull('user_id');
            }
        }

        if ($request->filled('has_question')) {
            if ($request->has_question == '1') {
                $query->whereNotNull('question_id');
            } else {
                $query->whereNull('question_id');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate
        $complaints = $query->paginate(20)->withQueryString();

        // Calculate statistics
        $statistics = $this->calculateStatistics();

        // Get filter options
        $categories = Category::select('id', 'title')->get();
        $questionTypes = Question::select('type')->distinct()->pluck('type');
        $questionPoints = Question::select('points')->distinct()->orderBy('points')->pluck('points');

        return view('dashboard.contact-us.index', compact(
            'complaints',
            'statistics',
            'categories',
            'questionTypes',
            'questionPoints'
        ));
    }

    private function calculateStatistics()
    {
        $totalComplaints = ContactUs::count();
        $complaintsThisMonth = ContactUs::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $complaintsToday = ContactUs::whereDate('created_at', Carbon::today())->count();
        
        $complaintsWithUsers = ContactUs::whereNotNull('user_id')->count();
        $complaintsWithQuestions = ContactUs::whereNotNull('question_id')->count();
        
        // Most common subjects
        $commonSubjects = ContactUs::select('subject', DB::raw('count(*) as count'))
            ->groupBy('subject')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Complaints by question type
        $complaintsByQuestionType = ContactUs::join('questions', 'contact_us.question_id', '=', 'questions.id')
            ->select('questions.type', DB::raw('count(*) as count'))
            ->groupBy('questions.type')
            ->get();

        // Daily complaints for the last 30 days
        $dailyComplaints = ContactUs::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Most problematic questions (most complained about)
        $problematicQuestions = ContactUs::join('questions', 'contact_us.question_id', '=', 'questions.id')
            ->select('questions.id', 'questions.question', 'questions.type', DB::raw('count(*) as complaint_count'))
            ->groupBy('questions.id', 'questions.question', 'questions.type')
            ->orderByDesc('complaint_count')
            ->limit(10)
            ->get();

        return [
            'total' => $totalComplaints,
            'this_month' => $complaintsThisMonth,
            'today' => $complaintsToday,
            'with_users' => $complaintsWithUsers,
            'with_questions' => $complaintsWithQuestions,
            'without_users' => $totalComplaints - $complaintsWithUsers,
            'without_questions' => $totalComplaints - $complaintsWithQuestions,
            'common_subjects' => $commonSubjects,
            'by_question_type' => $complaintsByQuestionType,
            'daily_complaints' => $dailyComplaints,
            'problematic_questions' => $problematicQuestions,
        ];
    }

    public function show($id)
    {
        $complaint = ContactUs::with(['question.category', 'user'])->findOrFail($id);
        
        return view('dashboard.contact-us.show', compact('complaint'));
    }

    public function destroy(Request $request)
    {
        $complaint = ContactUs::findOrFail($request->id);
        $complaint->delete();
        
        session()->flash('delete', 'تم حذف الشكوى بنجاح');
        return redirect()->route('contactus')->with('success', 'تم حذف الشكوى بنجاح');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'لم يتم تحديد أي عناصر للحذف']);
        }

        ContactUs::whereIn('id', $ids)->delete();
        
        return response()->json(['success' => true, 'message' => 'تم حذف الشكاوى المحددة بنجاح']);
    }
}