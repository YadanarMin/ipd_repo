<?php

namespace App\Http\Controllers;
use App\Models\ForgeModel;
use App\Models\ProjectMgtModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ProjectMgtController extends Controller 
{
    function index(){
        $forge = new ForgeModel();
        $projects = $forge->GetKoujiProjects();
        return view('project')->with(["projects"=>$projects]);
    }

    function saveData(Request $request){
        $prjMgt = new ProjectMgtModel();
        $message = $request->get('message');

        if($message == "save_project")
        {
            $name = $request->get('name');
            try{
                echo json_encode($prjMgt->saveProject($name));

            }catch(Exception $e)
            {
                echo $e->getMessage();
            }       
        }else if($message == "edit_project")
        {
            try{
                $oldName = $request->get('oldName');
                $updateData = json_decode($request->get('updateData'));

                echo json_encode($prjMgt->editProject($oldName, $updateData));

            }catch(Exception $e)
            {
                echo $e->getMessage();
            }   
        }else if($message == "edit_project2")
        {
            try{
                $oldName = $request->get('oldName');
                $data = $request->get('updateData');
                $hachuusha     = $data["hachuusha"];
                $sekkeisha     = $data["sekkeisha"];
                $meisho        = $data["meisho"];
                $startTime     = $data["startTime"];
                $endTime       = $data["endTime"];
                $kenchikuyoto  = $data["kenchikuyoto"];
                $kozo          = $data["kozo"];
                $zouchijyo     = $data["zouchijyo"];
                $kaichika      = $data["kaichika"];
                $yukamenseki   = $data["yukamenseki"];
                
                echo json_encode($prjMgt->editProject2($oldName, $hachuusha, $sekkeisha, $meisho, $startTime, $endTime, $kenchikuyoto, $kozo, $zouchijyo, $kaichika, $yukamenseki));
    
            }catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }else if($message == "edit_implementation_doc")
        {
            try{
                $oldName = $request->get('oldName');
                $newName = $request->get('newName');
                $updateData = json_decode($request->get('updateData'));
                
                echo json_encode($prjMgt->editImplementationDoc($oldName, $newName, $updateData));

            }catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }
    
    }
    
    function getData(Request $request){
        $message = $request->get('message');
        $prjMgt = new ProjectMgtModel();
        
    	if($message == "get_project") {
    		
    		$projectName = $request->get('name');
    		try{
    		    echo json_encode($prjMgt->getProject($projectName));
    		}catch(Exception $e) {
    			echo $e->getMessage();
    		}
    		
    	}else if($message == "getImplementationDocByProject") {
    		
    		$projectName = $request->get('name');
    		try{
    		    echo json_encode($prjMgt->getImplementationDocByProject($projectName));
    		}catch(Exception $e) {
    			echo $e->getMessage();
    		}
    	}else if($message == "getPojectReportInfo"){
    	    	$projectName = $request->get('name');
    		try{
    		    echo json_encode($prjMgt->getProjectReportInfo($projectName));
    		}catch(Exception $e) {
    			echo $e->getMessage();
    		}
	    }else if($message == "getImplementationDocComboData") {
		
            //$projectName = $request->get('name');
            $radio_state = $request->get('status');
		try{
    		echo json_encode($prjMgt->getImplementationDocComboData($radio_state));
		}catch(Exception $e) {
			echo $e->getMessage();
		}

    	}else if($message == "getProjectNameByImplementationDoc") {
    		
    		$prjAddressList     = $request->get('prjAddressList');
    		$buildingUseList    = $request->get('buildingUseList');
    		$ordererList        = $request->get('ordererList');
    // 		$relatedCompanyList = $request->get('relatedCompanyList');
            $branchStoreList    = $request->get('branchStoreList');
            $constructionTypeList = $request->get('constructionTypeList');
            $constructionList   = $request->get('constructionList');
            $designerList       = $request->get('designerList');
    		
    		try{
        		echo json_encode($prjMgt->getProjectNameByImplementationDoc($prjAddressList, $buildingUseList, $ordererList, $branchStoreList, $constructionTypeList, $constructionList,$designerList));
    		}catch(Exception $e) {
				echo $e->getMessage();
    		}

    	}

    }
    
    function deleteData(Request $request){
        $message = $request->get('message');
        $prjMgt = new ProjectMgtModel();
        
    	if($message == "delete_project") {
    		
    		$projectName = $_POST["name"];
    		
    		try{
    		    echo json_encode($prjMgt->deleteProject($projectName));
    		}catch(Exception $e)
    		{
    			echo $e->getMessage();
    		}
    	}
    }
    
}
