<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LoginModel;
use App\Models\ForgeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CorrelationController extends Controller 
{
    function index()
    {
        return view('correlation');
    }

    function sample()
    {
        if(session('userName')){ //check session data
            $forge = new ForgeModel();
            $login = new LoginModel();
            $projects = $forge->GetProjects();
            $authority_data = $login->GetAuthorityById(session('authority_id'));
            $ccc_all_authority_data = $login->GetAllAuthority();
            if(empty($authority_data)){
                $authority_data[0] = "";
            }
            return view('homedesign')->with(["projects"=>$projects, "authority_data"=>$authority_data[0],"ccc_all_authority_data"=>$ccc_all_authority_data]);
        }else{
            return view('login');
        }
    }
}
