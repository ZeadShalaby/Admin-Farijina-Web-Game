<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuestionVatImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Question([
            'question' => $row['question'],
            "category_id" => 2,
            'answer' => "no_answer",
            "type" =>  "vertebrae",
            'link_question' => null,
            'link_answer' => null,
            'points' => $row['points'],
            'link_type' => null, // Removed extra comma
            'link_answer_type' => null, // Removed extra comma
            'is_active' => 1,
            'is_free' => 0
        ]);
    }

    public function rules(): array
    {
        return [
            'question' => 'required',
            'answer' => 'nullable',
            'question_link' => 'nullable',
            'answer_link' => 'nullable',
            'points' => 'required|numeric',
        ];
    }

    private function detectLinkType($url)
    {
        if (empty($url)) {
            return 'text';
        }

        // Get file extension from URL
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        // Image extensions
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'avif', 'svg', 'jfif'];

        // Video extensions
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];

        // Audio extensions
        $voiceExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];

        // Check URL patterns for various services
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'video';
        }

        // Check file extensions
        if (in_array($extension, $imageExtensions)) {
            return 'image';
        }

        if (in_array($extension, $videoExtensions)) {
            return 'video';
        }

        if (in_array($extension, $voiceExtensions)) {
            return 'voice';
        }

        // Firebase Storage URL handling
        if (strpos($url, 'firebasestorage.googleapis.com') !== false) {
            // Check common image keywords in the URL
            $imageKeywords = ['image', 'img', 'photo', 'صورة', 'صور'];
            foreach ($imageKeywords as $keyword) {
                if (stripos($url, $keyword) !== false) {
                    return 'image';
                }
            }

            // Check video keywords
            $videoKeywords = ['video', 'فيديو'];
            foreach ($videoKeywords as $keyword) {
                if (stripos($url, $keyword) !== false) {
                    return 'video';
                }
            }

            // Check audio keywords
            $audioKeywords = ['audio', 'voice', 'sound', 'صوت'];
            foreach ($audioKeywords as $keyword) {
                if (stripos($url, $keyword) !== false) {
                    return 'voice';
                }
            }
        }

        // Default to text if no other type is detected
        return 'text';
    }
}
