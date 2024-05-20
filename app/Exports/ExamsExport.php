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
}
