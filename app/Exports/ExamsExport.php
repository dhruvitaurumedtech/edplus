<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ExamsExport implements FromCollection, WithHeadings
{
    protected $exam_list;

    public function __construct($exam_list)
    {
        $this->exam_list = $exam_list;
    }

    public function collection()
    {
        return new Collection($this->exam_list);
    }

    public function headings(): array
    {
        $uniqueKeys = [];
        $data = [];
        foreach ($this->exam_list as $exam) {
            $attributes = $exam->getAttributes();
            foreach ($attributes as $key => $value) {
                if (!isset($uniqueKeys[$key])) {
                    $data[]=$key;
                    $uniqueKeys[$key] = true;
                }
            }
        }
        return $data;
    }
}
