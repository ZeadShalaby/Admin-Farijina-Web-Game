<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\UpdatedQuestionsExport;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class QuestionImportController extends Controller
{
    /**
     * Step 1: Upload the ZIP, extract it, read the Excel, and store preview data in session.
     */
    public function uploadAndPreview(Request $request)
    {
        try {
            // 1. Validate the uploaded file is a ZIP
            $request->validate([
                'zip_file' => 'required|file|mimes:zip',
                'type' => 'required|string|in:yamaat,horror,vat',
            ]);

            // 2. Create a unique temp directory
            $tempPath = storage_path('app/temp/import_' . time() . '_' . uniqid());
            File::makeDirectory($tempPath, 0770, true);

            // 3. Extract the ZIP archive
            $zipFile = $request->file('zip_file');
            $zip = new \ZipArchive;
            if ($zip->open($zipFile->getRealPath()) !== true) {
                throw new \Exception('Cannot open the ZIP file.');
            }
            $zip->extractTo($tempPath);
            $zip->close();

            // 4. Ensure questions.xlsx is present
            $excelPath = $tempPath . '/questions.xlsx';
            if (!File::exists($excelPath)) {
                throw new \Exception('questions.xlsx not found in the ZIP package.');
            }

            // 5. Read the Excel file (first sheet) as a numeric array
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $excelPath)[0];

            // 6. Build a preview array of question data
            $questionsData = [];
            $filesPath = $tempPath . '/files/';

            foreach ($rows as $index => $row) {
                // Skip the header row
                if ($index === 0) {
                    continue;
                }

                // Extract columns
                $points    = $row[5] ?? 100;
                $question  = $row[3] ?? null;
                $answer    = $row[2] ?? null;
                $categ     = $row[4] ?? null;
                $direction = $row[6] ?? null ;
                $notes     = $row[7] ?? null;

                // Find category by title if needed
                $category = Category::where('title', $categ)->first();

                // Search for any media file matching "Q{index}.*" or "A{index}.*"
                $questionFile = $this->findMediaFile($filesPath, 'Q' . $index);
                $answerFile   = $this->findMediaFile($filesPath, 'A' . $index);

                // Prepare the data for preview
                $data = [
                    'points'         => intval($points), // Ensure points is stored as integer
                    'question'       => $question,
                    'answer'         => $answer,
                    'categ'          => $categ,
                    'category_id'    => $category ? $category->id : null,
                    'question_link'  => $questionFile,  // e.g. Q1.png, Q1.mp4, etc.
                    'answer_link'    => $answerFile,    // e.g. A1.mp3, A1.wav, etc.
                    'original_index' => $index,         // Store original index for reference
                    'direction'      => $direction,
                    'notes'          => $notes,
                ];

                $questionsData[] = $data;
            }

            // Log the original data (for debugging)
            Log::info('Before sorting - First 3 questions:', array_slice($questionsData, 0, 3));

            // 7. Sort by points ascending - enhanced with proper integer conversion
            usort($questionsData, function ($a, $b) {
                // Ensure points are integers
                $pointsA = $a['points'];
                $pointsB = $b['points'];

                // Use spaceship operator for comparison
                return $pointsA <=> $pointsB;
            });

            // Log the sorted data (for debugging)
            Log::info('After sorting - First 3 questions:', array_slice($questionsData, 0, 3));

            // 8. Add order information and re-index array with numeric keys
            $orderedData = [];
            foreach ($questionsData as $i => $data) {
                $data['sort_order'] = $i + 1; // Add sort order info
                $orderedData[] = $data;
            }
            $questionsData = array_values($orderedData); // Re-index to ensure numeric keys

            // 9. Store the data in session
            session([
                'import_temp_path' => $tempPath,
                'questions_data'   => $questionsData,
                'type'             => $request->type
            ]);

            // 10. Redirect to show the preview in your Blade
            return redirect()->route('import.index')
                ->with('success', 'تم رفع الملف بنجاح. يمكنك معاينة البيانات وتأكيد الاستيراد.');
        } catch (\Exception $e) {
            // Cleanup if something fails
            if (!empty($tempPath) && File::exists($tempPath)) {
                File::deleteDirectory($tempPath);
            }
            Log::error($e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء الرفع والمعاينة: ' . $e->getMessage());
        }
    }
    /**
     * Search for a media file in the given directory with any supported format.
     */
    private function findMediaFile($directory, $filename)
    {
        // Supported media file extensions
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'mp3', 'wav', 'ogg', 'webp', 'avif', 'svg', 'jfif', 'bmp'];

        foreach ($extensions as $ext) {
            $fullPath = $directory . $filename . '.' . $ext;
            if (File::exists($fullPath)) {
                return $filename . '.' . $ext;
            }
        }

        return null; // Return null if no matching file found
    }


    public function confirmImport(Request $request)
    {
        try {
            $tempPath      = session('import_temp_path');
            $questionsData = session('questions_data');
            $type = session('type');

            if (!$tempPath || !$questionsData) {
                throw new \Exception('لا توجد بيانات للاستيراد في الجلسة.');
            }

            // Group questions by points category
            $questionsByPoints = [
                200 => [],
                400 => [],
                600 => []
            ];

            // Populate the groups and track category_ids
            $questionsByCategoryId = [];
            foreach ($questionsData as $index => $data) {
                $points = $data['points'] ?? 200;
                $categoryId = $data['category_id'];

                // Group by points
                if (in_array($points, [200, 400, 600])) {
                    $questionsByPoints[$points][$index] = $data;
                }

                // Also group by category_id to track how many free questions each category has
                if (!isset($questionsByCategoryId[$categoryId])) {
                    $questionsByCategoryId[$categoryId] = [];
                }
                $questionsByCategoryId[$categoryId][$index] = $data;
            }

            // Check how many free questions each category already has in the database
            $categoryFreeQuestionCounts = [];
            foreach (array_keys($questionsByCategoryId) as $categoryId) {
                $categoryFreeQuestionCounts[$categoryId] = Question::where('category_id', $categoryId)
                    ->where('is_free', 1)
                    ->count();
            }

            // Initialize array to store indices of free questions
            $freeQuestionIndices = [];

            // Select 2 random questions from each point category
            foreach ([200, 400, 600] as $pointCategory) {
                $categoryQuestions = $questionsByPoints[$pointCategory];
                $categoryCount = count($categoryQuestions);

                if ($categoryCount > 0) {
                    // Filter questions by checking if their categories already have 6 free questions
                    $eligibleQuestions = [];
                    foreach ($categoryQuestions as $index => $data) {
                        $categoryId = $data['category_id'];
                        if ($categoryFreeQuestionCounts[$categoryId] < 6) {
                            $eligibleQuestions[$index] = $data;
                        }
                    }

                    // If we have eligible questions
                    $eligibleCount = count($eligibleQuestions);
                    if ($eligibleCount > 0) {
                        // Determine how many to select (up to 2)
                        $numToSelect = min(2, $eligibleCount);

                        // Get random indices from eligible questions
                        $selectedIndices = array_rand($eligibleQuestions, $numToSelect);
                        if (!is_array($selectedIndices)) {
                            $selectedIndices = [$selectedIndices];
                        }

                        // Add these indices to our free questions list and increment category counts
                        foreach ($selectedIndices as $idx) {
                            $freeQuestionIndices[] = $idx;
                            $categoryId = $eligibleQuestions[$idx]['category_id'];
                            $categoryFreeQuestionCounts[$categoryId]++;
                        }
                    }
                }
            }

            // Move each file to permanent storage and create question records
            foreach ($questionsData as $index => $data) {
                // If question_link is not null, move it with a new unique name
                $storedQuestionFile = null;
                if (!empty($data['question_link'])) {
                    $sourceQ = $tempPath . '/files/' . $data['question_link'];
                    if (File::exists($sourceQ)) {
                        $extension = pathinfo($data['question_link'], PATHINFO_EXTENSION);
                        $newQuestionName = 'q_' . uniqid() . '.' . $extension;
                        $storedQuestionFile = Storage::disk('public')
                            ->putFileAs('questions', new \Illuminate\Http\File($sourceQ), $newQuestionName);
                    }
                }

                // If answer_link is not null, move it with a new unique name
                $storedAnswerFile = null;
                if (!empty($data['answer_link'])) {
                    $sourceA = $tempPath . '/files/' . $data['answer_link'];
                    if (File::exists($sourceA)) {
                        $extension = pathinfo($data['answer_link'], PATHINFO_EXTENSION);
                        $newAnswerName = 'a_' . uniqid() . '.' . $extension;
                        $storedAnswerFile = Storage::disk('public')
                            ->putFileAs('answers', new \Illuminate\Http\File($sourceA), $newAnswerName);
                    }
                }

                // Determine if the question is free
                $isFree = in_array($index, $freeQuestionIndices) ? 1 : 0;

                // Create the question record
                Question::create([
                    'question'         => $data['question'] ?? '',
                    'answer'           => $data['answer'] ?? '',
                    'points'           => $data['points'] ?? 200,
                    'category_id'      => $data['category_id'],
                    'link_question'    => $storedQuestionFile,   // e.g. "questions/q_5f4dcc3b9a.jpg"
                    'link_answer'      => $storedAnswerFile,     // e.g. "answers/a_5f4dcc3b9a.png"
                    'link_type'        => $this->detectLinkType($storedQuestionFile),
                    'link_answer_type' => $this->detectLinkType($storedAnswerFile),
                    'is_active'        => 1,
                    'is_free'          => $isFree, // Mark as free if selected
                    'type'             => $type,
                ]);
            }

            // Cleanup temp folder & session
            File::deleteDirectory($tempPath);
            session()->forget(['import_temp_path', 'questions_data']);

            return redirect()->route('import.index')
                ->with('success', 'تم تأكيد الاستيراد وتخزين الملفات بنجاح.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تأكيد الاستيراد: ' . $e->getMessage());
        }
    }



    /**
     * Step 3: Cancel the import - delete temporary files and clear session.
     */
    public function cancelImport()
    {
        try {
            $tempPath = session('import_temp_path');
            if ($tempPath && File::exists($tempPath)) {
                File::deleteDirectory($tempPath);
            }
            session()->forget(['import_temp_path', 'questions_data']);

            return redirect()->route('import.index')
                ->with('error', 'تم إلغاء الاستيراد وحذف البيانات المؤقتة.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إلغاء الاستيراد: ' . $e->getMessage());
        }
    }

    /**
     * (Optional) Step 4: Download an updated Excel (if you have a route & export class).
     * You can skip or implement as needed.
     */
    public function downloadUpdatedExcel()
    {
        try {
            $questionsData = session('questions_data');
            if (!$questionsData) {
                return back()->with('error', 'There is no data to import in the session.');
            }

            // Use the custom export class to generate and download the Excel.
            return Excel::download(
                new UpdatedQuestionsExport($questionsData),
                'updated_questions.xlsx'
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'An error occurred while downloading Excel: ' . $e->getMessage());
        }
    }

    /**
     * Helper to detect file type by extension (image, video, voice, or text).
     */
    private function detectLinkType($filePath)
    {
        if (empty($filePath)) {
            return 'text';
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'avif', 'svg', 'jfif'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
        $voiceExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];

        if (in_array($extension, $imageExtensions)) {
            return 'image';
        }
        if (in_array($extension, $videoExtensions)) {
            return 'video';
        }
        if (in_array($extension, $voiceExtensions)) {
            return 'voice';
        }
        return 'text';
    }


    public function updateRowData(Request $request)
    {
        $request->validate([
            'row_index'    => 'required|integer',
            'question'     => 'required|string',
            'answer'       => 'required|string',
            'points'       => 'required|integer',
            'question_file' => 'nullable|file',
            'answer_file'  => 'nullable|file',
        ]);

        $tempPath      = session('import_temp_path');
        $questionsData = session('questions_data');

        if (!$tempPath || !$questionsData) {
            return back()->with('error', 'No import data in session.');
        }

        $rowIndex = $request->row_index;
        if (!isset($questionsData[$rowIndex])) {
            return back()->with('error', 'Invalid row index.');
        }

        // 1) Update text fields
        $questionsData[$rowIndex]['question'] = $request->question;
        $questionsData[$rowIndex]['answer']   = $request->answer;
        $questionsData[$rowIndex]['points']   = $request->points;

        // 2) If user uploaded a new question file, move it to the temp folder
        if ($request->hasFile('question_file')) {
            // Generate a unique filename or keep the original name
            $qFilename = uniqid('q_') . '.' . $request->file('question_file')->getClientOriginalExtension();
            $request->file('question_file')->move($tempPath . '/files', $qFilename);

            // Overwrite the old question_link
            $questionsData[$rowIndex]['question_link'] = $qFilename;
        }

        // 3) If user uploaded a new answer file
        if ($request->hasFile('answer_file')) {
            $aFilename = uniqid('a_') . '.' . $request->file('answer_file')->getClientOriginalExtension();
            $request->file('answer_file')->move($tempPath . '/files', $aFilename);

            $questionsData[$rowIndex]['answer_link'] = $aFilename;
        }

        // 4) Save updated data back to session
        session(['questions_data' => $questionsData]);

        return back()->with('success', 'تم تعديل السؤال/الإجابة والملفات بنجاح.');
    }



    /**
     * Allow user to upload a file if the image is empty.
     * This updates the session data so that 'question_link' or 'answer_link' is replaced.
     */
    public function uploadFileForRow(Request $request)
    {
        $request->validate([
            'row_index'   => 'required|integer',
            'column_name' => 'required|in:question_link,answer_link',
            'new_file'    => 'required|file',
        ]);

        $tempPath      = session('import_temp_path');
        $questionsData = session('questions_data');

        if (!$tempPath || !$questionsData) {
            return back()->with('error', 'No import data in session.');
        }

        // For safety, ensure row_index is in range
        $rowIndex = $request->row_index;
        if (!isset($questionsData[$rowIndex])) {
            return back()->with('error', 'Invalid row index.');
        }

        $file = $request->file('new_file');

        // Move the uploaded file into the temp "files" folder with the original name
        $filename = uniqid('temp_') . '.' . $file->getClientOriginalExtension();
        $file->move($tempPath . '/files', $filename);

        // Update the session data
        $columnName = $request->column_name; // "question_link" or "answer_link"
        $questionsData[$rowIndex][$columnName] = $filename;

        session(['questions_data' => $questionsData]);

        return back()->with('success', 'تم رفع الملف بنجاح وتحديث بيانات السؤال.');
    }
    /**
     * Preview a file from the temp directory in the browser (for the popup).
     */
    public function previewFile($filename)
    {
        try {
            // Get temp path from session
            $tempPath = session('import_temp_path');
            // if (!$tempPath) {
            //     Log::error('Preview file error: No import temp path in session');
            //     abort(404, 'No import data found.');
            // }

            // Build full path and verify file exists
            $fullPath = $tempPath . '/files/' . $filename;
            dd($fullPath);
            if (!File::exists($fullPath)) {
                Log::error('Preview file error: File not found at path: ' . $fullPath);
                abort(404, 'File not found in temp folder.');
            }

            // Get MIME type and return file
            $mimeType = File::mimeType($fullPath);

            // Make sure the file is readable
            if (!is_readable($fullPath)) {
                Log::error('Preview file error: File exists but is not readable: ' . $fullPath);
                chmod($fullPath, 0644); // Try to fix permissions
            }

            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            Log::error('Preview file exception: ' . $e->getMessage());
            abort(500, 'Error accessing file: ' . $e->getMessage());
        }
    }
}
