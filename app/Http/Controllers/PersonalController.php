<?php

namespace App\Http\Controllers;
use App\Models\LoginModel;
use App\Models\ForgeModel;
use App\Models\AllStoreModel;
use App\Models\ForeignStudentModel;
use App\Models\PartnerCompanyModel;
use App\Models\PartnerCompanyContactModel;
use App\Models\PersonalModel;
use App\Models\CompanyModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Artisan;
class PersonalController extends Controller
{
    function index()
    {
        return view('personal');
    }

    function GetData(Request $request)
    {
        $message = $request->get('message');
        
        if($message == "allStore"){
            $allstore = new AllStoreModel();
            $allstoreData = $allstore->GetAllStore();
            return $allstoreData;
        }else if($message == "AllPersonalData"){
            $personal = new PersonalModel();
            $personalData = $personal->GetAllPersonalData();
            return $personalData;
        }else if($message == "personalById"){
            $id = $request->get('id');
            $personal = new PersonalModel();
            $personalData = $personal->GetPersonalById($id);
            
            return $personalData;
        }else{
            $login = new LoginModel();
            $foreign = new foreignStudentModel();
            $partner = new PartnerCompanyModel();
            $partnerCont = new PartnerCompanyContactModel();
            
            $loginData = $login->GetAllUser();
            $userCodeData = $login->GetAllUserCode();
            $studentsData = $foreign->GetAllStudents();
            $partnerComp = $partner->GetAllPartnerCompany();
            $partnerCompCont = $partnerCont->GetAllPartnerCompanyContact();
            
            return array(   "login"=>$loginData,
                            "userCode"=>$userCodeData,
                            "foreign"=>$studentsData,
                            "partnerComp"=>$partnerComp,
                            "partnerCompCont"=>$partnerCompCont);
        }
    }
    
    function GetUser(Request $request)
    {
        $message = $request->get('message');
        
        if($message == "getUserByMail"){
            $mail = $request->get('mail');
            
            $personal = new PersonalModel();
            $company = new CompanyModel();
            $personalData = $personal->GetPersonalByMail($mail);
            if(count($personalData) > 0){
                $curData = $personalData[0];
                
                $companyData = $company->GetCompanyById($curData["company_id"]);
                if(count($companyData) > 0){
                    $curData["company_id"] = $companyData[0]["id"];
                    $curData["company_name"] = $companyData[0]["name"];
                    
                    $CompType = $company->GetCompanyTypeById($companyData[0]["company_type_id"]);
                    $curData["company_type_id"] = $CompType[0]["id"];
                    $curData["company_type_name"] = $CompType[0]["name"];
                }else{
                    $curData["company_id"] = "";
                    $curData["company_name"] = "";
                    $curData["company_type_id"] = "";
                    $curData["company_type_name"] = "";
                }
                
                $branchId = $curData['branch_id'];
                if(!empty($branchId)){
                    $branchData = $company->GetBranchById($branchId);
                    $curData["branch_id"]  = $branchData[0]['id'];
                    $curData["branch_name"] = $branchData[0]['name'];
                }else{
                    $curData["branch_id"]  = 0;
                    $curData["branch_name"] = "";
                }
                
                $deptId = $curData['dept_id'];
                if(!empty($deptId)){
                    $deptData = $company->GetDeptById($deptId);
                    if(count($deptData)>0){
                        $curData['dept_id'] = $deptData[0]['id'];
                        $curData['dept_name'] = $deptData[0]['name'];
                    }else{
                        $curData['dept_id'] = "";
                        $curData['dept_name'] = "";
                    }
                }else{
                    $curData['dept_id'] = "";
                    $curData['dept_name'] = "";
                }

               $personalData[0]  = $curData;
            }
            return $personalData;
        }else if($message == "getStudentByMail"){
            $mail = $request->get('mail');
            $personal = new PersonalModel();
            $company = new CompanyModel();
            $personalData = $personal->GetPersonalByMail($mail);
            if(count($personalData) > 0){
                $foreignStudent = $personalData[0];
                $personalId = $personalData[0]["id"];
                
                //******************現在[ Start ]***********************************//
                
                //会社名と企業タイプ取得
                $companyData = $company->GetCompanyById($foreignStudent["company_id"]);
                if(count($companyData) > 0){
                    $foreignStudent["genzai_company_id"] = $companyData[0]["id"];
                    $foreignStudent["genzai_company_name"] = $companyData[0]["name"];
                    
                    $CompType = $company->GetCompanyTypeById($companyData[0]["company_type_id"]);
                    $foreignStudent["genzai_company_type_id"] = $CompType[0]["id"];
                    $foreignStudent["genzai_company_type_name"] = $CompType[0]["name"];
                }else{
                    $foreignStudent["genzai_company_id"] = "";
                    $foreignStudent["genzai_company_name"] = "";
                    $foreignStudent["genzai_company_type_id"] = "";
                    $foreignStudent["genzai_company_type_name"] = "";
                }
                
                //所属と支店取得
                $deptId = $foreignStudent["dept_id"];
                if(!empty($deptId)){
                   $deptData = $company->GetDeptById($deptId);
                    if(count($deptData)>0){
                        $foreignStudent["genzai_dept_id"] = $deptData[0]["id"];
                        $foreignStudent["genzai_dept_name"] = $deptData[0]["name"];
                        
                        $branchData = $company->GetBranchById($deptData[0]["branch_id"]);
                        $foreignStudent["genzai_branch_id"] = $branchData[0]["id"];
                        $foreignStudent["genzai_branch_name"] = $branchData[0]["name"];
                    }else{
                        
                    } 
                }else{
                    $foreignStudent["genzai_dept_id"] = "";
                    $foreignStudent["genzai_dept_name"] = "";
                    $foreignStudent["genzai_branch_id"] =  $foreignStudent["branch_id"];
                    $foreignStudent["genzai_branch_name"] = "";
                }
                
                
                //派遣の場合
                $hakenData = $personal->GetHakenCompanyByUserId($personalId);
                if(count($hakenData)>0){
                    $hakenCompanyId = $hakenData[0]["company_id"];
                    $hakenCompanyData = $company->GetCompanyById($hakenCompanyId);
                    if(count($hakenCompanyData) >0){
                        $foreignStudent["genzai_haken_company_name"] = $hakenCompanyData[0]['name'];
                        $HakenCompType = $company->GetCompanyTypeById($hakenCompanyData[0]["company_type_id"]);
                        $foreignStudent["genzai_haken_company_type_id"] = $HakenCompType[0]["id"];
                        $foreignStudent["genzai_haken_company_type_name"] = $HakenCompType[0]["name"];
                    }
                }else{
                    $foreignStudent["genzai_haken_company_name"] = "";
                    $foreignStudent["genzai_haken_company_type_id"] = "";
                    $foreignStudent["genzai_haken_company_type_name"] = "";
                }
                
                //******************現在 [ End ]***********************************//
                
                //******************留学時 [ Start ]***********************************//
                $studentData = $personal->GetStudentInfoByUserId($personalId);
                if(count($studentData) > 0){
                    $foreignStudent["genzai_skill"] = $studentData[0]["genzai_skill"];
                    $foreignStudent["genzai_field"] = $studentData[0]["genzai_field"];
                    $foreignStudent["genzai_type"] = $studentData[0]["genzai_type"];
                    $foreignStudent["s_skill"] = $studentData[0]["s_skill"];
                    $foreignStudent["s_field"] = $studentData[0]["s_field"];
                    $foreignStudent["s_type"] = $studentData[0]["s_type"];
                    $foreignStudent["s_code"] = $studentData[0]["s_code"];
                    $foreignStudent["startDate"] = $studentData[0]["startDate"];
                    $foreignStudent["endDate"] = $studentData[0]["endDate"];
                    $foreignStudent["puchi1"] = $studentData[0]["puchi1"];
                    $foreignStudent["puchi2"] = $studentData[0]["puchi2"];
                    $foreignStudent["puchi3"] = $studentData[0]["puchi3"];
                    $foreignStudent["puchi4"] = $studentData[0]["puchi4"];
                    
                    $studentHakenCompanyId = $studentData[0]["s_haken_company_id"];
                    if(!empty($studentHakenCompanyId)){
                       $studentHakenCompanyData = $company->GetCompanyById($studentHakenCompanyId);
                        if(count($studentHakenCompanyData) >0){
                            $foreignStudent["s_haken_company_name"] = $studentHakenCompanyData[0]['name'];
                            $foreignStudent["s_haken_company_type_id"] = $studentHakenCompanyData[0]['company_type_id'];
                            
                        }else{
                            $foreignStudent["s_haken_company_name"] = "";
                            $foreignStudent["s_haken_company_type_id"] = "";
                        } 
                    }
                    
                    
                    $studentDeptId = $studentData[0]['s_dept_id'];
                    if(!empty($studentDeptId)){
                        $studentDeptData = $company->GetDeptById($studentDeptId);
                        if(count($studentDeptData)>0){
                            $foreignStudent["s_dept_id"] = $studentDeptData[0]["id"];
                            $foreignStudent["s_dept_name"] = $studentDeptData[0]["name"];
                            
                            $studentBranchData = $company->GetBranchById($studentDeptData[0]["branch_id"]);
                            $foreignStudent["s_branch_id"] = $studentBranchData[0]["id"];
                            $foreignStudent["s_branch_name"] = $studentBranchData[0]["name"];
                        }else{
                            $foreignStudent["s_dept_id"] = "";
                            $foreignStudent["s_dept_name"] = "";
                            $foreignStudent["s_branch_id"] = "";
                            $foreignStudent["s_branch_name"] = "";
                        }
                    }else{
                        $foreignStudent["s_dept_id"] = "";
                        $foreignStudent["s_dept_name"] = "";
                        $foreignStudent["s_branch_id"] = $studentData[0]['s_branch_id'];
                        $foreignStudent["s_branch_name"] = "";
                    }
                    
                    
                }else{
                    $foreignStudent["genzai_skill"] = "";
                    $foreignStudent["genzai_field"] = "";
                    $foreignStudent["genzai_type"] = "";
                    $foreignStudent["s_skill"] = "";
                    $foreignStudent["s_field"] = "";
                    $foreignStudent["s_type"] = "";
                    $foreignStudent["s_dept_id"] = "";
                    $foreignStudent["s_dept_name"] = "";
                    $foreignStudent["s_branch_id"] = "";
                    $foreignStudent["s_branch_name"] = "";
                    $foreignStudent["s_haken_company_name"] = "";
                    $foreignStudent["s_haken_company_type_id"] = "";
                    $foreignStudent["s_code"] = "";
                    $foreignStudent["startDate"] = "";
                    $foreignStudent["endDate"] = "";
                    $foreignStudent["puchi1"] = "";
                    $foreignStudent["puchi2"] = "";
                    $foreignStudent["puchi3"] = "";
                    $foreignStudent["puchi4"] = "";
                    
                    
                    
                }
                //******************留学時 [ End ]***********************************//
                $personalData[0] = $foreignStudent;
                
            }
            return $personalData;
        }
    }

}