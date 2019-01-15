<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoorAlarm extends FormRequest
{
    private $ins = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->ins)
            return [
                'email' => 'required|email',
                'mac' => 'required|unique:dooralarm|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
            ];
        else
            return [
                'email' => 'required|email',
                'device_token' => 'required',
            ];
    }

    public function checkValidate(Request $request, $ins = true)
    {
        if (!$ins)
            $this->ins = false;
        $validator = Validator::make($request->all(), $this->rules());
        return $validator;
    }

}
