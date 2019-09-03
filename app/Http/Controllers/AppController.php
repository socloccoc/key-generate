<?php

namespace App\Http\Controllers;

use App\Models\App;
use Illuminate\Http\Request;

class AppController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        return view('app.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:150',
        ], []);
        try {
            $app = App::create($request->all());
            if ($app) {
                return redirect('app/create')->with('message', 'Đăng ký app thành công !');
            }
            return redirect('app/create')->with('error_message', 'Đăng ký app thất bại !');
        } catch (\Exception $ex) {
            return redirect('app/create')->with('error_message', $ex);
        }
    }
}
