<?php

namespace App\Http\Controllers;
use App\Http\Controllers\AllstoreController;
use App\Models\CommonModel;
use App\Models\LoginModel;
use App\Models\ForgeModel;
use App\Models\CompanyModel;
use App\Models\PersonalModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Excel;
use App\Imports\ExcelDataImport;
use Exception;
use GuzzleHttp\Client;

class CompanyController extends Controller
{
    function index()
    {
        $company = new CompanyModel();
        $companyList = $company->GetCompany();
        $branchList = $company->GetAllBranch();
        
        foreach($companyList as $key=>$curCompany){
            $data = $company->GetCompanyTypeById($curCompany["company_type_id"]);
            $companyList[$key]["company_type_name"] = empty($data) ? "" :  $data[0]["name"];
        }
        
        return view('company')->with(["companyList"=>$companyList, "branchList"=>$branchList]);
    }
    
    public function SaveCompanyType(Request $request)
    {
        $company_type_name = $request->get('typeName');
        $company = new CompanyModel();
        $companyType = $company->SaveCompanyType($company_type_name);
        return $companyType;
    }

    public function GetCompanyType(Request $request)
    {
        $message = $request->get('message');
        
        if($message == 'getAllCompanyType'){
            $company = new CompanyModel();
            $companyType = $company->GetCompanyType();
            return $companyType;
        }else if($message == 'getCompanyTypeById'){
            $company_type_id = $request->get('typeId');
            $company = new CompanyModel();
            $companyInfo = $company->GetCompanyTypeById($company_type_id);
            return $companyInfo;
        }else if($message == 'getCompanyTypeByName'){
            $company_type_name = $request->get('typeName');
            $company = new CompanyModel();
            $companyInfo = $company->GetCompanyTypeByName($company_type_name);
            return $companyInfo;
        }else{
            return [];
        }
    }
    
    public function GetCompanyBranch(Request $request){
        $message = $request->get('message');
        $company = new CompanyModel();
        if($message == 'getAllBranch'){
            $branchList = $company->GetAllBranch();
            return $branchList;
        }
    }
    
    public function SaveData(Request $request)
    {
        $message = $request->get('message');
        
        if($message == 'singleCompany'){
            $tmp_data = $request->get('companyData');
            
            $company_data = array();
            foreach($tmp_data as $data){
                $key = $data['name'];//input textbox name
                $value = $data['value'];//input value
                $company_data[$key] = $value;
            }
            
            $company = new CompanyModel();
            $companyResult = $company->SaveCompany($company_data);
            $branchResult = "";
            if($companyResult == "success"){
                $companyData = $company->GetCompanyByName($company_data["txtName"]);
                if(!empty($companyData)){
                    $branchResult = $company->SaveBranchOffice($companyData[0]["id"], $company_data);
                }
            }

            return $branchResult;
        }else if($message == 'multiCompany'){
            $companyList = $request->get('companyList');
            $company = new CompanyModel();
            $companyInfo = $company->SaveCompanyList($companyList);
            
            return $companyInfo;
        }else{
            return "no message";
        }
    }
    
    public function GetData(Request $request)
    {
        $message = $request->get('message');
        
        if($message == 'getAllCompany'){
            $company = new CompanyModel();
            $companyList = $company->GetCompany();
            return $companyList;
        }else if($message == 'getCompanyById'){
            $company_id = $request->get('companyId');
            $company = new CompanyModel();
            $companyInfo = $company->GetCompanyById($company_id);
            if(!empty($companyInfo)){
                $branchList = $company->GetBranchByCompanyId($company_id);
                if(!empty($branchList)){
                    $companyInfo[0]["branchList"] = $branchList;
                }else{
                    $companyInfo[0]["branchList"] = [];
                }
            }else{
                $branchList = [];
            }
            
            return $companyInfo;
        }else if($message == 'getCompanyByName'){
            $company_name = $request->get('companyName');
            $company = new CompanyModel();
            $companyInfo = $company->GetCompanyByName($company_name);
            return $companyInfo;
        }else if($message == 'getIndustryType'){
            $company = new CompanyModel();
            $companyInfo = $company->GetCompany();
            return $companyInfo;
        }else{
            return [];
        }
    }
    
    public function DeleteData(Request $request)
    {
        $companyId = $request->get('companyId');
        $company = new CompanyModel();
        $retInfo = $company->DeleteCompanyById($companyId);
        if($retInfo == "success"){
            $personal = new PersonalModel();
            $retInfo = $personal->DeleteCompanyIdByCompanyId($companyId);
            $branchResult = $company->DeleteBranchOfficeByCompanyId($companyId);
            
            return "success";
        }else{
            return "";
        }
    }
    
    public function DeleteBranchData(Request $request)
    {
        $branchId = $request->get('branchId');
        $company = new CompanyModel();
        $retInfo = $company->DeleteBranchById($branchId);
        return $retInfo;
    }
    
}
