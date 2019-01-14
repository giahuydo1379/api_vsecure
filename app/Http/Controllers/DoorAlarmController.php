<?php

namespace App\Http\Controllers;

use App\Http\Models\Customer;
use App\Http\Models\DeviceToken;
use App\Http\Requests\DoorAlarm as DoorAlarmRequest;
use App\Http\Models\DoorAlarm;
use Illuminate\Http\Request;

class DoorAlarmController extends Controller
{
    private $doorAlarm;

    public function __construct()
    {
        $this->doorAlarm = new DoorAlarm();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
        try {
            $doorAlarm = DoorAlarm::find($id);
            if (!$doorAlarm)
                return $this->responseFormat(404, 'Not found');
            if (!$doorAlarm->delete())
                return $this->responseFormat(422, 'Deleted failed');
            return $this->responseFormat(200, 'Success');

        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $email = $request->email;
            $deviceToken = $request->device_token;
            $requestDoorAlarm = new DoorAlarmRequest();
            $validator = $requestDoorAlarm->checkValidate($request, false);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $cusId = Customer::where(['email' => $email, 'is_deleted' => 0])->pluck('id')->first();
            if (!$cusId)
                return $this->responseFormat(404, 'Not found customer');
            $doorAlarmId = DeviceToken::where(['customer_id' => $cusId, 'device_token' => $deviceToken])
                ->pluck('id')->first();
            if (!$doorAlarmId)
                return $this->responseFormat(404, 'Not found door alarm');
            $this->destroy($doorAlarmId);
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }
    }
}
