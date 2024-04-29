<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Institute_for_model;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasetableControllerAPI extends Controller
{
    use ApiTrait;
    public function institute_for(Request $request)
    {
        try {

            if (Auth::user()) {
                $base_institutfor = Institute_for_model::join('base_table', 'base_table.institute_for', '=', 'institute_for.id')
                    ->select('institute_for.id', 'institute_for.name', 'institute_for.icon')
                    ->distinct()
                    ->get();
                $data = [];
                foreach ($base_institutfor as $basedata) {
                    $data[] = array('id' => $basedata->id, 'name' => $basedata->name, 'icon' => url($basedata->icon));
                }
                return $this->response($data, "Fetch Data Successfully");
            }
            return $this->response([], "Something want Wrong!!", false, 400);
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }
}
