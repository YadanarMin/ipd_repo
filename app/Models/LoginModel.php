<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class LoginModel extends Model
{
    protected $table = 'tb_login';
    public function getData($params)
    {
      //using framework  
     /* $query = DB::table($this->table);   
      $query->where('name', $params['name']);
      $query->where('password', $params['password']);    
      $data = $query->distinct()->get(); */
      
      $query = "SELECT * FROM 
                  (SELECT concat(per.first_name,per.last_name) as name,per.id,login.password,per.mail,authority_id,first_time_login
                  FROM tb_personal as per
                  LEFT JOIN tb_login as login ON login.personal_id = per.id) as temp
                  WHERE  temp.password = '$params[password]' 
                  AND (name = '$params[name]' OR mail ='$params[name]')";
      
      $data = DB::select($query);
      
      return $data;
    }
    
    public function getAuthorityId($params)
    {
      $query = "SELECT * FROM tb_ccc_authority WHERE name = '$params[name]'";
      $data = DB::select($query);
      
      return $data;
    }
    
    public function getUserCodeByName($name)
    {
      $query = "SELECT * FROM tb_user_code WHERE name = '$name'";
      $data = DB::select($query);
      
      return $data;
    }
    
    public function GetBoxAuthority($authority_id)
    {
      $query = "SELECT box_access FROM tb_ccc_authority WHERE id = $authority_id";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }

    function ChangePassword($newPassword,$loginName,$personalId){
        try{
            $query = "UPDATE tb_login SET password = '$newPassword',first_time_login = 2 WHERE personal_id = $personalId";
            DB::update($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function DeleteUserById($userId)
    {
          $query = "DELETE FROM tb_login WHERE id = $userId";
          DB::delete($query);
          return "success";
    }
    
    public function DeleteUserByPersonalId($personalId)
    {
          $query = "DELETE FROM tb_login WHERE personal_id = $personalId";
          DB::delete($query);
          return "success";
    }
    
    public function DeleteRecentlyUsedCCC($personalId)
    {
          $query = "DELETE FROM tb_recently_used_ccc WHERE user_id = $personalId";
          DB::delete($query);
          return "success";
    }
    
    public function DeleteAuthorityById($authorityId)
    {
        
          $query = "DELETE FROM tb_ccc_authority WHERE id = $authorityId";
          DB::delete($query);
          return "success";
    }

    public function GetContents()
    {
      $query = "SELECT * FROM tb_ccc_contents";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    public function GetContentsLatestId()
    {
      $query = "SELECT * FROM tb_ccc_contents ORDER BY id DESC LIMIT 1";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    public function SetContents($content_name,$category,$img_src)
    {
        try{
            $query = "INSERT INTO tb_ccc_contents(id,name,category,img_src,data_url) 
                      SELECT MAX(id) +1,'$content_name','$category','$img_src','' FROM tb_ccc_contents
                      ON DUPLICATE KEY  UPDATE category='$category',img_src='$img_src',data_url=''";
            DB::insert($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function DeleteContentById($contentsId)
    {
        $query = "DELETE FROM tb_ccc_contents WHERE id = $contentsId";
        DB::delete($query);
        return "success";
    }
    
    public function GetCCCAccessHistory($personal_id)
    {
      $query = "SELECT * FROM tb_recently_used_ccc WHERE user_id = $personal_id";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetAllUserCode(){
      $query = "SELECT * FROM tb_user_code";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function ChangeLoginUserSetting($checkboxName,$personalId,$status){
        if(trim($checkboxName) == "chkCCCMaster"){
            $query = "UPDATE tb_login SET isCCCMaster = $status WHERE personal_id = $personalId";
            DB::update($query);
        }else if(trim($checkboxName) == "chkChiefAdmin"){
           $query = "UPDATE tb_login SET isChiefAdmin = $status WHERE personal_id = $personalId"; 
           DB::update($query);
        }
        return "success";
    }
    
    function GetChiefAdminNoti($loginUserId){
       
      $query = "SELECT per.mail,per.company_id,CONCAT(per.first_name,per.last_name)as name,per.isC3User,login.personal_id
                FROM tb_login login
                LEFT JOIN tb_personal as per ON per.id = login.personal_id
                WHERE chief_admin_id = $loginUserId 
                AND (SELECT isChiefAdmin FROM tb_login WHERE personal_id = $loginUserId) = 1
                AND per.isC3User = 1";//管理責任者承認待ちstate
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetCCCMasterNoti($loginUserId){
       
      $query = "SELECT per.mail,per.company_id,CONCAT(per.first_name,per.last_name)as name,per.isC3User,login.chief_admin_id,login.personal_id
                FROM tb_login login
                LEFT JOIN tb_personal as per ON per.id = login.personal_id
                WHERE (SELECT isCCCMaster FROM tb_login WHERE personal_id = $loginUserId) = 1
                AND per.isC3User = 2";//CCCMaster承認待ちstate
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetAllUser(){
      $query = "SELECT login.password,login.chief_admin_id,login.isCCCMaster,login.isChiefAdmin,
                (SELECT name from tb_ccc_authority WHERE id = login.authority_id) as authority_name,
                (SELECT name from tb_branch_office WHERE id = per.branch_id) as branch,
                (SELECT name from tb_dept WHERE id = per.dept_id) as department,
                (SELECT CONCAT(first_name,last_name) from tb_personal WHERE id = login.chief_admin_id) as chief_admin_name,
                CONCAT(per.first_name,per.last_name) as name,per.mail,per.id as personal_id,per.isC3User FROM tb_login login
                LEFT JOIN tb_personal as per ON per.id = login.personal_id";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function DuplicateEmailChecking($email){
      $query = "SELECT COUNT(*) as count FROM tb_personal WHERE mail = '$email'";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }

    function DuplicateCompanyChecking($companyName){
      $query = "SELECT COUNT(*) as count FROM tb_company WHERE name = '$companyName'";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetUserById($userId){
      $query = "SELECT * FROM tb_login WHERE id = $userId";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetUserByPersonalId($personalId){
      $query = "SELECT * FROM tb_login WHERE personal_id = $personalId";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetAllAuthority(){
      $query = "SELECT * FROM tb_ccc_authority";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    public function GetAuthorityById($authority_id){
      $query = "SELECT * FROM tb_ccc_authority where id = $authority_id";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }

    function GetAuthorityByName($authority_name){
      $query = "SELECT * FROM tb_ccc_authority WHERE name = '$authority_name'";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
   function SaveUserCode($params){
     $code= $params['txtCode'];
     $name= $params['txtName'];
     try{
        $query = "INSERT INTO tb_user_code(id,user_code,name) 
                  SELECT MAX(id) +1,'$code','$name' FROM tb_user_code
                  ON DUPLICATE KEY UPDATE user_code='$code',name='$name'";
        DB::insert($query);
        
        return "success";
     }catch(Exception $e){
      return $e->getMessage();
     }
     
   }

   function SaveLoginUser($params){

     $personalId = $params["hidPersonalId"];  
     $password = $params['txtPassword'];
     $authority_id = $params['authoritySelect'];
     try{
        $query = "INSERT INTO tb_login(id,personal_id,password,authority_id) 
                  SELECT MAX(id) +1,$personalId,'$password',$authority_id FROM tb_login
                  ON DUPLICATE KEY  UPDATE password='$password',authority_id = $authority_id";
                  DB::insert($query);
        
        return "success";
     }catch(Exception $e){
      return $e->getMessage();
     }
     
   }
   
   function SaveAccessHistory($params){
     $user_id = (int)$params['user_id'];
     $img_src = $params['img_src'];
     $url = $params['url'];
     $content_name = $params['content_name'];
     try{
        $query = "INSERT INTO tb_recently_used_ccc(id,user_id,img_src,url,content_name) 
                  SELECT MAX(id) +1,$user_id,'$img_src','$url','$content_name' FROM tb_recently_used_ccc
                  ON DUPLICATE KEY UPDATE img_src='$img_src',url='$url',content_name='$content_name'";
        DB::insert($query);
        return "success";
     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function CreateAuthority($name,$authority){
     
        try{
            $query = "INSERT INTO tb_ccc_authority(id,name,authority,box_access) 
                      SELECT MAX(id) +1,'$name','$authority',0 FROM tb_ccc_authority
                      ON DUPLICATE KEY  UPDATE name='$name',authority = '$authority',box_access = 0";
            DB::insert($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
     
   }
   
    function UpdateAuthority($name,$authority_string,$box_access){
        
        try{
            $query = "INSERT INTO tb_ccc_authority(id,name,authority,box_access) 
                      SELECT MAX(id) +1,'$name','$authority_string',$box_access FROM tb_ccc_authority
                      ON DUPLICATE KEY  UPDATE name='$name',authority = '$authority_string',box_access = $box_access";
            DB::insert($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    
    function UpdateAllAuthority($allAuthorityData){
     
        try{
            foreach($allAuthorityData as $authorityData){
                $id = $authorityData["id"];
                $authority = $authorityData["authority"];

                $query = "UPDATE tb_ccc_authority SET authority = '$authority' WHERE id = $id";
                DB::update($query);
            }
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    
    function GetCompanyType(){
      $query = "SELECT * FROM tb_company_type ORDER BY id";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetDepartmentByBranchId($branch_id){
      $query = "SELECT * FROM tb_dept WHERE branch_id = $branch_id";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetCompanyData(){
      $query = "SELECT c.*,b.name as branch_name,b.id as branch_id,d.name as dept_name,d.id as dept_id
                FROM tb_company as c 
                LEFT JOIN tb_branch_office as b ON b.company_id = c.id
                LEFT JOIN tb_dept as d ON d.branch_id = b.id
                ORDER BY c.company_type_id";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetChiefAdminInfo(){
        try{
              $query = "SELECT per.id,CONCAT(per.first_name,' ',per.last_name) as name,per.mail,per.position,
                        (SELECT name FROM tb_dept WHERE id=per.dept_id) as department
                        FROM tb_personal as per
                        LEFT JOIN tb_login as login ON login.personal_id = per.id
                        WHERE isC3User = 3 AND login.isChiefAdmin = 1";//;
              $data = DB::select($query);
              return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function GetPersonalInfoById($user_id){
        try{
              $query = "SELECT per.id,CONCAT(per.first_name,' ',per.last_name) as name,per.mail,
                            per.phone,per.work_location,per.isC3User,
                            (SELECT name FROM tb_dept WHERE id = per.dept_id) as dept,
                            (SELECT name FROM tb_branch_office WHERE id = per.branch_id) as branch,
                            (SELECT name FROM tb_company WHERE id = per.company_id) as companyName,
                            (SELECT id FROM tb_company WHERE id = per.company_id) as companyId 
                        FROM tb_personal per
                        WHERE per.id = $user_id";
              $data = DB::select($query);
              return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function SaveLoginAccountInfo($step1Data,$chiefAdminId){
     
     try{
         $email = $step1Data->email;
         $first_name = $step1Data->firstName;
         $last_name = $step1Data->lastName;
         $first_name_kana = $step1Data->firstNameKana;
         $last_name_kana = $step1Data->lastNameKana;
         $password = $step1Data->password;
         $company_type_id = $step1Data->companyTypeId;
         $company_id = ($step1Data->companyId ) == "" ? 'NULL' : $step1Data->companyId;
         $company = $step1Data->company;
         $branch_id = ($step1Data->branchId ) == "" ? 'NULL' : $step1Data->branchId;
         $branch = $step1Data->branch;
         $dept_id = ($step1Data->departmentId) == "" ? 'NULL' : $step1Data->departmentId;
         $dept = $step1Data->department;
         $phone = $step1Data->phone;
         $work_location = $step1Data->workingPlace;

         $isC3User = 1;
         $isStudyAbroad = 0;
         $isSpeedCourse = 0;
         $update = "";
         if($phone != ""){
             $update .= ",phone = '$phone'";
         }
         if($work_location != ""){
             $update .= ",work_location = '$work_location'";
         }
         
         //department insert
        if($dept_id == 'NULL' && $dept != "" && $branch_id != 'NULL'){
            $query = "INSERT INTO tb_dept(id,branch_id,name) 
                      SELECT MAX(id) +1,$branch_id,'$dept' FROM tb_dept
                      ON DUPLICATE KEY  UPDATE name='$dept'";
            DB::insert($query);
            $select = "SELECT id FROM tb_dept WHERE branch_id=$branch_id AND name='$dept'";
            $result = DB::select($select);
            if($result != null){
                $result = json_decode(json_encode($result),true);
                $dept_id = $result[0]["id"];
            }
            
        }
        
        //company insert
        if($company != "" && $company_id == 'NULL'){
            $query = "INSERT INTO tb_company(id,name,company_type_id,isNew) 
                      SELECT MAX(id) +1,'$company',$company_type_id,1 FROM tb_company
                      ON DUPLICATE KEY  UPDATE name='$company'";
            DB::insert($query);
            $select = "SELECT id FROM tb_company WHERE name='$company'";
            $result = DB::select($select);
            if($result != null){
                $result = json_decode(json_encode($result),true);
                $company_id = $result[0]["id"];
            }
        }
         
         //personal info insert
        $queryPersonal = "INSERT INTO tb_personal(id,mail,first_name,last_name,first_name_kana,last_name_kana,
                          company_id,branch_id,dept_id,phone,work_location,
                          isC3User,isStudyAbroad,isSpeedCourse) 
                          SELECT MAX(id) +1,'$email','$first_name','$last_name','$first_name_kana','$last_name_kana',
                          $company_id,$branch_id,$dept_id,'$phone','$work_location',
                          $isC3User,$isStudyAbroad,$isSpeedCourse FROM tb_personal
                          ON DUPLICATE KEY  UPDATE company_id=$company_id,branch_id=$branch_id,dept_id=$dept_id".$update;
        DB::insert($queryPersonal);

        $name = $first_name.$last_name;
        $personalId = "NULL";
        $select = "SELECT id FROM tb_personal WHERE mail='$email'";
        $result = DB::select($select);
        if($result != null){
            $result = json_decode(json_encode($result),true);
            $personalId = $result[0]["id"];
        }
        //login info insert
        if($personalId != "NULL"){
            $query = "INSERT INTO tb_login(id,personal_id,password,chief_admin_id) 
                  SELECT MAX(id) +1,$personalId, '$password',$chiefAdminId FROM tb_login
                  ON DUPLICATE KEY  UPDATE chief_admin_id=$chiefAdminId,password = '$password'";
            DB::insert($query);
        }
        
        
       
     
        return "success";
     }catch(Exception $e){
      return $e->getMessage();
     }
     
   }
   
   function UpdateIsC3UserByChiefAdmin($user_id){
       try{
           $query ="UPDATE tb_personal SET isC3User = 2 WHERE id = $user_id";
           DB::update($query);
           return "success";
           
       }catch(Exception $e){
           return $e->getMessage();
       }
   }
   
    function UpdateIsC3UserByCCCAdmin($user_id,$authorityId){
       try{
           //need to update complete isNew flag when adim is approved.
           
           $query ="UPDATE tb_personal SET isC3User = 3 WHERE id = $user_id";
           DB::update($query);
           
           $queryLogin ="UPDATE tb_login SET authority_id=$authorityId WHERE personal_id =$user_id";
           DB::update($queryLogin);
           
           $queryCompany ="UPDATE tb_company SET isNew = 0 WHERE id =(SELECT company_id FROM tb_personal WHERE id=$user_id)";
           DB::update($queryCompany);
           
           return "success";
           
       }catch(Exception $e){
           return $e->getMessage();
       }
   }
   
   function DeleteLoginAccountInfo($personalId,$companyId){
      try{
           //delete login info
           $query ="DELETE FROM tb_login WHERE personal_id = $personalId";
           DB::delete($query);
           
           //delete company info
           $queryCompany ="DELETE FROM tb_company 
                    WHERE id = (SELECT company_id FROM tb_personal WHERE id = $personalId) 
                    AND isNew = 1
                    AND (SELECT count(mail) FROM tb_personal WHERE company_id = $companyId) <= 1";
           DB::delete($queryCompany);
           
           //delete department info
        //   $queryDept ="DELETE FROM tb_dept 
        //             WHERE id = (SELECT dept_id FROM tb_personal WHERE id = $personalId) 
        //             AND (SELECT count(mail) FROM tb_personal WHERE dept_id = id) <= 1";
        //   DB::delete($queryDept);
           
           //delete personal info
           $queryPersonal ="DELETE FROM tb_personal WHERE id = $personalId AND isStudyAbroad = 0 AND isSpeedCourse = 0 ";
           DB::delete($queryPersonal);
           
           return "success";
           
       }catch(Exception $e){
           return $e->getMessage();
       } 
   }
   
    public function DeleteLoginById($personalId)
    {
        $query = "DELETE FROM tb_login WHERE personal_id = $personalId";
        DB::delete($query);
        return "success";
    }

}
