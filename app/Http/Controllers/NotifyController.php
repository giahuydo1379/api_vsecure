<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notify;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function showAll(Request $request)
    {
        $requestNotify = new Notify();
        $validator = $requestNotify->checkValidate($request);
        if ($validator->fails())
            return $this->responseFormat(422, $validator->errors());
        $data = [
            [
                "id" => 1,
                "nick_name" => "huy",
                "action" => "action1",
                "time_push" => "2019-01-04 09:35:44",
                "model_device" => " device1",
            ],
            [
                "id" => 2,
                "nick_name" => "huy",
                "action" => "action2",
                "time_push" => "2019-01-04 09:35:44",
                "model_device" => " device2",
            ],
        ];
        return $this->responseFormat(200, 'Success', $data);
    }

}
