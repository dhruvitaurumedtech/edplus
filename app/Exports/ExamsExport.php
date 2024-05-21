<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExamsExport implements FromCollection
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
        if (!empty($this->exam_list)) {
            return array_keys($this->exam_list[0]);
        }
        
        // Return an empty array if exam list is empty
        return [];
    }
}
