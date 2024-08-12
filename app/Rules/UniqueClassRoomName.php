<?php

namespace App\Rules;

use App\Models\Class_room_model;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueClassRoomName implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }
    protected $editId;

    public function __construct($editId = null)
    {
        $this->editId = $editId;
    }

    public function passes($attribute, $value)
    {
        $data= Class_room_model::where('id', '=', $this->editId)->exists();
        echo $data;exit;
    }

    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
