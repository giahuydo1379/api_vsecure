<?php
/**
 * Created by PhpStorm.
 * User: taishiro
 * Date: 1/14/19
 * Time: 3:17 PM
 */

namespace App\Repositories;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\DeviceToken as DeviceTokenModel;

class DeviceToken extends Controller implements RepositoriesInterface
{
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $deviceToken = DeviceTokenModel::findOrCreate(['email' => $request->email, 'is_deleted' => 0]);
            dd($deviceToken->exists);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function selectAll()
    {
        // TODO: Implement selectAll() method.
    }

}