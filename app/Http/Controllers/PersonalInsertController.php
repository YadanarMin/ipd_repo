<?php

namespace App\Http\Controllers;
use App\Models\LoginModel;
use App\Models\PersonalModel;
use App\Models\CompanyModel;
use App\Models\ApplicationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Artisan;
class PersonalInsertController extends Controller
{
    function index()
    {
        $personal = new PersonalModel();
        $company = new CompanyModel();
        $personnelData = $personal->GetAllPersonalData();
        $companyTypeList = $company->GetCompanyType();
        $companyList = $company->GetCompany();
        $deptList = $company->GetAllDept();
        $branchList = $company->GetAllBranch();

        foreach($personnelData as $key=>$personnel){
            $company_id = $personnel["company_id"] == null ? 0 : $personnel["company_id"];
            $data = $company->GetCompanyById($company_id);

            $personnelData[$key]["company_name"] = empty($data) ? "" : $data[0]["name"];
            
            //GET branch_id/branch_name(by branch_id)
            if($personnel["branch_id"] != null){
                $branchData = $company->GetBranchById($personnel["branch_id"]);
                $personnelData[$key]["branch_code"] = $branchData[0]["code"];
                $personnelData[$key]["branch_name"] = $branchData[0]["name"];
            }else{
                $personnelData[$key]["branch_code"] = "";
                $personnelData[$key]["branch_name"] = "";
            }
            
            //GET dept_code/dept_name(by dept_id)
            if($personnel["dept_id"] != null){
                $deptData = $company->GetDeptById($personnel["dept_id"]);
                if(empty($deptData)){
                    if($personnelData[$key]["branch_code"] == ""){
                        $personnelData[$key]["dept_code"] = "";
                    }else{
                        $personnelData[$key]["dept_code"] = "*****";
                    }
                    $personnelData[$key]["dept_name"] = "";

                }else{
                    if($personnelData[$key]["branch_code"] == ""){
                       $personnelData[$key]["branch_code"] = "**"; 
                    }
                    $personnelData[$key]["dept_code"] = $deptData[0]["code"];
                    $personnelData[$key]["dept_name"] = $deptData[0]["name"];
                }
            }else{
                if($personnelData[$key]["branch_code"] == ""){
                    $personnelData[$key]["dept_code"] = "";
                }else{
                    $personnelData[$key]["dept_code"] = "*****";
                }
                $personnelData[$key]["dept_name"] = "";
            }
            
            //GET haken_company_id(by personal_id)
            $hakenData = $personal->GetHakenCompanyByUserId($personnel["id"]);
            if(empty($hakenData)){
                $personnelData[$key]["haken_company_id"] = "";
                $personnelData[$key]["haken_company_name"] = "";
                $personnelData[$key]["haken_company_type_id"] = 0;
                $personnelData[$key]["haken_company_type_name"] = "";
            }else{
                $personnelData[$key]["haken_company_id"] = $hakenData[0]["company_id"];
                //GET haken_company_name(by haken_company_id)
                $companyData = $company->GetCompanyById($hakenData[0]["company_id"]);
                if(empty($companyData)){
                    $personnelData[$key]["haken_company_name"] = "";
                    $personnelData[$key]["haken_company_type_id"] = 0;
                    $personnelData[$key]["haken_company_type_name"] = "";
                }else{
                    $personnelData[$key]["haken_company_name"] = $companyData[0]["name"];
                    //GET haken_company_type(by company_type_id)
                    $companyTypeData = $company->GetCompanyTypeById($companyData[0]["company_type_id"]);
                    $personnelData[$key]["haken_company_type_id"] = $companyTypeData[0]["id"];
                    $personnelData[$key]["haken_company_type_name"] = $companyTypeData[0]["name"];
                }
            }
        }
        
        return view('personnelInsert')->with(
            [   "personnels"=>$personnelData,
                "companyTypeList"=>$companyTypeList,
                "companyList"=>$companyList,
                "deptList"=>$deptList,
                "branchList"=>$branchList
            ]);
    }
    
    public function SaveData(Request $request)
    {
        $message = $request->get('message');
        
        if($message == 'onePerson'){
            $personal_data = $request->get('personalData');

            $personal = new PersonalModel();
            $company = new CompanyModel();

            try{
                //INSERT tb_dept(if dept_name not exist in tb_dept->name)
                $chkResult = $company->CheckExistingDeptNotLike($personal_data['branch_id'], $personal_data['dept_name']);
                if(empty($chkResult)){
                    $deptResult = $company->InsertNewDepartment($personal_data['dept_name'],$personal_data['branch_id']);
                    $latestInsertDeptData = $company->CheckExistingDeptNotLike($personal_data['branch_id'], $personal_data['dept_name']);
                    $personal_data["dept_id"] = $latestInsertDeptData[0]["id"];
                }else{
                    $deptResult = "success";
                }
    
                //INSERT tb_personal
                $result = $personal->InsertPersonal($personal_data);
                
                //INSERT tb_haken
                $personalData = $personal->GetPersonalByMail($personal_data['mail']);
                if(!empty($personalData)){
                    $hakenResult = $personal->InsertPersonalSubHaken($personalData[0]['id'],$personal_data['haken_company_id']);
                }else{
                    $hakenResult = "success";
                }
                
                return "success";
            }catch(Exception $e){
                return $e;
            }

        }else{
            return "no message";
        }
    }

    public function DeleteData(Request $request)
    {
        $personalId = $request->get('personalId');
        $login = new LoginModel();
        $personal = new PersonalModel();
        $applicants = new ApplicationModel();

        try{
            $retInfo = $personal->DeletePersonalById($personalId);
            $retLogin = $login->DeleteLoginById($personalId);
            $retBimCourse = $applicants->DeleteBimCourseInfoByUserId($personalId);
            $retHaken = $personal->DeleteHakenByUserId($personalId);
            $retStudent = $personal->DeleteForeignStudentByUserId($personalId);
            
            return "success";
        }catch(Exception $e){
            return $e;
        }
    }


}