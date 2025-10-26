<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoriesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Category([
            'title' => $row['name'] ?? "null",
            'description' => $row['explain'] ?? "null",
            'image' => $row['image'] ?? "null",
            'type' => $this->getType($row['type']),
            'is_active' => 1,
            'no_words' => $row['no_words'] ?? 0,
        ]);
    }
    public function rules(): array
    {
        return [
            'name' => 'required',
            'explain' => 'required',
            'image' => 'required',
            'type' => 'required',
            

        ];
    }

    protected function getType($type)
    {
        return $type == 'حصري' ? 'premium' : 'normal';
    }
}
