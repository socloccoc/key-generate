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

class AppApiController extends BaseApiController
{
    public function index()
    {
        try{
            $apps = App::all();
            return $this->sendResponseData($apps, Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->sendError($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}