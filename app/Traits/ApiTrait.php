<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ApiTrait
{

    public function response($data = [], $message = "Success", $success = true, $code = 200)
    {
        $res = [
            "data" => $data,
            "message" => $message,
            "success" => $success
        ];

        return response()->json($res, $code);
    }
}
