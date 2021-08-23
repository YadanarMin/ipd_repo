<?php

namespace App\Http\Controllers;
// use App\Models\ForgeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DashBoardController extends Controller 
{
    function index()
    {
        return view('base');
    }
    
    // function putData(Request $request)
    // {
    //     $ganttJSON = $request->get('ganttJSON');
    //     $fileName = $request->get('fileName');
    //     return file_put_contents($fileName, $ganttJSON, LOCK_EX);
    // }

    // function getData(Request $request)
    // {
    //     $fileName = $request->get('fileName');
    //     return file_get_contents($fileName);
    // }

}
