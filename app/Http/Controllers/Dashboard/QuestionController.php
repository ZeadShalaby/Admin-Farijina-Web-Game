<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\QuestionImport;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\ImageProcessing;

class QuestionController extends Controller
{
    use ImageProcessing;

    public function index(Request $request)
    {
        $categories = Category::where('is_active', 1)->get();

        $query = Question::with('category')->where('type', 'yamaat');

        if ($request->filled('question_id')) {
            $query->where('id', $request->question_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('points', 'like', "%$search%")
                    ->orWhere('question', 'like', "%$search%")
                    ->orWhere('answer', 'like', "%$search%")
                    // ->orWhere('link_question', 'like', "%$search%")
                    // ->orWhere('link_answer', 'like', "%$search%")
                    ->orWhere('link_type', 'like', "%$search%")
                    ->orWhere('link_answer_type', 'like', "%$search%")
                    // ->orWhere('views', 'like', "%$search%")
                    ->orWhere('category_id', 'like', "%$search%")
                    ->orWhere('is_active', 'like', "%$search%")
                    ->orWhere('is_free', 'like', "%$search%");
                // ->orWhere('type', 'like', "%$search%");;
            });
        }

        if ($request->filled('show_all')) {
            // إذا طلب المستخدم عرض الكل، رجّع كل النتائج
            $questions = $query->get();
        } else {
            // غير كذا استخدم paginate
            $questions = $query->paginate(10);
        }

        return view('dashboard.questions.index', compact('questions', 'categories'));
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'points' => 'required|integer|min:0',
            'question' => 'required|string',
            'answer' => 'required|string',
            'link_question' => 'required',
            'link_answer' => 'required',
            'link_type' => 'required|in:video,image,voice,text',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'is_free' => 'boolean'
        ], [
            'points.required' => 'يرجى إدخال النقاط',
            'question.required' => 'يرجى إدخال السؤال',
            'answer.required' => 'يرجى إدخال الإجابة',
            'category_id.required' => 'يرجى اختيار الفئة',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'فشلت عملية الإضافة');
        }

        // Save the uploaded files
        $questionFilePath = $this->saveFile($request->file('link_question'), 'questions');
        $answerFilePath = $this->saveFile($request->file('link_answer'), 'answers');

        // Create the question record
        Question::create([
            'points' => $request->points,
            'question' => $request->question,
            'answer' => $request->answer,
            'link_question' => 'questions/' . $questionFilePath,
            'link_answer' => 'answers/' . $answerFilePath,
            'link_type' => $request->link_type,
            'category_id' => $request->category_id,
            'is_active' => $request->has('is_active'),
            'is_free' => $request->has('is_free'),
        ]);

        session()->flash('Add', 'تم إضافة السؤال بنجاح');
        return redirect()->route('questions.index');
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'points' => 'required|integer|min:0',
                'question' => 'required|string',
                'answer' => 'required|string',
                'link_question' => 'nullable|file',
                'link_answer' => 'nullable|file',
                'link_type' => 'required|in:video,image,voice,text',
                'category_id' => 'required|exists:categories,id',
                'is_active' => 'boolean',
                'is_free' => 'boolean'
            ], [
                'points.required' => 'يرجى إدخال النقاط',
                'points.integer' => 'يجب أن تكون النقاط رقماً صحيحاً',
                'points.min' => 'يجب أن تكون النقاط 0 أو أكثر',
                'question.required' => 'يرجى إدخال السؤال',
                'answer.required' => 'يرجى إدخال الإجابة',
                'link_type.required' => 'يرجى اختيار نوع الرابط',
                'link_type.in' => 'نوع الرابط غير صالح',
                'category_id.required' => 'يرجى اختيار الفئة',
                'category_id.exists' => 'الفئة المختارة غير موجودة',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'فشل التحديث');
            }

            $question = Question::findOrFail($request->id);
            $data = $request->except(['_token', '_method']);

            // Handle File Uploads
            if ($request->hasFile('link_question')) {
                $data['link_question'] = 'questions/' . $this->saveFile($request->file('link_question'), 'questions');
            }

            if ($request->hasFile('link_answer')) {
                $data['link_answer'] = 'answers/' . $this->saveFile($request->file('link_answer'), 'answers');
            }

            // Set boolean values
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            $data['is_free'] = $request->has('is_free') ? 1 : 0;

            $question->update($data);

            session()->flash('edit', 'تم تعديل السؤال بنجاح');
            return redirect()->route('questions.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
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
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
          dd($mode);
            Excel::import(new QuestionImport(request->mode), $request->file('excel_file'));

            return response()->json([
                'status' => 'success',
                'message' => 'تم استيراد الأسئلة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء استيراد الملف: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        // Check if this is a bulk delete operation
        if ($request->has('selected_ids') && is_array($request->selected_ids)) {
            // Bulk delete
            $count = Question::whereIn('id', $request->selected_ids)->count();
            Question::whereIn('id', $request->selected_ids)->delete();
            
            session()->flash('delete', "تم حذف {$count} سؤال بنجاح");
        } else {
            // Single delete
            Question::findOrFail($request->id)->delete();
            session()->flash('delete', 'تم حذف السؤال بنجاح');
        }
        
        return redirect()->route('questions.index');
    }
    
    /**
     * Update the status of multiple questions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:questions,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $count = Question::whereIn('id', $request->selected_ids)->count();
        
        Question::whereIn('id', $request->selected_ids)
            ->update(['is_active' => $request->status]);
        
        $statusText = $request->status ? 'تفعيل' : 'تعطيل';
        
        return response()->json([
            'status' => 'success',
            'message' => "تم {$statusText} {$count} سؤال بنجاح"
        ]);
    }
}
