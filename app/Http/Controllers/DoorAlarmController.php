<?php

namespace App\Http\Controllers;

use App\Http\Models\Customer;
use App\Http\Models\DeviceToken;
use App\Http\Requests\Customer as CustomerRequest;
use App\Http\Requests\DoorAlarm as DoorAlarmRequest;
use App\Http\Requests\CustomerAddDevice as AddDoorAlarmRequest;
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
     * If door alarm is exists -> update device token
     * else create door alarms and device token
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $requestDoorAlarm = new AddDoorAlarmRequest();
            $validator = $requestDoorAlarm->checkValidate($request);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $customer = Customer::where(['email' => $request->email, 'is_deleted' => 0])->first();
            if (!$customer)
                return $this->responseFormat(404, 'Not found customer');
            $doorAlarms = $customer->doorAlarms;
            $doorAlarm = DoorAlarm::where('mac', $request->mac)->first();
            $deviceTokenId = DeviceToken::where(['customer_id' => $customer->id,
                'device_token' => $request->device_token])->pluck('id')->first();
            if (!$deviceTokenId)
                return $this->responseFormat(404, trans('messages.not_found', ['name' => 'device token']));

            if (!$doorAlarm) {
                $doorAlaramNew = new DoorAlarm(['mac' => $request->mac]);
                $insertDevice = Customer::saveDoorAlarm($customer, $doorAlaramNew, $deviceTokenId);
                if ($insertDevice)
                    return $this->responseFormat(200, trans('messages.success'));
                else
                    return $this->responseFormat(422, trans('messages.fail'));
            } else {

                $deviceToken = DeviceToken::where(['customer_id' => $customer->id, 'dooralarm_id' => $doorAlarm->id, 'parent_id' => $deviceTokenId])
                    ->first();
                if ($deviceToken)
                    return $this->responseFormat(404, trans('messages.exists', ['name' => 'device token']));
                else {
                    $update = Customer::attachDoorAlarm($customer, $doorAlarm->id, $deviceTokenId);

                    if ($update)
                        return $this->responseFormat(200, trans('messages.success'));
                    else
                        return $this->responseFormat(422, trans('messages.fail'));
                }
            }
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
        try {
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
        } catch (\Exception $exception) {
            return $this->responseFormat(500, trans('messages.service_error'));
        }

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
            $mac = $request->mac;
            $requestDoorAlarm = new CustomerRequest();
            $validator = $requestDoorAlarm->checkValidate($request, false);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $customer = Customer::where(['email' => $email, 'is_deleted' => 0])->first();
            if (!$customer)
                return $this->responseFormat(404, trans('messages.not_found', ['name' => 'customer']));
            $doorAlarm = DoorAlarm::where('mac', $mac)->first();
            if (!$doorAlarm)
                return $this->responseFormat(404, trans('messages.not_found', ['name' => 'door alarm']));
            $idDeviceToken = DeviceToken::where(['dooralarm_id' => $doorAlarm->id, 'customer_id' => $customer->id])
                ->pluck('id')->first();
            if (!$idDeviceToken)
                return $this->responseFormat(404, trans('messages.not_found', ['name' => 'device token']));
            return $this->destroy($idDeviceToken);
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }
    }

    public function showCustomer(Request $request)
    {
        $mac = $request->mac;
        $validator = Validator::make($request->all(), [
            'mac' => 'required|regex:/^([0-9A-Fa-f]{2}){5}([0-9A-Fa-f]{2})$/',
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

    public function share(Request $request)
    {
        try {
            $requestDoorAlarm = new CustomerRequest();
            $validator = $requestDoorAlarm->checkValidate($request, false);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $customer = $this->checkExistCustomer($request->email);

            if (!$customer)
                return $this->responseFormat(404, trans('messages.not_found', ['name' => 'customer']));
            $doorAlarm = $this->checkExistDoorAlarm($request->mac);
            if (!$doorAlarm)
                return $this->responseFormat(404, trans('messages.not_found', ['name' => 'door alarm']));
//            $deviceToken = $this->checkExistDeviceToken($customer->id, $doorAlarm->id);
            $parentDeviceTokenIds = DeviceToken::findDeviceByCustomerId($customer->id);
//            return $this->responseFormat(200, trans('messages.fail'), $parentDeviceTokenIds->isEmpty());
            $share = DeviceToken::deviceTokenShare($customer, $doorAlarm, $parentDeviceTokenIds);
            if (!$share)
                return $this->responseFormat(200, trans('messages.fail'));
            return $this->responseFormat(200, trans('messages.success'));

        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }
    }

//    --------------------- PRIVATE FUNCTION ---------------------------- //

    private function checkExistCustomer($email)
    {
        $customer = Customer::where(['email' => $email, 'is_deleted' => 0])->first();
        return $customer;
    }

    private function checkExistDoorAlarm($mac)
    {
        $doorAlarm = DoorAlarm::where(['mac' => $mac])->first();
        return $doorAlarm;
    }

    private function checkExistDeviceToken($customerId, $doorAlarmId)
    {
        if ($customerId && $doorAlarmId) {
            $deviceToken = DeviceToken::where(['customer_id' => $customerId, 'dooralarm_id' => $doorAlarmId])
                ->first();
            return $deviceToken;
        } else {
            return null;
        }
    }

    public function insert(Request $request)
    {
        try {
            $doorAlarm = new DoorAlarm;
            $doorAlarm->mac = $request->mac;
            $doorAlarm->save();
            return $this->responseFormat(200, 'Success');
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }

    }

    /*
     * params int $doorAlarm id
     * return false | object => not exist | exist
     *
     * */
    public static function getDoorAlarmById($doorAlarmId)
    {
        $doorAlarm = DoorAlarm::find($doorAlarmId);
        if (!$doorAlarm)
            return false;
        return $doorAlarm;
    }

    public static function showCusbyDoorAlarmId($doorAlarmId)
    {
        $doorAlarm = self::getDoorAlarmById($doorAlarmId);
        if (!$doorAlarm)
            return false;
        $customers = $doorAlarm->customers;
        return $customers;
    }

}
