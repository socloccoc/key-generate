<?php

namespace App\Http\Controllers\Api;

use App\Models\App;
use App\Models\AppDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Matrix\Exception;
use Symfony\Component\HttpFoundation\Response;
use Validator;
use File;

class KeyApiController extends BaseApiController
{
    public function checkKey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'           => 'required|max:9',
            'serial_number' => 'required',
            'app_name'      => 'required',
        ], [
            'key.required'           => 'Key không được để trống',
            'key.max'                => 'Key không được quá 9 ký tự',
            'serial_number.required' => 'Serial Number không được để trống',
            'app_name.required'      => 'App Name không được để trống',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        $key = $request->key;

        $keyExist = AppDetail::where('key', $key)->first();

        if (!$keyExist) {
            return $this->sendError('Key không hợp lệ !', Response::HTTP_BAD_REQUEST);
        }

        // check app_name
        $appName = $request->app_name;
        $app = App::where('name', $appName)->first();
        if (!$app) {
            return $this->sendError('App không tồn tại !', Response::HTTP_NOT_FOUND);
        }

        $appId = $app->id;

        if ($keyExist->serial_number == '') {
            if ($keyExist->expire_date == '') {
                $data = [
                    'serial_number' => $request->serial_number,
                    'expire_date'   => Carbon::now()->addDays($keyExist->expire_time)->format('y-m-d H:i:s')
                ];
            } else {
                $data = [
                    'serial_number' => $request->serial_number
                ];
            }
            try {
                $updateKey = AppDetail::where('id', $keyExist->id)->where('app_id', $appId)->limit(1)->update($data);
                if ($updateKey) {
                    return $this->sendResponse(true, Response::HTTP_OK);
                }
                return $this->sendError('Key hoặc App Không hợp lệ !', Response::HTTP_BAD_REQUEST);
            } catch (\Exception $ex) {
                return $this->sendError($ex->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        } else {
            $keyRegisted = AppDetail::where('key', $key)->where('serial_number', $request->serial_number)->where('app_id', $appId)->first();
            if ($keyRegisted) {
                $curentTime = Carbon::now()->format('Y-m-d H:i:s');
                if ($curentTime > $keyRegisted->expire_date) {
                    return $this->sendError('Key đã hết hạn !', Response::HTTP_BAD_REQUEST);
                }
                return $this->sendResponse(true, Response::HTTP_OK);
            }
            return $this->sendError('Key hoặc serial_number hoặc App không hợp lệ !', Response::HTTP_BAD_REQUEST);
        }

    }

    public function getPointByKey($key)
    {
        $key = AppDetail::where('key', $key)->first();
        if ($key) {
            return response()->json(['point' => $key->point], Response::HTTP_OK);
        }
        return $this->sendError('Key không hợp lệ !', Response::HTTP_BAD_REQUEST);
    }

    public function updatePoint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|max:9',
        ], [
            'key.required' => 'Key không được để trống',
            'key.max'      => 'Key không được quá 9 ký tự',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        $key = $request->key;

        $keyExist = AppDetail::where('key', $key)->first();

        if (!$keyExist) {
            return $this->sendError('Key không hợp lệ !', Response::HTTP_BAD_REQUEST);
        }

        try {
            $newPoint = $keyExist->point > 0 ? $keyExist->point - 1 : 0;
            $updatePoint = AppDetail::where('id', $keyExist->id)->limit(1)->update(['point' => $newPoint]);
            if ($updatePoint) {
                return $this->sendResponse(true, Response::HTTP_OK);
            }
            return $this->sendError('Key Không hợp lệ !', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), Response::HTTP_BAD_REQUEST);
        }

    }

    protected function checkKeyExist($key, $registered = false)
    {
        $key = AppDetail::where('key', $key)
            ->where(function ($query) use ($registered) {
                if ($registered) {
                    $query->where('serial_number', '!=', '');
                }
            })
            ->first();
        if ($key) {
            return true;
        }
        return false;
    }

    /**
     * check key has not been used
     * @param $key
     * @param $appId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function validateKey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'    => 'required|max:9',
            'app_id' => 'required',
        ], [
            'key.required'    => 'Key không được để trống',
            'key.max'         => 'Key không được quá 9 ký tự',
            'app_id.required' => 'App không được để trống',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $app = App::where('id', $request->app_id)->first();
            if (!$app) {
                return $this->sendError('Key không tồn tại hoặc đã được sử dụng', Response::HTTP_NOT_FOUND);
            }
            $key = AppDetail::where('app_id', $request->app_id)->where('key', $request->key)->where('serial_number', null)->first();
            if ($key) {
                return $this->sendResponse($key);
            }
            return $this->sendError('( ' . $app['name'] . ' ) Key không tồn tại hoặc đã được sử dụng', Response::HTTP_NOT_FOUND);
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * add point for the key
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function addPointForKey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'   => 'required|max:9',
            'point' => 'required|integer',
        ], [
            'key.required'   => 'Key không được để trống',
            'key.max'        => 'Key không được quá 9 ký tự',
            'point.required' => 'point không được để trống',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        $key = $request->key;

        $keyExist = AppDetail::where('key', $key)->first();
        if (!$keyExist) {
            return $this->sendError('Key không hợp lệ !', Response::HTTP_BAD_REQUEST);
        }

        $keyExpired = $this->checkKeyExpired($key);
        if ($keyExpired) {
            return $this->sendError('Key đã hết hạn !', Response::HTTP_BAD_REQUEST);
        }
        try {
            $point = $request->point + $keyExist['point'];
            $updateKey = AppDetail::where('key', $key)->limit(1)->update(['point' => $point]);
            if ($updateKey) {
                return $this->sendResponse($updateKey);
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * check key expired
     * @param $key
     * @return bool
     */
    public function checkKeyExpired($key)
    {
        try {
            $app = AppDetail::where('key', $key)->where('expire_date', '<', Carbon::now()->format('Y-m-d H:i:s'))->first();
            if ($app) {
                return true;
            }
            return false;
        } catch (\Exception $ex) {
            return false;
        }
    }
}