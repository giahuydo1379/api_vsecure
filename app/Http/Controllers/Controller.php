<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseFormat($code = 401, $msg = '', $data = null, $total = 0, $isList = false)
    {
        switch ($code) {
            case 200:
                $msg = $msg ? $msg : trans('messages.success');
                break;
            case 204:
                $msg = $msg ? $msg : trans('messages.not_content_or_deleted');
                break;
            case 401:
                $msg = $msg ? $msg : trans('messages.unauthorised');
                break;
            case 404:
                $msg = $msg ? $msg : trans('messages.not_found');
                break;
            case 406:
                $msg = $msg ? $msg : trans('messages.not_acceptable');
                break;
            case 422:
                $msg = $msg ? $msg : trans('messages.unprocessable_entity');
                break;
            case 417:
                $msg = $msg ? $msg : trans('messages.except_failed');
                break;
            case 500:
                $msg = $msg ? $msg : trans('messages.service_errors');
                break;
            default:
                $msg = $msg ? $msg : trans('messages.check_status_code_again');
        }
        if ($isList)
            $result = [
                'code' => $code,
                'msg' => $msg,
                'data' => [
                    'total' => $total,
                    'rows' => $data ? $data : []
                ]
            ];
        else
            $result = [
                'code' => $code,
                'msg' => $msg,
                'data' => $data ? $data : null
            ];
        return response()->json($result);
    }

}
