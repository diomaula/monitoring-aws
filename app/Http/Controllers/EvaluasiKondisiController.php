<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EvaluasiKondisiController extends Controller
{
    public function index()
    {
        //
        return view('report.evaluasi-kondisi');
    }

    public function indexDetail()
    {
        //
        return view('report.detail-evaluasi-kondisi');
    }
}
