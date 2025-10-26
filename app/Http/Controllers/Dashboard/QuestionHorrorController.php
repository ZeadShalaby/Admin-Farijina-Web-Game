<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\QuestionHorrorImport;
use App\Imports\QuestionImport;
use App\Models\Category;
use App\Models\Question;
use App\Traits\ImageProcessing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class QuestionHorrorController extends Controller
{
    use ImageProcessing;
    public function index(Request $request)
    {
        $query = Question::where("type", "horror");
  if ($request->filled('question_id')) {
            $query->where('id', $request->question_id);
        }
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%$searchTerm%")
                    ->orWhere('points', 'like', "%$searchTerm%")
                    ->orWhere('question', 'like', "%$searchTerm%")
                    ->orWhere('answer', 'like', "%$searchTerm%")
                    ->orWhere('link_type', 'like', "%$searchTerm%")
                    ->orWhere('link_answer_type', 'like', "%$searchTerm%")
                    ->orWhere('category_id', 'like', "%$searchTerm%")
                    ->orWhere('is_active', 'like', "%$searchTerm%")
                    ->orWhere('is_free', 'like', "%$searchTerm%");
                // ->orWhere('type', 'like', "%$searchTerm%");;
            });
        }

        if ($request->has('all') && $request->all == '1') {
            $questions = $query->get(); // بدون paginate
        } else {
            $questions = $query->paginate(10)->appends($request->all()); // مع paginate وحفظ البحث
        }

        return view('dashboard.question_horror.index', compact('questions'));
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
            'is_active' => 'boolean',
            'is_free' => 'boolean'
        ], [
            'points.required' => 'يرجى إدخال النقاط',
            'question.required' => 'يرجى إدخال السؤال',
            'answer.required' => 'يرجى إدخال الإجابة',
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
        Question::create([
            'points' => $request->points,
            'question' => $request->question,
            'type' => "horror",
            'answer' => $request->answer,
            'link_question' => 'questions/' . $questionFilePath,
            'link_answer' => 'answers/' . $answerFilePath,
            'link_type' => $request->link_type,
            'category_id' => 1,
            'is_active' => $request->has('is_active'),
            'is_free' => $request->has('is_free'),
        ]);
        session()->flash('Add', 'تم إضافة السؤال بنجاح');
        return redirect()->route('question_horror.index');
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
                'is_active' => 'boolean',
                'is_free' => 'boolean'
            ], [
                'points.required' => 'يرجى إدخال النقاط',
                'points.integer' => 'يجب أن تكون النقاط رقماً صحيحاً',
                'points.min' => 'يجب أن تكون النقاط 0 أو أكثر',
                'question.required' => 'يرجى إدخال السؤال',
                'answer.required' => 'يرجى إدخال الإجابة',
                'link_question.required' => 'يرجى إدخال رابط السؤال',
                'link_answer.required' => 'يرجى إدخال رابط الإجابة',
                'link_type.required' => 'يرجى اختيار نوع الرابط',
                'link_type.in' => 'نوع الرابط غير صالح',
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
            return redirect()->route('question_horror.index');
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
            Excel::import(new QuestionHorrorImport, $request->file('excel_file'));

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
        Question::findOrFail($request->id)->delete();
        session()->flash('delete', 'تم حذف السؤال بنجاح');
        return redirect()->route('question_horror.index');
    }
}
