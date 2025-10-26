<?php

namespace App\Exports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UpdatedQuestionsExport implements FromCollection, WithHeadings
{
    protected $questionsData;

    public function __construct(array $questionsData)
    {
        $this->questionsData = $questionsData;
    }

    /**
     * Define the headings (column titles) that appear in the exported Excel.
     * This matches your original file's column order:
     * A => answer_link
     * B => question_link
     * C => answer
     * D => question
     * E => categ
     * F => points
     */
    public function headings(): array
    {
        return [
            'answer_link',
            'question_link',
            'answer',
            'question',
            'categ',
            'points',
        ];
    }

    /**
     * Return a collection of rows to export.
     */
    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->questionsData as $data) {
            $rows->push([
                // Must match the headings order
                $data['answer_link']    ?? '',
                $data['question_link']  ?? '',
                $data['answer']         ?? '',
                $data['question']       ?? '',
                $data['categ']          ?? '',
                $data['points']         ?? '',
            ]);
        }

        return $rows;
    }
}
