<?php

namespace App\Http\Controllers;
use App\Models\ForgeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class GanttChartController extends Controller 
{
    function index()
    {
        $pjId   = "";
        $pjName = "";
        $pjCode = "";
        if (session()->has('processPjName'))
        {
            $pjId = session('processPjId');
            $pjName = session('processPjName');
            $pjCode = session('processPjCode'); //project type
        }
        return view('gantt')->with(['processPjId'=>$pjId,'processPjName'=>$pjName,'processPjCode'=>$pjCode]);
    }

    function processControll()
    {
        $forge = new ForgeModel();
        $projects = $forge->GetKoujiProjects();
        for($i = 0; $i < count($projects); $i++){
            $prjName = $projects[$i]["name"];
            $query = "SELECT pj_name FROM tb_gantt WHERE pj_name = '$prjName'";
            $data = DB::select($query);
            if(count($data) == 0){
                $projects[$i]["isExist"] = 0;
            }else{
                $projects[$i]["isExist"] = 1;
            }
        }
        return view('processControll')->with(["projects"=>$projects]);
    }
    
    function putData(Request $request)
    {
        $isTemp = $request->get('isTemp');
        $gantt_data = $request->get('gantt_data');
        
        if($isTemp == 0){
            $pj_code = $request->get('pj_code');
            $pj_name = $request->get('pj_name');
            
            $query = "INSERT INTO tb_gantt(id,pj_code,pj_name,gantt_data)
                    SELECT COALESCE(MAX(id), 0) + 1,'$pj_code','$pj_name','$gantt_data' FROM tb_gantt
                    ON DUPLICATE KEY UPDATE pj_code = '$pj_code',pj_name = '$pj_name',gantt_data = '$gantt_data'";
            DB::insert($query);
            return 'success';
        }else{
            $fileName = $request->get('fileName');
            return file_put_contents($fileName, $gantt_data);
        }
        
    }

    function getData(Request $request)
    {
        $isTemp = $request->get('isTemp');
        
        if($isTemp == 0){
            $pj_name = $request->get('pj_name');
            $query = "SELECT * FROM tb_gantt WHERE pj_name = '$pj_name'";
            $data = DB::select($query);     
            return $data;
        }else{
            $fileName = $request->get('fileName');
            return file_get_contents($fileName);
        }
    }
    
    function SetProjectIdToSession(Request $request)
    {
        $projectId = $request->projectId;
        $projectName = $request->projectName;
        $projectCode = $request->projectCode;
        session(['processPjId' => $projectId]);
        session(['processPjName' => $projectName]);
        session(['processPjCode' => $projectCode]);
        return "success";
    }
    
}
