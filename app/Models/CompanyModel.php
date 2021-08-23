<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CompanyModel extends Model
{
    //###########################################################
    // tb_company_type[start]
    //###########################################################
    public function GetCompanyType()
    {
        $query = "SELECT * FROM tb_company_type ORDER BY id ASC";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetCompanyTypeById($id)
    {
        $query = "SELECT * FROM tb_company_type WHERE id = $id";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }

    public function GetCompanyTypeByName($name)
    {
        $query = "SELECT * FROM tb_company_type WHERE name = '$name'";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }

    public function SaveCompanyType($name)
    {
        try{
            $query = "INSERT INTO tb_company_type(id,name) 
                      SELECT MAX(id) +1,'$name' FROM tb_company_type
                      ON DUPLICATE KEY  UPDATE ";
            DB::insert($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    //###########################################################
    // tb_company_type[end]
    //###########################################################

    //###########################################################
    // tb_company[start]
    //###########################################################
    public function GetCompany()
    {
        $query = "SELECT * FROM tb_company ORDER BY id ASC";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }

    public function CheckExistingCompany($companyName){
        $query = "SELECT * FROM tb_company WHERE name = '$companyName'";
        $result = DB::select($query);
        return $result;
    }
    
    public function InsertNewCompany($companyName,$companyTypeId,$industryType = ""){
        // $query = "INSERT INTO tb_company(name,company_type_id) VALUES('$companyName',$company_type_id)";
        // $result = DB::insert($query);
        // return $result;
        try{ 
            $name = $companyName;
            $company_type_id = $companyTypeId;
            $industry_type = $industryType;
            $company_logo_path = "";
            

            $query = "INSERT INTO tb_company(id,name,company_type_id,industry_type,company_logo_path) 
                      SELECT MAX(id) +1,'$name',$company_type_id,'$industry_type','$company_logo_path' FROM tb_company
                      ON DUPLICATE KEY  UPDATE company_type_id=$company_type_id";
            DB::insert($query);
            return "success";
 
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function GetCompanyIdFromCompany($companyName,$company_type_id){
        $query = "SELECT id FROM tb_company WHERE name = '$companyName'";
        $result = DB::select($query);
        return $result;
    }
    
    public function GetCompanyById($id)
    {
        $query = "SELECT company.*,company_type.name as company_type_name FROM tb_company as company
                 LEFT JOIN tb_company_type as company_type on company_type.id = company.company_type_id
                 WHERE company.id = $id";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }

    public function GetCompanyByName($name)
    {
        $query = "SELECT * FROM tb_company WHERE name = '$name'";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function SaveCompany($company)
    {
        $name = $company["txtName"];
        $company_type_id = $company["companyTypeSelect"];
        $industry_type = $company["txtIndustryType"];
        $company_logo_path = "";
        
        try{
            $query = "INSERT INTO tb_company(id,name,company_type_id,industry_type,
                      company_logo_path) 
                      SELECT MAX(id) +1,'$name',$company_type_id,'$industry_type',
                      '$company_logo_path' FROM tb_company
                      ON DUPLICATE KEY UPDATE company_type_id=$company_type_id,
                      industry_type='$industry_type',company_logo_path='$company_logo_path'";
            DB::insert($query);

            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function SaveCompanyList($companyList)
    {
        try{
            foreach($companyList as $company){
                $name = $company["txtName"];
                $company_type_id = $company["companyTypeSelect"];
                $industry_type = $company["txtIndustryType"];
                $company_logo_path = "";
                $postal_code = $company["txtPostalCode"];
                $address = $company["txtAddress"];
                
                $query = "INSERT INTO tb_company(id,name,company_type_id,industry_type,
                          company_logo_path,postal_code,address) 
                          SELECT MAX(id) +1,'$name',$company_type_id,'$industry_type',
                          '$company_logo_path','$postal_code','$address' FROM tb_company
                          ON DUPLICATE KEY UPDATE company_type_id=$company_type_id,
                          industry_type='$industry_type',company_logo_path='$company_logo_path',
                          postal_code='$postal_code',address='$address'";
                DB::insert($query);
            }
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function InsertCompanyBySpeedCourse($inserList)
    {
        try{
            foreach($inserList as $userinfo){
                
                $name = $userinfo["companyName"];
                if($name == ""){
                    continue;
                }
                
                $company_type_id = 0;
                if($userinfo["companyTypeId"] != "" && $userinfo["companyTypeId"] != null){
                    $company_type_id = (int)$userinfo["companyTypeId"];
                }
                $industry_type = "";
                $company_logo_path = "";
                $postal_code = "";
                $address = "";

                $query = "INSERT INTO tb_company(id,name,company_type_id,industry_type,company_logo_path,postal_code,address) 
                          SELECT MAX(id) +1,'$name',$company_type_id,'$industry_type','$company_logo_path','$postal_code','$address' FROM tb_company
                          ON DUPLICATE KEY  UPDATE company_type_id=$company_type_id";
                DB::insert($query);

            }
            return "success";
 
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function DeleteCompanyById($companyId)
    {
        $query = "DELETE FROM tb_company WHERE id = $companyId";
        DB::delete($query);
        return "success";
    }
    
    public function GetCompanyInfoByName($name){
        $query = "SELECT * FROM tb_company WHERE name = '$name'";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetCompanyInfoByNameAndCompanyTypeId($name, $companyTypeId){
        $query = "SELECT * FROM tb_company WHERE name = '$name' AND company_type_id = $companyTypeId";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    //###########################################################
    // tb_company[end]
    //###########################################################

    //###########################################################
    // tb_branch_office[start]
    //###########################################################
    public function SaveBranchOffice($company_id, $company){
        
        try{
            $branchName = $company["txtBranch"];
            if($branchName == ""){
                $code = "";
                $postal_code = "";
                $address = "";
            }else{
                $code = $company["txtCode"];
                $postal_code = $company["txtPostalCode"];
                $address = $company["txtAddress"];
            }

            $query = "INSERT INTO tb_branch_office(id,company_id,code,name,postal_code,address) 
                      SELECT MAX(id) +1,$company_id,'$code','$branchName','$postal_code','$address' FROM tb_branch_office
                      ON DUPLICATE KEY UPDATE code='$code',postal_code='$postal_code',address='$address'";
            DB::insert($query);

            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    
    public function GetAllBranch(){
        $query = "SELECT * FROM tb_branch_office ORDER BY id ASC";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }

    public function GetBranchById($branchId){
        $query = "select * from tb_branch_office where id=$branchId";
        $result = DB::select($query);
        return json_decode(json_encode($result),true);
    }
    
    public function GetBranchByCompanyId($companyId){
        $query = "select * from tb_branch_office where company_id=$companyId";
        $result = DB::select($query);
        return json_decode(json_encode($result),true);
    }
    
    public function DeleteBranchOfficeByCompanyId($companyId){
        $query = "DELETE FROM tb_branch_office where company_id = $companyId";
        DB::delete($query);
        return "success";
    }
    
    public function DeleteBranchById($branchId)
    {
        $query = "DELETE FROM tb_branch_office WHERE id = $branchId";
        DB::delete($query);
        return "success";
    }

    //###########################################################
    // tb_branch_office[end]
    //###########################################################

    //###########################################################
    // tb_dept[start]
    //###########################################################
    public function GetAllDept(){
        $query = "SELECT * FROM tb_dept ORDER BY id ASC";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }

    public function CheckExistingDept($branchId,$dept){
        $query = "SELECT * FROM tb_dept WHERE branch_id = $branchId AND name = '$dept'";
        $result = DB::select($query);
        return $result;
    }
    
    public function CheckExistingDeptNotLike($branchId,$dept){
        $query = "SELECT * FROM tb_dept WHERE branch_id = $branchId AND name = '$dept'";
        $result = DB::select($query);
        return json_decode(json_encode($result),true);
    }
    
    public function GetDeptById($depId){
        $query = "select * from tb_dept where id=$depId";
        $result = DB::select($query);
        return json_decode(json_encode($result),true);
    }
    
    public function GetDeptIdByNameAndBranchId($dept,$branchId){
        $query = "SELECT id FROM tb_dept WHERE name LIKE '%$dept%' AND branch_id = $branchId";
        $result = DB::select($query);
        return $result;
    }

    public function InsertNewDepartment($dept,$branchId){
        // $query = "INSERT INTO tb_company(name,company_type_id) VALUES('$companyName',$company_type_id)";
        // $result = DB::insert($query);
        // return $result;
        try{ 
            $name = $dept;
            $branch_id = $branchId;
            $code = "";
            
            $query = "INSERT INTO tb_dept(id,branch_id,code,name) 
                      SELECT MAX(id) +1,$branch_id,'$code','$name' FROM tb_dept
                      ON DUPLICATE KEY  UPDATE branch_id=$branch_id";
            DB::insert($query);
            return "success";
 
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    //###########################################################
    // tb_dept[end]
    //###########################################################
    //###########################################################
    // tb_cooperate_company[start]
    //###########################################################

    public function InsertCooperateCompanyInfo($companyInfo){
        try{
            $companyId = $companyInfo['company_id'];
            $userId = empty($companyInfo['incharge_id']) ? 0 : $companyInfo['incharge_id'];
            $yaruki = empty($companyInfo['yaruki']) ? "NULL" : $companyInfo['yaruki'];
            $revit = empty($companyInfo['revit']) ? "NULL" : $companyInfo['revit'];
            $ipd = empty($companyInfo['ipd']) ? "NULL" : $companyInfo['ipd'];
            $satelliteExp = empty($companyInfo['satelliteExp']) ? "NULL" : $companyInfo['satelliteExp'];
            $satelliteProjName = empty($companyInfo['satelliteProjName']) ? "NULL" : $companyInfo['satelliteProjName'];
            $remark = empty($companyInfo['remark']) ? "NULL" : $companyInfo['remark'];
            
            
            $query = "INSERT INTO tb_cooperate_company(id,company_id,user_id,yaruki,revit,iPDStudent,satelliteExp,satelliteName,remark) 
                      SELECT MAX(id) +1,$companyId,$userId,'$yaruki','$revit','$ipd','$satelliteExp','$satelliteProjName','$remark' FROM tb_cooperate_company
                      ON DUPLICATE KEY  UPDATE 
                      yaruki = '$yaruki',
                      revit = '$revit' ,
                      iPDStudent = '$ipd' ,
                      satelliteExp= '$satelliteExp',
                      satelliteName= '$satelliteProjName',
                      remark = '$remark'";
            DB::insert($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    //###########################################################
    // tb_cooperate_company[end]
    //###########################################################
    
    //###########################################################
    // tb_modelling_company[start]
    //###########################################################

    public function InsertModellingCompanyInfo($companyInfo){
        try{
            $companyId = $companyInfo['company_id'];
            $userId = empty($companyInfo['incharge_id']) ? 0 : $companyInfo['incharge_id'];
            $branch = empty($companyInfo['branch']) ? NULL : $companyInfo['branch'];
            $isPartnerCompany = $companyInfo['isPartnerCompany'];
            
            
            $query = "INSERT INTO tb_modelling_company(id,company_id,user_id,branch,isPartnerCompany) 
                      SELECT MAX(id) +1,$companyId,$userId,'$branch',$isPartnerCompany FROM tb_modelling_company
                      ON DUPLICATE KEY  UPDATE 
                      branch = '$branch',
                      isPartnerCompany = $isPartnerCompany";
            DB::insert($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    //###########################################################
    // tb_modelling_company[end]
    //###########################################################

}