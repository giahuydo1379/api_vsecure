<?php

namespace App\Http\Controllers;

use App\Http\Models\Customer;
use App\Http\Models\DeviceToken;
use App\Http\Requests\Customer as CustomerRequest;
use App\Http\Requests\DoorAlarm as DoorAlarmRequest;
use App\Http\Models\DoorAlarm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $doorAlarms = DoorAlarm::all()->where('is_deleted', 0);
        return $this->responseFormat(200, 'Success', $doorAlarms);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $requestDoorAlarm = new DoorAlarmRequest();
            $validator = $requestDoorAlarm->checkValidate($request);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $cusId = Customer::where(['email' => $request->email, 'is_deleted' => 0])->pluck('id')->first();
            if (!$cusId)
                return $this->responseFormat(404, 'Not found customer');
            $doorAlaram = new DoorAlarm(['mac' => $request->mac]);
            $customer = Customer::find($cusId);
            $customer->doorAlarms()->save($doorAlaram);

            return $this->responseFormat(200, 'Success');

        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $email = $request->email;
        $requestCus = new CustomerRequest();
        $validator = $requestCus->checkValidate($request);
        if ($validator->fails())
            return $this->responseFormat(422, $validator->errors());
        $cusId = Customer::where(['email' => $request->email, 'is_deleted' => 0])->pluck('id')->first();
        if (!$cusId)
            return $this->responseFormat(404, 'Not found customer');
        $customer = Customer::find($cusId);
        $customer->doorAlarms;
//        $arr = array();
//        array_push($arr, $customer);
        return $this->responseFormat(200, 'Success', $customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $email = $request->email;
            $requestCus = new CustomerRequest();
            $validator = $requestCus->checkValidate($request, false);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $customer = Customer::where(['email' => $request->email, 'is_deleted' => 0])->first();
            if (!$customer)
                return $this->responseFormat(404, 'Not found customer');
            $doorAlarms = $customer->doorAlarms;
            if ($doorAlarms->isEmpty())
                return $this->responseFormat(404, 'Not found door alarms');
            foreach ($doorAlarms as $doorAlarm) {
                if ($doorAlarm->mac == $request->mac) {
                    $doorAlarm->fill($request->all());
                    $doorAlarm->save();
                    return $this->responseFormat(200, 'Success', $customer->doorAlarms->find($doorAlarm->id));
                }

            }
            return $this->responseFormat(404, 'Not found door alarm');
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }

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
            DeviceToken::destroy($id);
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
            $deviceTokenId = DeviceToken::where(['customer_id' => $cusId, 'device_token' => $deviceToken])
                ->pluck('id')->first();
            if (!$deviceTokenId)
                return $this->responseFormat(404, 'Not found device token');
//            $customer = Customer::where(['email' => $email, 'is_deleted' => 0])->first();
//            $device = $customer->deviceToken->where('device_token',$deviceToken);
//            return $this->responseFormat(422, 'aaa',$device);
            return $this->destroy($deviceTokenId);
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }
    }

    public function showCustomer(Request $request)
    {
        $mac = $request->mac;
        $validator = Validator::make($request->all(), [
            'mac' => 'required|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
        ]);
        if ($validator->fails())
            return $this->responseFormat(422, $validator->errors());
        $doorAlarmId = DoorAlarm::where(['mac' => $mac, 'is_deleted' => 0])->pluck('id')->first();
        if (!$doorAlarmId)
            return $this->responseFormat(404, 'Not found door alarm');
        $doorAlarm = DoorAlarm::find($doorAlarmId);
        $doorAlarm->customers;
//        $arr = array();
//        array_push($arr, $customer);
        return $this->responseFormat(200, 'Success', $doorAlarm);
    }
}
