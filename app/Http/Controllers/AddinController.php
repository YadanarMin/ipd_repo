<?php

namespace App\Http\Controllers;
use App\Models\CommonModel;
use App\Models\ForgeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class AddinController extends Controller
{
   
    function LoadCalcAshiba(){
        return view('calc_ashiba');
    }
    
    function LoadCalcKasetsu(){
        return view('calc_kasetsu');
    }
    
}
