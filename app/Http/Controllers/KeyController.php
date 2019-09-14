<?php

namespace App\Http\Controllers;

use App\Exports\KeyExport;
use App\Models\App;
use App\Models\AppDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
//use Exportable;

use App\Exports\AliExport;

class KeyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = isset($request->status) ? $request->status : 1;
        $keys = AppDetail::with('app')
            ->where(function ($query) use ($status) {
                if ($status == 1) {
                    $query->where('serial_number', '!=', null);
                }else{
                    $query->where('serial_number', '=', null);
                }
            })
            ->get();
        return view('key.index', compact('keys', 'status'));
    }

    public function updateExpireDate(Request $request)
    {
        $this->validate($request, [
            'modal_key_id' => 'required|integer|min:0',
            'expire_date'  => 'required',
            'point'        => 'required|integer|min:0',
        ], []);
        try {
            $data = [
                'expire_date' => $request->expire_date,
                'point'       => $request->point,
            ];
            $key = AppDetail::where('id', $request->modal_key_id)->limit(1)->update($data);
            if ($key) {
                return redirect('/key')->with('message', 'Cập nhật expire_date thành công !');
            } else {
                return redirect('/key')->with('error_message', 'Cập nhật expire_date thất bại !');
            }
        } catch (\Exception $ex) {
            return redirect('/key')->with('error_message', $ex);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $apps = App::all();
        return view('key.create', compact('apps'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'app'         => 'required|integer',
            'number'      => 'required|min:1|max:1000',
            'expire_time' => 'required|min:1|max:1000',
        ], []);
        try {
            $keys = [];
            for ($i = 0; $i < $request->number; $i++) {
                $keys[] = [
                    'app_id'      => $request->app,
                    'key'         => $this->random_strings(9),
                    'expire_time' => $request->expire_time,
                    'point'       => isset($request->point) ? $request->point : 0,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now()
                ];
            }

            $key = AppDetail::insert($keys);
            if (!$key) {
                return redirect('key/create')->with('error_message', 'Key generate error !');
            }

            $app = App::where('id', $request->app)->first();
            if (!$app) {
                return redirect('key/create')->with('error_message', 'App not found !');
            }

            $appName = $app->name;
            for ($i = 0; $i < count($keys); $i++) {
                $keys[$i]['app_id'] = $appName;
            }

            // export excel
            $header = $this->getHeader();
            $dataExcel = new KeyExport([$keys], $header);

            $excel = Excel::download($dataExcel, "key-gen-" . Carbon::now()->format('Y-m-d-his') . ".xlsx");
            return $excel;
        } catch (\Exception $ex) {
            return redirect('key/create')->with('error_message', $ex);
        }
    }

    private function getHeader()
    {
        return $header = [
            'App',
            'Key',
            'Expire_time',
            'Point',
            'created_at',
            'updated_at'
        ];
    }

    private function random_strings($length_of_string)
    {
        return substr(bin2hex(random_bytes($length_of_string)),
            0, $length_of_string);
    }
}
