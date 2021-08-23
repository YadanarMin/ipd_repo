<?php

namespace App\Http\Controllers;
use App\Models\ForeignStudentModel;
use App\Models\PersonalModel;
use App\Models\CompanyModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; 

class ForeignStudentsController extends Controller
{
    
    public function index()
    {
        return view('foreignStudentsInfo');
    }
    
    public function ShowData(){
        $fstudent = new ForeignStudentModel();
        $allStudents = $fstudent->GetAllStudents();
        return view('foreignStudentsShow');
       
    }
    
    
    
    public function GetCompareData(Request $request){
        $message = $request->get('message');
        $idList = $request->get('list');
        
        $student = new ForeignStudentModel();
        if($message == "getCompareStudentData"){
            $compareStudents = $student->GetCompareStudent($idList);
             
            //$html = view('foreignStudentsCompare')->with(compact('compareStudents'))->render();
            //return response()->json(['success' => true]);
            return array("listOfStudents"=>$compareStudents);
        }
    }
    
    public function CompareData($id){
        //print_r(gettype($id));
        $idList = explode(',', $id); //Change string to array
        //print_r(gettype($idList));
        $loginUser = session("userName");
        $student = new ForeignStudentModel();
        $company = new CompanyModel();
        $personal = new PersonalModel();
        
        $compareStudents = $student->GetCompareStudent($idList);
        $compareStudentList = array();
        
        for($i=0; $i<count($compareStudents); $i++){
            $personalId = $compareStudents[$i]['id'];
            
             //**********大林組所属と支店「現在」**************//
            $branchId = $compareStudents[$i]['branch_id'];
            if(!empty($branchId)){
                $branchInfo = $company->GetBranchById($branchId);
                $compareStudents[$i]["genzai_branch_name"] = $branchInfo[0]["name"];
            }else{
                $compareStudents[$i]["genzai_branch_name"] = "";
            }
            
            $deptId  = $compareStudents[$i]['dept_id'];
            if(!empty($deptId)){
                $deptInfo = $company->GetDeptById($deptId);
                if(count($deptInfo)>0){
                    $compareStudents[$i]["genzai_dept_name"] = $deptInfo[0]["name"];
                    
                }else{
                    $compareStudents[$i]["genzai_dept_name"] = "";
                }
            }else{
                $compareStudents[$i]["genzai_dept_name"] = "";
            }
            
            //**********派遣元所属「現在」**************//
            $genzaiHakenData = $personal->GetHakenCompanyByUserId($personalId);
            if(count($genzaiHakenData)>0){
                $hakenCompanyId = $genzaiHakenData[0]["company_id"];
                $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                if(count($hakenCompanyData) >0){
                    $compareStudents[$i]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];
                }else{
                    $compareStudents[$i]["genzai_haken_company_name"] = "";
                }
            }else{
                $compareStudents[$i]["genzai_haken_company_name"] = "";
            }
            
            //**********支店、大林組所属、派遣元所属「留学」**************//
            $foreignStudentInfo = $personal->GetStudentInfoByUserId($personalId);
            if(count($foreignStudentInfo) >0){
                $studentHakenCompanyId = $foreignStudentInfo[0]['s_haken_company_id'];
                if(!empty($studentHakenCompanyId)){
                    $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                    if(count($studentHakenCompanyData) >0){
                        $compareStudents[$i]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                    }else{
                        $compareStudents[$i]["s_haken_company_name"] = "";
                    }
                }else{
                    $compareStudents[$i]["s_haken_company_name"] = "";
                }
                
                        
                $studentBranchId = $foreignStudentInfo[0]['s_branch_id'];
                if(!empty($studentBranchId)){
                    $studentBranchInfo = $company->GetBranchById($studentBranchId);
                    $compareStudents[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                }else{
                    $compareStudents[$i]["s_branch_name"] = "";
                }
                
                $studentDeptId   = $foreignStudentInfo[0]['s_dept_id'];
                if(!empty($studentDeptId)){
                    $studentDeptInfo = $company->GetDeptById($studentDeptId);
                    if(count($studentDeptInfo)>0){
                        $compareStudents[$i]["s_dept_name"] = $studentDeptInfo[0]["name"];
                    }else{
                        $compareStudents[$i]["s_dept_name"] = "";
                    }
                }else{
                    $compareStudents[$i]["s_dept_name"] = "";
                }
                        
                $compareStudents[$i]['genzai_field'] = $foreignStudentInfo[0]['genzai_field'];
                $compareStudents[$i]['genzai_skill'] = $foreignStudentInfo[0]['genzai_skill'];
                $compareStudents[$i]['genzai_type'] = $foreignStudentInfo[0]['genzai_type'];
                $compareStudents[$i]['s_field'] = $foreignStudentInfo[0]['s_field'];
                $compareStudents[$i]['s_skill'] = $foreignStudentInfo[0]['s_skill'];
                $compareStudents[$i]['s_type'] = $foreignStudentInfo[0]['s_type'];
                $compareStudents[$i]['s_code'] = $foreignStudentInfo[0]['s_code'];
                $compareStudents[$i]['startDate'] = $foreignStudentInfo[0]['startDate'];
                $compareStudents[$i]['endDate'] = $foreignStudentInfo[0]['endDate'];
                $compareStudents[$i]['puchi1'] = $foreignStudentInfo[0]['puchi1'];
                $compareStudents[$i]['puchi2'] = $foreignStudentInfo[0]['puchi2'];
                $compareStudents[$i]['puchi3'] = $foreignStudentInfo[0]['puchi3'];
                $compareStudents[$i]['puchi4'] = $foreignStudentInfo[0]['puchi4'];
                        
            }
            $compareStudentList[] = $compareStudents[$i];
        }
        
        $hideOrShow  = $student->HideOrShowTable($loginUser);
        $data = [
            'students'  => $compareStudentList,
            'hideOrShow'   => $hideOrShow
        ];
        
        return view('foreignStudentsCompare')->with($data);
    }
    
    public function GetData(Request $request){
        $message = $request->get('message');
        $student = new ForeignStudentModel();
        $personal = new PersonalModel();
        $company = new CompanyModel();
        
        //**********留学生全員取得**********//
        if($message == 'getAllStudents'){
            // $allStudents = $student->GetAllStudents();
            $foreignStudent = $personal->GetAllForeignStudents();
            if( count($foreignStudent) > 0){
                for($i=0; $i<count($foreignStudent) ;$i++){
                    $id = $foreignStudent[$i]['id'];
                    
                    $branchId = $foreignStudent[$i]['branch_id'];
                    if(!empty($branchId)){
                        $branchInfo = $company->GetBranchById($branchId);
                        $foreignStudent[$i]["genzai_branch_name"] = $branchInfo[0]["name"];
                    }else{
                        $foreignStudent[$i]["genzai_branch_name"] = "";
                    }
                    
                    $deptId   = $foreignStudent[$i]['dept_id'];
                    if(!empty($deptId)){
                        $deptInfo = $company->GetDeptById($deptId);
                        if(count($deptInfo)>0){
                            $foreignStudent[$i]["genzai_dept_name"] = $deptInfo[0]["name"];
                            
                        }else{
                            $foreignStudent[$i]["genzai_dept_name"] = "";
                            
                        }
                    }
                    $foreignStudentInfo = $personal->GetStudentInfoByUserId($id);
                    if(count($foreignStudentInfo) >0){
                        
                        $studentHakenCompanyId = $foreignStudentInfo[0]['s_haken_company_id'];
                        if(!empty($studentHakenCompanyId)){
                            $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                            if(count($studentHakenCompanyData) >0){
                                $foreignStudent[$i]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                          
                            }else{
                                $foreignStudent[$i]["s_haken_company_name"] = "";
                            }
                        }
                        
                        
                        $studentBranchId = $foreignStudentInfo[0]['s_branch_id'];
                        if(!empty($studentBranchId)){
                            $studentBranchInfo = $company->GetBranchById($studentBranchId);
                            $foreignStudent[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                        }else{
                            $foreignStudent[$i]["s_branch_name"] = "";
                        }
                        
                        $studentDeptId   = $foreignStudentInfo[0]['s_dept_id'];
                        if(!empty($studentDeptId)){
                            $studentDeptInfo = $company->GetDeptById($studentDeptId);
                            if(count($studentDeptInfo)>0){
                                $foreignStudent[$i]["s_dept_name"] = $studentDeptInfo[0]["name"];
                                
                            }else{
                                $foreignStudent[$i]["s_dept_name"] = "";
                                
                            }
                        }
                        
                        $foreignStudent[$i]['genzai_field'] = $foreignStudentInfo[0]['genzai_field'];
                        $foreignStudent[$i]['genzai_skill'] = $foreignStudentInfo[0]['genzai_skill'];
                        $foreignStudent[$i]['genzai_type'] = $foreignStudentInfo[0]['genzai_type'];
                        $foreignStudent[$i]['s_field'] = $foreignStudentInfo[0]['s_field'];
                        $foreignStudent[$i]['s_skill'] = $foreignStudentInfo[0]['s_skill'];
                        $foreignStudent[$i]['s_type'] = $foreignStudentInfo[0]['s_type'];
                        $foreignStudent[$i]['s_code'] = $foreignStudentInfo[0]['s_code'];
                        $foreignStudent[$i]['startDate'] = $foreignStudentInfo[0]['startDate'];
                        $foreignStudent[$i]['endDate'] = $foreignStudentInfo[0]['endDate'];
                        $foreignStudent[$i]['puchi1'] = $foreignStudentInfo[0]['puchi1'];
                        $foreignStudent[$i]['puchi2'] = $foreignStudentInfo[0]['puchi2'];
                        $foreignStudent[$i]['puchi3'] = $foreignStudentInfo[0]['puchi3'];
                        $foreignStudent[$i]['puchi4'] = $foreignStudentInfo[0]['puchi4'];
                        
                    }
                    
                    $genzaiHakenData = $personal->GetHakenCompanyByUserId($id);
                    if(count($genzaiHakenData)>0){
                        $hakenCompanyId = $genzaiHakenData[0]["company_id"];
                        $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                        if(count($hakenCompanyData) >0){
                            $foreignStudent[$i]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];

                        }else{
                            $foreignStudent[$i]["genzai_haken_company_name"] = "";
                        }
                    }else{
                        $foreignStudent[$i]["genzai_haken_company_name"] = "";
                    }    
                }
            }
            return array("listOfStudents"=>$foreignStudent);
        }else if($message == 'getAllStudentsByStartDateDesc'){
            // $allStudents = $student->GetAllStudents();
            $foreignStudent = $personal->GetAllForeignStudentsByStartDateDesc();
            if( count($foreignStudent) > 0){
                for($i=0; $i<count($foreignStudent) ;$i++){
                    $id = $foreignStudent[$i]['id'];
                    
                    $branchId = $foreignStudent[$i]['branch_id'];
                    if(!empty($branchId)){
                        $branchInfo = $company->GetBranchById($branchId);
                        $foreignStudent[$i]["genzai_branch_name"] = $branchInfo[0]["name"];
                    }else{
                        $foreignStudent[$i]["genzai_branch_name"] = "";
                    }
                    
                    $deptId   = $foreignStudent[$i]['dept_id'];
                    if(!empty($deptId)){
                        $deptInfo = $company->GetDeptById($deptId);
                        if(count($deptInfo)>0){
                            $foreignStudent[$i]["genzai_dept_name"] = $deptInfo[0]["name"];
                            
                        }else{
                            $foreignStudent[$i]["genzai_dept_name"] = "";
                            
                        }
                    }
                    $foreignStudentInfo = $personal->GetStudentInfoByUserId($id);
                    if(count($foreignStudentInfo) >0){
                        
                        $studentHakenCompanyId = $foreignStudentInfo[0]['s_haken_company_id'];
                        if(!empty($studentHakenCompanyId)){
                            $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                            if(count($studentHakenCompanyData) >0){
                                $foreignStudent[$i]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                          
                            }else{
                                $foreignStudent[$i]["s_haken_company_name"] = "";
                            }
                        }
                        
                        $studentBranchId = $foreignStudentInfo[0]['s_branch_id'];
                        if(!empty($studentBranchId)){
                            $studentBranchInfo = $company->GetBranchById($studentBranchId);
                            $foreignStudent[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                        }else{
                            $foreignStudent[$i]["s_branch_name"] = "";
                        }
                        
                        $studentDeptId   = $foreignStudentInfo[0]['s_dept_id'];
                        if(!empty($studentDeptId)){
                            $studentDeptInfo = $company->GetDeptById($studentDeptId);
                            if(count($studentDeptInfo)>0){
                                $foreignStudent[$i]["s_dept_name"] = $studentDeptInfo[0]["name"];
                                
                            }else{
                                $foreignStudent[$i]["s_dept_name"] = "";
                                
                            }
                        }
                        
                        $foreignStudent[$i]['genzai_field'] = $foreignStudentInfo[0]['genzai_field'];
                        $foreignStudent[$i]['genzai_skill'] = $foreignStudentInfo[0]['genzai_skill'];
                        $foreignStudent[$i]['genzai_type'] = $foreignStudentInfo[0]['genzai_type'];
                        $foreignStudent[$i]['s_field'] = $foreignStudentInfo[0]['s_field'];
                        $foreignStudent[$i]['s_skill'] = $foreignStudentInfo[0]['s_skill'];
                        $foreignStudent[$i]['s_type'] = $foreignStudentInfo[0]['s_type'];
                        $foreignStudent[$i]['s_code'] = $foreignStudentInfo[0]['s_code'];
                        $foreignStudent[$i]['startDate'] = $foreignStudentInfo[0]['startDate'];
                        $foreignStudent[$i]['endDate'] = $foreignStudentInfo[0]['endDate'];
                        $foreignStudent[$i]['puchi1'] = $foreignStudentInfo[0]['puchi1'];
                        $foreignStudent[$i]['puchi2'] = $foreignStudentInfo[0]['puchi2'];
                        $foreignStudent[$i]['puchi3'] = $foreignStudentInfo[0]['puchi3'];
                        $foreignStudent[$i]['puchi4'] = $foreignStudentInfo[0]['puchi4'];
                        
                    }
                    
                    $genzaiHakenData = $personal->GetHakenCompanyByUserId($id);
                    if(count($genzaiHakenData)>0){
                        $hakenCompanyId = $genzaiHakenData[0]["company_id"];
                        $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                        if(count($hakenCompanyData) >0){
                            $foreignStudent[$i]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];

                        }else{
                            $foreignStudent[$i]["genzai_haken_company_name"] = "";
                        }
                    }else{
                        $foreignStudent[$i]["genzai_haken_company_name"] = "";
                    }    
                }
            }
            return array("listOfStudents"=>$foreignStudent);
        }
        else if($message == 'getAllStudentsDuration'){ 
            $allStudentsDuration = $student->GetAllStudentsDuration();
            return array("listOfStudentsDuration"=>$allStudentsDuration);
        }else if($message == 'getAllStudentsDurationById'){
            $id = $request->get('id');
            $allStudentsDurationById = $student->GetStudentById($id);
            return array("listOfStudentsDurationById"=>$allStudentsDurationById);
        }else if($message == 'getAllStudentsDescByStartDate'){
            $allStudentsDescByStartDate = $student->GetAllStudentsByStartDateDesc();
            return array("listOfStudentsSortedByDate"=>$allStudentsDescByStartDate);
        }else if($message == 'getFinishedStudents'){
            // $finishedStudents = $student->GetFinishedStudents();
            $finishedStudentList = array();
            $foreignStudent = $personal->GetAllForeignStudents();
            if(count($foreignStudent) > 0){
                for($i=0; $i<count($foreignStudent) ;$i++){
                    $personalId = $foreignStudent[$i]['id'];
                    $finishedStudent = $student->GetFinishedStudents($personalId);
                    if(count($finishedStudent)){
                        ////////////////////////
                        
                        $branchId = $foreignStudent[$i]['branch_id'];
                        if(!empty($branchId)){
                            $branchInfo = $company->GetBranchById($branchId);
                            $foreignStudent[$i]["genzai_branch_name"] = $branchInfo[0]["name"];
                        }else{
                            $foreignStudent[$i]["genzai_branch_name"] = "";
                        }
                        $deptId   = $foreignStudent[$i]['dept_id'];
                        if(!empty($deptId)){
                            $deptInfo = $company->GetDeptById($deptId);
                            if(count($deptInfo)>0){
                                $foreignStudent[$i]["genzai_dept_name"] = $deptInfo[0]["name"];
                                
                            }else{
                                $foreignStudent[$i]["genzai_dept_name"] = "";
                                
                            }
                        }
                        $foreignStudentInfo = $personal->GetStudentInfoByUserId($personalId);
                        if(count($foreignStudentInfo) >0){
                            $studentHakenCompanyId = $foreignStudentInfo[0]['s_haken_company_id'];
                            if(!empty($studentHakenCompanyId)){
                                $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                                if(count($studentHakenCompanyData) >0){
                                    $foreignStudent[$i]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                              
                                }else{
                                    $foreignStudent[$i]["s_haken_company_name"] = "";
                                }
                            }
                        
                            $studentBranchId = $foreignStudentInfo[0]['s_branch_id'];
                            if(!empty($studentBranchId)){
                                $studentBranchInfo = $company->GetBranchById($studentBranchId);
                                $foreignStudent[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                            }else{
                                $foreignStudent[$i]["s_branch_name"] = "";
                            }
                            
                            $studentDeptId   = $foreignStudentInfo[0]['s_dept_id'];
                            if(!empty($studentDeptId)){
                                $studentDeptInfo = $company->GetDeptById($studentDeptId);
                                if(count($studentDeptInfo)>0){
                                    $foreignStudent[$i]["s_dept_name"] = $studentDeptInfo[0]["name"];
                                    $studentBranchInfo = $company->GetBranchById($studentBranchId);
                                    $foreignStudent[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                                }else{
                                    $foreignStudent[$i]["s_dept_name"] = "";
                                    
                                }
                            }
                        
                            $foreignStudent[$i]['genzai_field'] = $foreignStudentInfo[0]['genzai_field'];
                            $foreignStudent[$i]['genzai_skill'] = $foreignStudentInfo[0]['genzai_skill'];
                            $foreignStudent[$i]['genzai_type'] = $foreignStudentInfo[0]['genzai_type'];
                            $foreignStudent[$i]['s_field'] = $foreignStudentInfo[0]['s_field'];
                            $foreignStudent[$i]['s_skill'] = $foreignStudentInfo[0]['s_skill'];
                            $foreignStudent[$i]['s_type'] = $foreignStudentInfo[0]['s_type'];
                            $foreignStudent[$i]['s_code'] = $foreignStudentInfo[0]['s_code'];
                            $foreignStudent[$i]['startDate'] = $foreignStudentInfo[0]['startDate'];
                            $foreignStudent[$i]['endDate'] = $foreignStudentInfo[0]['endDate'];
                            $foreignStudent[$i]['puchi1'] = $foreignStudentInfo[0]['puchi1'];
                            $foreignStudent[$i]['puchi2'] = $foreignStudentInfo[0]['puchi2'];
                            $foreignStudent[$i]['puchi3'] = $foreignStudentInfo[0]['puchi3'];
                            $foreignStudent[$i]['puchi4'] = $foreignStudentInfo[0]['puchi4'];
                        
                        }
                    
                        $genzaiHakenData = $personal->GetHakenCompanyByUserId($personalId);
                        if(count($genzaiHakenData)>0){
                            $hakenCompanyId = $genzaiHakenData[0]["company_id"];
                            $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                            if(count($hakenCompanyData) >0){
                                $foreignStudent[$i]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];

                            }else{
                                $foreignStudent[$i]["genzai_haken_company_name"] = "";
                            }
                        }else{
                            $foreignStudent[$i]["genzai_haken_company_name"] = "";
                        }    
                        $finishedStudentList[] = $foreignStudent[$i];
                    }
                }     
            }
            return array("listOfFinishedStudents"=>$finishedStudentList);
        }else if($message == 'getNotFinishedStudents'){
            $notfinishedStudentList = array();
            $foreignStudent = $personal->GetAllForeignStudents();
            if(count($foreignStudent) > 0){
                for($i=0; $i<count($foreignStudent) ;$i++){
                    $personalId = $foreignStudent[$i]['id'];
                    $notFinishedStudent = $student->GetNotFinishedStudents($personalId);
                    if(count($notFinishedStudent)){
                        ////////////////////////
                        
                        $branchId = $foreignStudent[$i]['branch_id'];
                        if(!empty($branchId)){
                            $branchInfo = $company->GetBranchById($branchId);
                            $foreignStudent[$i]["genzai_branch_name"] = $branchInfo[0]["name"];
                        }else{
                             $foreignStudent[$i]["genzai_branch_name"] = "";
                        }
                        
                        $deptId   = $foreignStudent[$i]['dept_id'];
                        if(!empty($deptId)){
                            $deptInfo = $company->GetDeptById($deptId);
                            if(count($deptInfo)>0){
                                $foreignStudent[$i]["genzai_dept_name"] = $deptInfo[0]["name"];
                                
                            }else{
                                $foreignStudent[$i]["genzai_dept_name"] = "";
                               
                            }
                        }
                        $foreignStudentInfo = $personal->GetStudentInfoByUserId($personalId);
                        if(count($foreignStudentInfo) >0){
                            $studentHakenCompanyId = $foreignStudentInfo[0]['s_haken_company_id'];
                            if(!empty($studentHakenCompanyId)){
                                $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                                if(count($studentHakenCompanyData) >0){
                                    $foreignStudent[$i]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                              
                                }else{
                                    $foreignStudent[$i]["s_haken_company_name"] = "";
                                }
                            }
                        
                            $studentBranchId = $foreignStudentInfo[0]['s_branch_id'];
                            if(!empty($studentBranchId)){
                                $studentBranchInfo = $company->GetBranchById($studentBranchId);
                                $foreignStudent[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                            }else{
                                $foreignStudent[$i]["s_branch_name"] = "";
                            }
                            
                            $studentDeptId   = $foreignStudentInfo[0]['s_dept_id'];
                            if(!empty($studentDeptId)){
                                $studentDeptInfo = $company->GetDeptById($studentDeptId);
                                if(count($studentDeptInfo)>0){
                                    $foreignStudent[$i]["s_dept_name"] = $studentDeptInfo[0]["name"];
                                    
                                }else{
                                    $foreignStudent[$i]["s_dept_name"] = "";
                                    
                                }
                            }
                        
                            $foreignStudent[$i]['genzai_field'] = $foreignStudentInfo[0]['genzai_field'];
                            $foreignStudent[$i]['genzai_skill'] = $foreignStudentInfo[0]['genzai_skill'];
                            $foreignStudent[$i]['genzai_type'] = $foreignStudentInfo[0]['genzai_type'];
                            $foreignStudent[$i]['s_field'] = $foreignStudentInfo[0]['s_field'];
                            $foreignStudent[$i]['s_skill'] = $foreignStudentInfo[0]['s_skill'];
                            $foreignStudent[$i]['s_type'] = $foreignStudentInfo[0]['s_type'];
                            $foreignStudent[$i]['s_code'] = $foreignStudentInfo[0]['s_code'];
                            $foreignStudent[$i]['startDate'] = $foreignStudentInfo[0]['startDate'];
                            $foreignStudent[$i]['endDate'] = $foreignStudentInfo[0]['endDate'];
                            $foreignStudent[$i]['puchi1'] = $foreignStudentInfo[0]['puchi1'];
                            $foreignStudent[$i]['puchi2'] = $foreignStudentInfo[0]['puchi2'];
                            $foreignStudent[$i]['puchi3'] = $foreignStudentInfo[0]['puchi3'];
                            $foreignStudent[$i]['puchi4'] = $foreignStudentInfo[0]['puchi4'];
                        
                        }
                    
                        $genzaiHakenData = $personal->GetHakenCompanyByUserId($personalId);
                        if(count($genzaiHakenData)>0){
                            $hakenCompanyId = $genzaiHakenData[0]["company_id"];
                            $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                            if(count($hakenCompanyData) >0){
                                $foreignStudent[$i]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];

                            }else{
                                $foreignStudent[$i]["genzai_haken_company_name"] = "";
                            }
                        }else{
                            $foreignStudent[$i]["genzai_haken_company_name"] = "";
                        }    
                        $notfinishedStudentList[] = $foreignStudent[$i];
                    }
                }     
            }
            return array("listOfNotFinishedStudents"=>$notfinishedStudentList);
        }else if($message == 'getNotYetStudents'){
            // $notYetStudents = $student->GetNotYetStudents();
            $notYetStudentList = array();
            $foreignStudent = $personal->GetAllForeignStudents();
            if(count($foreignStudent) > 0){
                for($i=0; $i<count($foreignStudent) ;$i++){
                    $personalId = $foreignStudent[$i]['id'];
                    $notYetStudent = $student->GetNotYetStudents($personalId);
                    if(count($notYetStudent)){
                        ////////////////////////
                        
                        $branchId = $foreignStudent[$i]['branch_id'];
                        if(!empty($branchId)){
                            $branchInfo = $company->GetBranchById($branchId);
                            $foreignStudent[$i]["genzai_branch_name"] = $branchInfo[0]["name"];
                        }else{
                            $foreignStudent[$i]["genzai_branch_name"] = "";
                        }
                        
                        $deptId   = $foreignStudent[$i]['dept_id'];
                        if(!empty($deptId)){
                            $deptInfo = $company->GetDeptById($deptId);
                            if(count($deptInfo)>0){
                                $foreignStudent[$i]["genzai_dept_name"] = $deptInfo[0]["name"];
                                
                            }else{
                                $foreignStudent[$i]["genzai_dept_name"] = "";
                                
                            }
                        }
                        $foreignStudentInfo = $personal->GetStudentInfoByUserId($personalId);
                        if(count($foreignStudentInfo) >0){
                            $studentHakenCompanyId = $foreignStudentInfo[0]['s_haken_company_id'];
                            if(!empty($studentHakenCompanyId)){
                                $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                                if(count($studentHakenCompanyData) >0){
                                    $foreignStudent[$i]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                              
                                }else{
                                    $foreignStudent[$i]["s_haken_company_name"] = "";
                                }
                            }
                        
                            $studentBranchId = $foreignStudentInfo[0]['s_branch_id'];
                            if(!empty($studentBranchId)){
                                $studentBranchInfo = $company->GetBranchById($studentBranchId);
                                $foreignStudent[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                            }else{
                                $foreignStudent[$i]["s_branch_name"] = "";
                            }
                            
                            $studentDeptId   = $foreignStudentInfo[0]['s_dept_id'];
                            if(!empty($studentDeptId)){
                                $studentDeptInfo = $company->GetDeptById($studentDeptId);
                                if(count($studentDeptInfo)>0){
                                    $foreignStudent[$i]["s_dept_name"] = $studentDeptInfo[0]["name"];
                                    
                                }else{
                                    $foreignStudent[$i]["s_dept_name"] = "";
                                    
                                }
                            }
                        
                            $foreignStudent[$i]['genzai_field'] = $foreignStudentInfo[0]['genzai_field'];
                            $foreignStudent[$i]['genzai_skill'] = $foreignStudentInfo[0]['genzai_skill'];
                            $foreignStudent[$i]['genzai_type'] = $foreignStudentInfo[0]['genzai_type'];
                            $foreignStudent[$i]['s_field'] = $foreignStudentInfo[0]['s_field'];
                            $foreignStudent[$i]['s_skill'] = $foreignStudentInfo[0]['s_skill'];
                            $foreignStudent[$i]['s_type'] = $foreignStudentInfo[0]['s_type'];
                            $foreignStudent[$i]['s_code'] = $foreignStudentInfo[0]['s_code'];
                            $foreignStudent[$i]['startDate'] = $foreignStudentInfo[0]['startDate'];
                            $foreignStudent[$i]['endDate'] = $foreignStudentInfo[0]['endDate'];
                            $foreignStudent[$i]['puchi1'] = $foreignStudentInfo[0]['puchi1'];
                            $foreignStudent[$i]['puchi2'] = $foreignStudentInfo[0]['puchi2'];
                            $foreignStudent[$i]['puchi3'] = $foreignStudentInfo[0]['puchi3'];
                            $foreignStudent[$i]['puchi4'] = $foreignStudentInfo[0]['puchi4'];
                        
                        }
                    
                        $genzaiHakenData = $personal->GetHakenCompanyByUserId($personalId);
                        if(count($genzaiHakenData)>0){
                            $hakenCompanyId = $genzaiHakenData[0]["company_id"];
                            $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                            if(count($hakenCompanyData) >0){
                                $foreignStudent[$i]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];

                            }else{
                                $foreignStudent[$i]["genzai_haken_company_name"] = "";
                            }
                        }else{
                            $foreignStudent[$i]["genzai_haken_company_name"] = "";
                        }    
                        $notYetStudentList[] = $foreignStudent[$i];
                    }
                }     
            }
            return array("listOfNotYetStudents"=>$notYetStudentList);
        }else if($message == 'getNotFinishedAndFinishedStudents'){
            // $notFinishedAndFinishedStudents = $student->GetFinishedAndNotFinishedStudents();
            
            $notFinishedAndFinishedStudentList = array();
            $foreignStudent = $personal->GetAllForeignStudents();
            if(count($foreignStudent) > 0){
                for($i=0; $i<count($foreignStudent) ;$i++){
                    $personalId = $foreignStudent[$i]['id'];
                    $notFinishedAndFinishedStudent = $student->GetFinishedAndNotFinishedStudents($personalId);
                    if(count($notFinishedAndFinishedStudent)){
                        ////////////////////////
                        
                        $branchId = $foreignStudent[$i]['branch_id'];
                        if(!empty($branchId)){
                            $branchInfo = $company->GetBranchById($branchId);
                            $foreignStudent[$i]["genzai_branch_name"] = $branchInfo[0]["name"];
                        }else{
                            $foreignStudent[$i]["genzai_branch_name"] = "";
                        }
                        
                        $deptId   = $foreignStudent[$i]['dept_id'];
                        if(!empty($deptId)){
                            $deptInfo = $company->GetDeptById($deptId);
                            if(count($deptInfo)>0){
                                $foreignStudent[$i]["genzai_dept_name"] = $deptInfo[0]["name"];
                                
                            }else{
                                $foreignStudent[$i]["genzai_dept_name"] = "";
                                
                            }
                        }
                        $foreignStudentInfo = $personal->GetStudentInfoByUserId($personalId);
                        if(count($foreignStudentInfo) >0){
                            $studentHakenCompanyId = $foreignStudentInfo[0]['s_haken_company_id'];
                            if(!empty($studentHakenCompanyId)){
                                $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                                if(count($studentHakenCompanyData) >0){
                                    $foreignStudent[$i]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                              
                                }else{
                                    $foreignStudent[$i]["s_haken_company_name"] = "";
                                }
                            }
                        
                            $studentBranchId = $foreignStudentInfo[0]['s_branch_id'];
                            if(!empty($studentBranchId)){
                                $studentBranchInfo = $company->GetBranchById($studentBranchId);
                                $foreignStudent[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                            }else{
                                $foreignStudent[$i]["s_branch_name"] = "";
                            }
                            
                            $studentDeptId   = $foreignStudentInfo[0]['s_dept_id'];
                            if(!empty($studentDeptId)){
                                $studentDeptInfo = $company->GetDeptById($studentDeptId);
                                if(count($studentDeptInfo)>0){
                                    $foreignStudent[$i]["s_dept_name"] = $studentDeptInfo[0]["name"];
                                    
                                }else{
                                    $foreignStudent[$i]["s_dept_name"] = "";
                                    
                                }
                            }
                        
                            $foreignStudent[$i]['genzai_field'] = $foreignStudentInfo[0]['genzai_field'];
                            $foreignStudent[$i]['genzai_skill'] = $foreignStudentInfo[0]['genzai_skill'];
                            $foreignStudent[$i]['genzai_type'] = $foreignStudentInfo[0]['genzai_type'];
                            $foreignStudent[$i]['s_field'] = $foreignStudentInfo[0]['s_field'];
                            $foreignStudent[$i]['s_skill'] = $foreignStudentInfo[0]['s_skill'];
                            $foreignStudent[$i]['s_type'] = $foreignStudentInfo[0]['s_type'];
                            $foreignStudent[$i]['s_code'] = $foreignStudentInfo[0]['s_code'];
                            $foreignStudent[$i]['startDate'] = $foreignStudentInfo[0]['startDate'];
                            $foreignStudent[$i]['endDate'] = $foreignStudentInfo[0]['endDate'];
                            $foreignStudent[$i]['puchi1'] = $foreignStudentInfo[0]['puchi1'];
                            $foreignStudent[$i]['puchi2'] = $foreignStudentInfo[0]['puchi2'];
                            $foreignStudent[$i]['puchi3'] = $foreignStudentInfo[0]['puchi3'];
                            $foreignStudent[$i]['puchi4'] = $foreignStudentInfo[0]['puchi4'];
                        
                        }
                    
                        $genzaiHakenData = $personal->GetHakenCompanyByUserId($personalId);
                        if(count($genzaiHakenData)>0){
                            $hakenCompanyId = $genzaiHakenData[0]["company_id"];
                            $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                            if(count($hakenCompanyData) >0){
                                $foreignStudent[$i]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];

                            }else{
                                $foreignStudent[$i]["genzai_haken_company_name"] = "";
                            }
                        }else{
                            $foreignStudent[$i]["genzai_haken_company_name"] = "";
                        }    
                        $notFinishedAndFinishedStudentList[] = $foreignStudent[$i];
                    }
                }     
            }
            return array("listOfNotFinishedAndFinishedStudents"=>$notFinishedAndFinishedStudentList);
        }else if($message == 'getNotFinishedAndNotYetStudents'){
            // $notFinishedAndNotYetStudents = $student->GetNotFinishedAndNotYetStudents();
            $notFinishedAndNotYetStudentList = array();
            $foreignStudent = $personal->GetAllForeignStudents();
            if(count($foreignStudent) > 0){
                for($i=0; $i<count($foreignStudent) ;$i++){
                    $personalId = $foreignStudent[$i]['id'];
                    $notFinishedAndNotYetStudent = $student->GetNotFinishedAndNotYetStudents($personalId);
                    if(count($notFinishedAndNotYetStudent)){
                        ////////////////////////
                        
                        $branchId = $foreignStudent[$i]['branch_id'];
                        if(!empty($branchId)){
                            $branchInfo = $company->GetBranchById($branchId);
                            $foreignStudent[$i]["genzai_branch_name"] = $branchInfo[0]["name"];
                        }else{
                            $foreignStudent[$i]["genzai_branch_name"] = "";
                        }
                        
                        $deptId   = $foreignStudent[$i]['dept_id'];
                        if(!empty($deptId)){
                            $deptInfo = $company->GetDeptById($deptId);
                            if(count($deptInfo)>0){
                                $foreignStudent[$i]["genzai_dept_name"] = $deptInfo[0]["name"];
                                
                            }else{
                                $foreignStudent[$i]["genzai_dept_name"] = "";
                                
                            }
                        }
                        $foreignStudentInfo = $personal->GetStudentInfoByUserId($personalId);
                        if(count($foreignStudentInfo) >0){
                            $studentHakenCompanyId = $foreignStudentInfo[0]['s_haken_company_id'];
                            if(!empty($studentHakenCompanyId)){
                                $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                                if(count($studentHakenCompanyData) >0){
                                    $foreignStudent[$i]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                              
                                }else{
                                    $foreignStudent[$i]["s_haken_company_name"] = "";
                                }
                            }
                        
                            $studentBranchId = $foreignStudentInfo[0]['s_branch_id'];
                            if(!empty($studentBranchId)){
                                $studentBranchInfo = $company->GetBranchById($studentBranchId);
                                $foreignStudent[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                            }else{
                                $foreignStudent[$i]["s_branch_name"] = "";
                            }
                            
                            $studentDeptId   = $foreignStudentInfo[0]['s_dept_id'];
                            if(!empty($studentDeptId)){
                                $studentDeptInfo = $company->GetDeptById($studentDeptId);
                                if(count($studentDeptInfo)>0){
                                    $foreignStudent[$i]["s_dept_name"] = $studentDeptInfo[0]["name"];
                                    
                                }else{
                                    $foreignStudent[$i]["s_dept_name"] = "";
                                    
                                }
                            }
                        
                            $foreignStudent[$i]['genzai_field'] = $foreignStudentInfo[0]['genzai_field'];
                            $foreignStudent[$i]['genzai_skill'] = $foreignStudentInfo[0]['genzai_skill'];
                            $foreignStudent[$i]['genzai_type'] = $foreignStudentInfo[0]['genzai_type'];
                            $foreignStudent[$i]['s_field'] = $foreignStudentInfo[0]['s_field'];
                            $foreignStudent[$i]['s_skill'] = $foreignStudentInfo[0]['s_skill'];
                            $foreignStudent[$i]['s_type'] = $foreignStudentInfo[0]['s_type'];
                            $foreignStudent[$i]['s_code'] = $foreignStudentInfo[0]['s_code'];
                            $foreignStudent[$i]['startDate'] = $foreignStudentInfo[0]['startDate'];
                            $foreignStudent[$i]['endDate'] = $foreignStudentInfo[0]['endDate'];
                            $foreignStudent[$i]['puchi1'] = $foreignStudentInfo[0]['puchi1'];
                            $foreignStudent[$i]['puchi2'] = $foreignStudentInfo[0]['puchi2'];
                            $foreignStudent[$i]['puchi3'] = $foreignStudentInfo[0]['puchi3'];
                            $foreignStudent[$i]['puchi4'] = $foreignStudentInfo[0]['puchi4'];
                        
                        }
                    
                        $genzaiHakenData = $personal->GetHakenCompanyByUserId($personalId);
                        if(count($genzaiHakenData)>0){
                            $hakenCompanyId = $genzaiHakenData[0]["company_id"];
                            $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                            if(count($hakenCompanyData) >0){
                                $foreignStudent[$i]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];

                            }else{
                                $foreignStudent[$i]["genzai_haken_company_name"] = "";
                            }
                        }else{
                            $foreignStudent[$i]["genzai_haken_company_name"] = "";
                        }    
                        $notFinishedAndNotYetStudentList[] = $foreignStudent[$i];
                    }
                }     
            }
            return array("listOfNotFinishedAndNotYetStudents"=>$notFinishedAndNotYetStudentList);
        }else if($message == 'getNotYetAndFinishedStudents'){
            // $notYetAndFinishedStudents = $student->GetNotYetAndFinishedStudents();
            $notYetAndFinishedStudentList = array();
            $foreignStudent = $personal->GetAllForeignStudents();
            if(count($foreignStudent) > 0){
                for($i=0; $i<count($foreignStudent) ;$i++){
                    $personalId = $foreignStudent[$i]['id'];
                    $notYetAndFinishedStudent = $student->GetNotYetAndFinishedStudents($personalId);
                    if(count($notYetAndFinishedStudent)){
                        ////////////////////////
                        
                        $branchId = $foreignStudent[$i]['branch_id'];
                        if(!empty($branchId)){
                            $branchInfo = $company->GetBranchById($branchId);
                            $foreignStudent[$i]["genzai_branch_name"] = $branchInfo[0]["name"];
                        }else{
                            $foreignStudent[$i]["genzai_branch_name"] = "";
                        }
                        
                        $deptId   = $foreignStudent[$i]['dept_id'];
                        if(!empty($deptId)){
                            $deptInfo = $company->GetDeptById($deptId);
                            if(count($deptInfo)>0){
                                $foreignStudent[$i]["genzai_dept_name"] = $deptInfo[0]["name"];
                                
                            }else{
                                $foreignStudent[$i]["genzai_dept_name"] = "";
                                
                            }
                        }
                        $foreignStudentInfo = $personal->GetStudentInfoByUserId($personalId);
                        if(count($foreignStudentInfo) >0){
                            $studentHakenCompanyId = $foreignStudentInfo[0]['s_haken_company_id'];
                            if(!empty($studentHakenCompanyId)){
                                $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                                if(count($studentHakenCompanyData) >0){
                                    $foreignStudent[$i]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                              
                                }else{
                                    $foreignStudent[$i]["s_haken_company_name"] = "";
                                }
                            }
                        
                            $studentBranchId = $foreignStudentInfo[0]['s_branch_id'];
                            if(!empty($studentBranchId)){
                                $studentBranchInfo = $company->GetBranchById($studentBranchId);
                                $foreignStudent[$i]["s_branch_name"] = $studentBranchInfo[0]["name"];
                            }else{
                                $foreignStudent[$i]["s_branch_name"] = "";
                            }
                            
                            $studentDeptId   = $foreignStudentInfo[0]['s_dept_id'];
                            if(!empty($studentDeptId)){
                                $studentDeptInfo = $company->GetDeptById($studentDeptId);
                                if(count($studentDeptInfo)>0){
                                    $foreignStudent[$i]["s_dept_name"] = $studentDeptInfo[0]["name"];
                                    
                                }else{
                                    $foreignStudent[$i]["s_dept_name"] = "";
                                    
                                }
                            }
                        
                            $foreignStudent[$i]['genzai_field'] = $foreignStudentInfo[0]['genzai_field'];
                            $foreignStudent[$i]['genzai_skill'] = $foreignStudentInfo[0]['genzai_skill'];
                            $foreignStudent[$i]['genzai_type'] = $foreignStudentInfo[0]['genzai_type'];
                            $foreignStudent[$i]['s_field'] = $foreignStudentInfo[0]['s_field'];
                            $foreignStudent[$i]['s_skill'] = $foreignStudentInfo[0]['s_skill'];
                            $foreignStudent[$i]['s_type'] = $foreignStudentInfo[0]['s_type'];
                            $foreignStudent[$i]['s_code'] = $foreignStudentInfo[0]['s_code'];
                            $foreignStudent[$i]['startDate'] = $foreignStudentInfo[0]['startDate'];
                            $foreignStudent[$i]['endDate'] = $foreignStudentInfo[0]['endDate'];
                            $foreignStudent[$i]['puchi1'] = $foreignStudentInfo[0]['puchi1'];
                            $foreignStudent[$i]['puchi2'] = $foreignStudentInfo[0]['puchi2'];
                            $foreignStudent[$i]['puchi3'] = $foreignStudentInfo[0]['puchi3'];
                            $foreignStudent[$i]['puchi4'] = $foreignStudentInfo[0]['puchi4'];
                        
                        }
                    
                        $genzaiHakenData = $personal->GetHakenCompanyByUserId($personalId);
                        if(count($genzaiHakenData)>0){
                            $hakenCompanyId = $genzaiHakenData[0]["company_id"];
                            $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                            if(count($hakenCompanyData) >0){
                                $foreignStudent[$i]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];

                            }else{
                                $foreignStudent[$i]["genzai_haken_company_name"] = "";
                            }
                        }else{
                            $foreignStudent[$i]["genzai_haken_company_name"] = "";
                        }    
                        $notYetAndFinishedStudentList[] = $foreignStudent[$i];
                    }
                }     
            }
            return array("listOfNotYetAndFinishedStudents"=>$notYetAndFinishedStudentList);
        }else if($message == 'getStudentById'){
            $id = $request->get('id');
            $personalInfo = $personal->GetPersonalById($id);
            if(count($personalInfo)>0){
                
                
                
                //*************大林組所属*******************//
                $branchId = $personalInfo[0]['branch_id'];
                if(!empty($branchId)){
                    $branchData = $company->GetBranchById($branchId);
                    $personalInfo[0]["branch_id"] = $branchData[0]["id"];
                    $personalInfo[0]["branch_name"] = $branchData[0]["name"];
                }else{
                    $personalInfo[0]["branch_id"] = "";
                    $personalInfo[0]["branch_name"] = "";
                }
                
                $deptId   = $personalInfo[0]['dept_name'];
                // if(!empty($deptId)){
                //     $deptInfo = $company->GetDeptById($deptId);
                //     if(count($deptInfo)>0){
                //         $personalInfo[0]['dept'] = $deptInfo[0]['name'];
                //     }else{
                //         $personalInfo[0]['dept'] = "";
                //     }
                // }else{
                //     $personalInfo[0]['dept'] = "";
                // }
                
                //****************派遣元所属******************//
                $personalId = $personalInfo[0]['id'];
                $hakenData = $personal->GetHakenCompanyByUserId($personalId);
                if(count($hakenData)>0){
                    $hakenCompanyId = $hakenData[0]["company_id"];
                    $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                    if(count($hakenCompanyData) >0){
                        $personalInfo[0]["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];
                        $HakenCompType = $company->GetCompanyTypeById($hakenCompanyData[0]["company_type_id"]);
                        $personalInfo[0]["genzai_haken_company_type_id"] = $HakenCompType[0]["id"];
                        $personalInfo[0]["genzai_haken_company_type_name"] = $HakenCompType[0]["name"];
                    }
                }else{
                    $personalInfo[0]["genzai_haken_company_name"] = "";
                    $personalInfo[0]["genzai_haken_company_type_id"] = "";
                    $personalInfo[0]["genzai_haken_company_type_name"] = "";
                }
                
                
                //****************留学生情報******************//
                $studentData = $personal->GetStudentInfoByUserId($personalId);
                if(count($studentData) > 0){
                    $personalInfo[0]["genzai_skill"] = $studentData[0]["genzai_skill"];
                    $personalInfo[0]["genzai_field"] = $studentData[0]["genzai_field"];
                    $personalInfo[0]["genzai_type"] = $studentData[0]["genzai_type"];
                    $personalInfo[0]["s_skill"] = $studentData[0]["s_skill"];
                    $personalInfo[0]["s_field"] = $studentData[0]["s_field"];
                    $personalInfo[0]["s_type"] = $studentData[0]["s_type"];
                    $personalInfo[0]["s_code"] = $studentData[0]["s_code"];
                    $personalInfo[0]["startDate"] = $studentData[0]["startDate"];
                    $personalInfo[0]["endDate"] = $studentData[0]["endDate"];
                    $personalInfo[0]["puchi1"] = $studentData[0]["puchi1"];
                    $personalInfo[0]["puchi2"] = $studentData[0]["puchi2"];
                    $personalInfo[0]["puchi3"] = $studentData[0]["puchi3"];
                    $personalInfo[0]["puchi4"] = $studentData[0]["puchi4"];
                    $personalInfo[0]["s_contract_type"] = $studentData[0]["contract_type"];
                    
                    
                    $studentHakenCompanyId = $studentData[0]["s_haken_company_id"];
                    if(!empty($studentHakenCompanyId)){
                        $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                        if(count($studentHakenCompanyData) >0){
                            $personalInfo[0]["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                            $personalInfo[0]["s_haken_company_type_id"] = $studentHakenCompanyData[0]['company_type_id'];
                            
                        }else{
                            $personalInfo[0]["s_haken_company_name"] = "";
                            $personalInfo[0]["s_haken_company_type_id"] = "";
                        }
                    }else{
                        $personalInfo[0]["s_haken_company_name"] = "";
                        $personalInfo[0]["s_haken_company_type_id"] = "";
                    }
                    
                    
                    $studentDeptId = $studentData[0]['s_dept_id'];
                    $studentBranchId = $studentData[0]['s_branch_id'];
                    
                    if(!empty($studentBranchId)){
                        $studentBranchData = $company->GetBranchById($studentBranchId);
                        $personalInfo[0]["s_branch_id"] = $studentBranchData[0]["id"];
                        $personalInfo[0]["s_branch_name"] = $studentBranchData[0]["name"];
                    }else{
                        $personalInfo[0]["s_branch_id"] = "";
                        $personalInfo[0]["s_branch_name"] = "";
                    }
                    
                    
                    if(!empty($studentDeptId)){
                        $studentDeptData = $company->GetDeptById($studentDeptId);
                        if(count($studentDeptData)>0){
                            $personalInfo[0]["s_dept_id"] = $studentDeptData[0]["id"];
                            $personalInfo[0]["s_dept_name"] = $studentDeptData[0]["name"];
                        }else{
                            $personalInfo[0]["s_dept_id"] = "";
                            $personalInfo[0]["s_dept_name"] = "";
                        }
                    }else{
                        $personalInfo[0]["s_dept_id"] = "";
                        $personalInfo[0]["s_dept_name"] = "";
                    }
                    
                    
                }else{
                    $personalInfo[0]["genzai_skill"] = "";
                    $personalInfo[0]["genzai_field"] = "";
                    $personalInfo[0]["genzai_type"] = "";
                    $personalInfo[0]["s_skill"] = "";
                    $personalInfo[0]["s_field"] = "";
                    $personalInfo[0]["s_type"] = "";
                    $personalInfo[0]["s_dept_id"] = "";
                    $personalInfo[0]["s_dept_name"] = "";
                    $personalInfo[0]["s_branch_id"] = "";
                    $personalInfo[0]["s_branch_name"] = "";
                    $personalInfo[0]["s_haken_company_name"] = "";
                    $personalInfo[0]["s_haken_company_type_id"] = "";
                    $personalInfo[0]["s_code"] = "";
                    $personalInfo[0]["startDate"] = "";
                    $personalInfo[0]["endDate"] = "";
                    $personalInfo[0]["puchi1"] = "";
                    $personalInfo[0]["puchi2"] = "";
                    $personalInfo[0]["puchi3"] = "";
                    $personalInfo[0]["puchi4"] = "";
                    $personalInfo[0]["s_contract_type"] = "";
                }
            }
            return array("studentById" => $personalInfo);
            // $studentById = $student->GetStudentById($id);
        }else if($message == 'getAllMise'){
            $listOfMise = $student->GetAllMise();
            return array("ListOfMise" => $listOfMise);
        }else if($message == 'getAllField'){
            $listOfField = $student->GetAllField();
            return array("ListOfField" => $listOfField);
        }else if($message == 'getAllSkill'){
            $listOfSkill = $student->GetAllSkill();
            return array("ListOfSkill" => $listOfSkill);
        }else if($message == 'getAllhakenplace'){
            $listOfhakenplace = $student->GetAllHakenPlace();
            return array("listOfhakenplace" => $listOfhakenplace);
        }else if($message == 'getAllObayashi'){
            $listOfObayashi = $student->GetAllObayashi();
            return array("listOfObayashi" => $listOfObayashi);
        }else if($message == 'getAllType'){
            $listOfType = $student->GetAllType();
            return array("listOfType" => $listOfType);
        }
        
    }
    
    public function InsertPage(){
        return view('foreignStudentsInsert');
    }
    
    //Get Data From View
    // public function InsertData(Request $request){
    //     $data = $request->input();
    //     try{
    //         $student = new ForeignStudentModel();
    //         $student->username = $data['username'];
    //         $student->s_place = $data['s-place'];
    //         $student->s_skill = $data['s-skill'];
    //         $student->s_field = $data['s-field'];
    //         $student->s_haken_department = $data['s-hakenplace'];
    //         $student->s_obayashi_department = $data['s-obayashi'];
    //         $student->s_code = $data['s-code'];
    //         $student->s_type = $data['s-type'];
            
    //         $student->genzai_place = $data['e-place'];
    //         $student->genzai_skill = $data['e-skill'];
    //         $student->genzai_field = $data['e-field'];
    //         $student->genzai_haken_department = $data['e-hakenplace'];
    //         $student->genzai_obayashi_department = $data['e-obayashi'];
    //         $student->genzai_code = $data['e-code'];
    //         $student->genzai_type = $data['e-type'];
    //         $student->s_startDate = $data['startDate'];
    //         $student->s_endDate = $data['endDate'];
    //         $student->pichi1 = $data['puchi1'];
    //         $student->pichi2 = $data['puchi2'];
    //         $student->pichi3 = $data['puchi3'];
    //         $student->pichi4 = $data['puchi4'];
    //         $student->s_afterfinish_department = null;
            
    //         $student->save();
    //         return redirect('/foreignStudents/insert')->with('status', '留学生情報入力完了しました!');
    //     }catch(Exception $e){
    //         return redirect('/foreignStudents/insert')->with('failed', 'エラーが発生しました!');
    //     }
        
    // }
    
    //Update Data Via Ajax
    public function SaveData(Request $request){
        $message = $request->get('message');
        $id = $request->get('id');
        $data =  $request->get('studentData');
        $student = new ForeignStudentModel(); 
        
       
        if($message == "updateStudentById"){
            $isUpdated = $student->UpdateStudentById($id, $data);
            //print_r($isUpdated);
            return array('isUpdated' => true);
        }else if($message == "insertStudent"){
            try{
                $student->username = $data['username'];
                $student->s_place = $data['s-place'];
                $student->s_skill = $data['s-skill'];
                $student->s_field = $data['s-field'];
                $student->s_haken_department = $data['s-hakenplace'];
                $student->s_obayashi_department = $data['s-obayashi'];
                $student->s_code = $data['s-code'];
                $student->s_type = $data['s-type'];
                
                $student->genzai_place = $data['e-place'];
                $student->genzai_skill = $data['e-skill'];
                $student->genzai_field = $data['e-field'];
                $student->genzai_haken_department = $data['e-hakenplace'];
                $student->genzai_obayashi_department = $data['e-obayashi'];
                $student->genzai_code = $data['e-code'];
                $student->genzai_type = $data['e-type'];
                $student->s_startDate = $data['startDate'];
                $student->s_endDate = $data['endDate'];
                $student->pichi1 = $data['puchi1'];
                $student->pichi2 = $data['puchi2'];
                $student->pichi3 = $data['puchi3'];
                $student->pichi4 = $data['puchi4'];
                $student->s_afterfinish_department = null;
                
                $student->save();
                return array("isInserted" => true);
            }catch(Exception $e){
                return array("isInserted" => false);
            }
        }
        
        else if($message == "insertForeignStudent"){
            $studentData = $request->get("studentData");
            $personal = new PersonalModel();
            $company  = new CompanyModel();
            $student = new ForeignStudentModel(); 
            echo "<pre>";
            print_r($studentData);
            
            
            //***********tb-personal [ dept-id, branch-id ]*****************//
            
            $branchId = $studentData["branch_id"];
            $dept = $studentData["dept"];
            if(!empty($dept) && !empty($branchId)){
                $checkExistingDept = $company->CheckExistingDept($branchId, $dept);
                if(count($checkExistingDept)>0){
                    $studentData["dept_id"] = $checkExistingDept[0]->id;
                }else{
                    $deptInsert = $company->InsertNewDepartment($dept,$branchId);
                    $newDeptInfo = $company->GetDeptIdByNameAndBranchId($dept,$branchId);
                    $studentData["dept_id"] = $newDeptInfo[0]->id;
                }
            }else{
                $studentData["dept_id"] ="";
            }
            
            
            //***********tb-students [ s-dept-id, s-branch-id ]*****************//
            $sBranchId = $studentData["s_branch_id"];
            $sdept  = $studentData["s_dept_name"];
            if(!empty($sdept) && !empty($sBranchId)){
                $checkExistingStudentDept = $company->CheckExistingDept($sBranchId, $sdept);
                if(count($checkExistingStudentDept)>0){
                    $studentData["s_dept_id"] = $checkExistingStudentDept[0]->id;
                }else{
                    $sdeptInsert = $company->InsertNewDepartment($sdept,$sBranchId);
                    $newStudentDeptInfo = $company->GetDeptIdByNameAndBranchId($sdept,$sBranchId);
                    $studentData["s_dept_id"] = $newStudentDeptInfo[0]->id;
                }
            }else{
                $studentData["s_dept_id"] = "";
            }
            
            //************** tb-haken [ company-id ] *****************//
            $hakenCompanyName = $studentData["genzai_haken_company_name"];
            $hakenCompanyTypeId = $studentData["genzai_haken_company_type_id"];
            if($hakenCompanyTypeId == 1){
                $studentData['contractType'] = 1;
            }else{
                if(!empty($hakenCompanyName)){
                    $checkExistingCompany = $company->CheckExistingCompany($hakenCompanyName);
                    if(count($checkExistingCompany)>0){
                        $studentData["genzai_haken_company_id"] = $checkExistingCompany[0]->id;
                    }else{
                        $companyInsert = $company->InsertNewCompany($hakenCompanyName,$hakenCompanyTypeId);
                        $newCompanyInfo = $company->GetCompanyIdFromCompany($hakenCompanyName,$hakenCompanyTypeId);
                        $studentData["genzai_haken_company_id"] = $newCompanyInfo[0]->id;
                    }
                }
            }
            
            if($hakenCompanyTypeId == 10){
                $studentData['contractType'] = 2;
            }
            
            //***********tb-students [ s_haken_company_id ]*****************//
            $studentHakenCompanyName = $studentData["s_haken_company_name"];
            $studentHakenCompanyTypeId = $studentData["s_haken_company_type_id"];
            if($studentHakenCompanyTypeId == 1){
                $studentData["s_haken_company_id"] = "";
            }else{
                if(!empty($studentHakenCompanyName)){
                    $checkExistingStudentHakenCompany = $company->CheckExistingCompany($studentHakenCompanyName);
                    if(count($checkExistingStudentHakenCompany)>0){
                        $studentData["s_haken_company_id"] = $checkExistingStudentHakenCompany[0]->id;
                    }else{
                        $companyInsert2 = $company->InsertNewCompany($studentHakenCompanyName,$studentHakenCompanyTypeId);
                        $newCompanyInfo2 = $company->GetCompanyIdFromCompany($studentHakenCompanyName,$studentHakenCompanyTypeId);
                        $studentData["s_haken_company_id"] = $newCompanyInfo2[0]->id;
                    }
                }else{
                    $studentData["s_haken_company_id"] = "";
                }
            }
            
            if($studentHakenCompanyTypeId == 10){
                $studentData['studentContractType'] = 2;
            }
            
            
            echo "<pre>";
            print_r($studentData);
            
            
            //********Insert or Update to tb-personal*************//
            $personalInsert = $personal->InsertPersonalByStudyAbraod($studentData);
            $personalInfo = $personal->GetPersonalByMail($studentData["email"]);
            $personalId = $personalInfo[0]["id"];
            print_r($personalId);
            
            //**********Insert tb-student*************//
            $studentInsert = $student->InsertStudent($studentData,$personalId);
            
            //***********Insert tb-haken*************//
            if(!empty($hakenCompanyName)){
                print_r($studentData["genzai_haken_company_id"]);
                print_r($personalId);
                
                $hakenCompanyInsert = $personal->InsertPersonalSubHaken($personalId,$studentData["genzai_haken_company_id"]);
            }
            
            
            return "success";
            
        }
        
        else if($message == "updateForeignStudent"){
            $studentData = $request->get("studentData");
            $personalId = $request->get("userId");
            $personal = new PersonalModel();
            $company  = new CompanyModel();
            $student = new ForeignStudentModel(); 
            // echo "<pre>";
            // print_r($studentData);
            //***********tb-students [ s-dept-id, s-branch-id ]*****************//
            $sBranchId = $studentData["s_branch_id"];
            $sdept  = $studentData["s_dept_name"];
            if(!empty($sdept)){
                $checkExistingStudentDept = $company->CheckExistingDept($sBranchId, $sdept);
                if(count($checkExistingStudentDept)>0){
                    $studentData["s_dept_id"] = $checkExistingStudentDept[0]->id;
                }else{
                    $sdeptInsert = $company->InsertNewDepartment($sdept,$sBranchId);
                    $newStudentDeptInfo = $company->GetDeptIdByNameAndBranchId($sdept,$sBranchId);
                    $studentData["s_dept_id"] = $newStudentDeptInfo[0]->id;
                }
            }else{
                $studentData["s_dept_id"] = "";
            }
            
            $studentHakenCompanyName = $studentData["s_haken_company_name"];
            $studentHakenCompanyTypeId = $studentData["s_haken_company_type_id"];
            if(!empty($studentHakenCompanyName)){
                $checkExistingStudentHakenCompany = $company->CheckExistingCompany($studentHakenCompanyName);
                if(count($checkExistingStudentHakenCompany)>0){
                    $studentData["s_haken_company_id"] = $checkExistingStudentHakenCompany[0]->id;
                }else{
                    $companyInsert2 = $company->InsertNewCompany($studentHakenCompanyName,$studentHakenCompanyTypeId);
                    $newCompanyInfo2 = $company->GetCompanyIdFromCompany($studentHakenCompanyName,$studentHakenCompanyTypeId);
                    $studentData["s_haken_company_id"] = $newCompanyInfo2[0]->id;
                }
            }else{
                 $studentData["s_haken_company_id"] = "";
            }
            
            if($studentHakenCompanyTypeId == 10){
                $studentData['studentContractType'] = 2;
            }
            
            //**********Insert tb-student*************//
            $studentUpdate = $student->InsertStudent($studentData,$personalId);
            return "success";
        }
    }
    
    //Delete data via Ajax
    public function DeleteData(Request $request){
        $message = $request->get('message');
        $id = $request->get('userId');
        $student = new ForeignStudentModel();
        $personal = new PersonalModel();
        if($message == "deleteStudentById"){
            $isDeleted = $student->DeleteStudentById($id);
            $updatePersonal = $personal->DeleteForeignStudentFromPersonal($id);
            return array("isDeleted" => $isDeleted);
        }
    }
    
    //Update Show or Hide Column View For Compare Page
    public function UpdateData(Request $request){
        $message = $request->get("message");
        $loginUser = $request->get("loginUser");
        $columnName = $request->get("columnName");
        $foreignStudent = new ForeignStudentModel();
        if($message == "hideDisplay"){
            $update  = $foreignStudent->HideTableColumnDisplay($columnName,$loginUser);
            return array("DisplayOrNot" =>$update);
        }elseif($message == "showDisplay"){
            $update  = $foreignStudent->ShowTableColumnDisplay($columnName,$loginUser);
            return array("DisplayOrNot" =>$update);
        }
    }
}
