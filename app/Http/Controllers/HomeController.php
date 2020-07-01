<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function user(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
            'message' => __('Success')
        ]);
    }
}
