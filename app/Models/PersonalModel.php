<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class PersonalModel extends Model
{
    public function GetAllPersonalData()
    {
        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,dept.name as dept_name,
                    b.name as branch_name,c.name as company_name from tb_personal p
                    LEFT JOIN tb_dept as dept on dept.id = p.dept_id
                    LEFT JOIN tb_branch_office as b on b.id = p.branch_id
                    LEFT JOIN tb_company as c on c.id = p.company_id";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetNotC3UserData()
    {
        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,dept.name as dept_name,
                    b.name as branch_name,c.name as company_name from tb_personal p
                    LEFT JOIN tb_dept as dept on dept.id = p.dept_id
                    LEFT JOIN tb_branch_office as b on b.id = p.branch_id
                    LEFT JOIN tb_company as c on c.id = p.company_id
                    WHERE p.isC3User = 0";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetPersonalById($id)
    {
        try{
              $query = "SELECT per.id,CONCAT(per.first_name,' ',per.last_name) as name,per.mail,
                            per.phone,per.work_location,per.isC3User,per.code,per.first_name,per.last_name,
                            per.first_name_kana,per.last_name_kana,per.position,per.outsideCall,per.fax,
                            per.branch_id,per.isAdditionalPost,per.contract_type,
                            (SELECT name FROM tb_dept WHERE id = per.dept_id) as dept_name,
                            (SELECT name FROM tb_branch_office WHERE id = per.branch_id) as branch_name,
                            (SELECT id FROM tb_company_type WHERE id = 
                                (SELECT company_type_id FROM tb_company WHERE id = per.company_id)
                            ) as company_type_id,
                            (SELECT name FROM tb_company_type WHERE id = 
                                (SELECT company_type_id FROM tb_company WHERE id = per.company_id)
                            ) as company_type_name,
                            (SELECT password FROM tb_login WHERE personal_id = per.id) as password,
                            (SELECT authority_id FROM tb_login WHERE personal_id = per.id) as authority_id,
                            (SELECT name FROM tb_ccc_authority WHERE id =
                                (SELECT authority_id FROM tb_login WHERE personal_id = per.id)
                            ) as authority_name,
                            (SELECT id FROM tb_company WHERE id = per.company_id) as company_id,
                            (SELECT name FROM tb_company WHERE id = per.company_id) as company_name,
                            (SELECT company_type_id FROM tb_company WHERE id = per.company_id) as company_type_id,
                            (SELECT company_id FROM tb_haken WHERE user_id = per.id) as haken_company_id,
                            (SELECT name FROM tb_company WHERE id = 
                                (SELECT company_id FROM tb_haken WHERE user_id = per.id)
                            ) as haken_company_name,
                            (SELECT company_type_id FROM tb_company WHERE id = 
                                (SELECT company_id FROM tb_haken WHERE user_id = per.id)
                            ) as haken_company_type_id,
                            (SELECT name FROM tb_company_type WHERE id = 
                                (SELECT company_type_id FROM tb_company WHERE id = 
                                    (SELECT company_id FROM tb_haken WHERE user_id = per.id)
                                )
                            ) as haken_company_type_name
                        FROM tb_personal per
                        WHERE per.id = $id";
              $data = DB::select($query);
              return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function GetPersonalByMail($mail)
    {
        $query = "SELECT * FROM tb_personal WHERE mail = '$mail'";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetPersonal($id){
        $query = "SELECT CONCAT(first_name,' ',last_name) as name , dept_id FROM tb_personal where id = $id";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetInchargeInfoById($id){
        $query = "SELECT concat(first_name,' ',last_name) as name, phone, outsideCall, mail FROM tb_personal WHERE id = $id";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetBimUserByID($id){
        $query = "SELECT * FROM tb_personal WHERE id=$id AND isSpeedCourse = 1";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }

    public function GetAllHakenCompany(){
        $query = "SELECT * FROM tb_haken";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetHakenCompanyByUserId($id){
        $query = "SELECT * FROM tb_haken WHERE user_id=$id";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetStudentInfoByUserId($id){
        $query = "SELECT * FROM tb_students WHERE user_id=$id";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function UpdateC3Available($personalId)
    {
        try{
            $query = "UPDATE tb_personal SET isC3User = 3 WHERE id = $personalId";
            DB::insert($query);
            
            //update tb_login first_time_login flag
            $query = "UPDATE tb_login SET first_time_login = 1 WHERE personal_id = $personalId AND first_time_login != 1";
            DB::insert($query);
            
            return "success";

        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function UpdateC3Unavailable($personalId)
    {
        try{
            $query = "UPDATE tb_personal SET isC3User = 0 WHERE id = $personalId";
            DB::insert($query);
            return "success";

        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function DeleteCompanyIdByCompanyId($companyId)
    {
        try{
            $query = "UPDATE tb_personal SET company_id = null,branch_id = null,dept_id = null WHERE company_id = $companyId";

            DB::insert($query);
            return "success";

        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function GetPersonalInfoByCompanyIdAndUserId($companyId, $userId){
        $query = "select * from tb_personal where company_id = $companyId AND id = $userId";
        $data = DB::select($query);
        return json_decode(json_encode($data),true); 
    }
    
    public function InsertPersonal($params)
    {
        try{
            $mail = $params['mail'];
            $first_name = $params['first_name'];
            $last_name = $params['last_name'];
            $first_name_kana = $params['first_name_kana'];
            $last_name_kana = $params['last_name_kana'];
            
            $company_id = empty($params['company_id']) ? "null" : $params['company_id'];
            $dept_id = empty($params['dept_id']) ? "null" : $params['dept_id'];
            $branch_id = empty($params['branch_id']) ? "null" :$params['branch_id'];
            
            $phone = $params['phone'];
            $work_location = $params['work_location'];
            $code = $params['code'] ;
            $position = $params['position'] ;
            $outsideCall = $params['outsideCall'];
            $fax = $params['fax'];
            
            $isAdditionalPost = empty($params['isAdditionalPost']) ? 0 : $params['isAdditionalPost'];
            $isC3User =empty($params['isC3User']) ? 0 : $params['isC3User'] ;
            $isStudyAbroad = empty($params['isStudyAbraod']) ? 0 : $params['isStudyAbraod'];
            $isSpeedCourse = empty($params['isSpeedCourse']) ? 0 : $params['isSpeedCourse'];
            $contract_type = empty($params['contract_type']) ? 0 : $params['contract_type'];

            $query = "INSERT INTO tb_personal(id,mail,first_name,last_name,
                      first_name_kana,last_name_kana,
                      company_id,dept_id,branch_id,
                      phone,work_location,code,position,outsideCall,fax,
                      isAdditionalPost,isC3User,isStudyAbroad,isSpeedCourse,contract_type) 
                      SELECT MAX(id) +1,'$mail','$first_name','$last_name',
                      '$first_name_kana','$last_name_kana',
                      $company_id,$dept_id,$branch_id,
                      '$phone','$work_location','$code','$position','$outsideCall','$fax',
                      $isAdditionalPost,$isC3User,$isStudyAbroad,$isSpeedCourse,$contract_type FROM tb_personal
                      ON DUPLICATE KEY  UPDATE first_name='$first_name',last_name='$last_name',
                      first_name_kana='$first_name_kana',last_name_kana='$last_name_kana',
                      company_id=$company_id,dept_id=$dept_id,branch_id=$branch_id,
                      phone='$phone',work_location='$work_location',code='$code',position='$position',outsideCall='$outsideCall',fax='$fax',
                      isAdditionalPost=$isAdditionalPost,contract_type=$contract_type";
            DB::insert($query);
            return "success";

        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    
    public function InsertPersonalCooperateCompany($params)
    {
        try{
            $mail = $params['mail'];
            $first_name = $params['first_name'];
            $last_name = $params['last_name'];
            $first_name_kana = "";
            $last_name_kana = "";
            
            $company_id = empty($params['company_id']) ? "null" : $params['company_id'];
            $dept_id = empty($params['dept_id']) ? "null" : $params['dept_id'];
            $branch_id = empty($params['branch_id']) ? "null" :$params['branch_id'];
            
            $phone = $params['phone'];
            $work_location = "";
            $code = "" ;
            $position = "" ;
            $outsideCall = $params['outsideCall'];
            $fax = "";
            
            $isAdditionalPost = empty($params['isAdditionalPost']) ? 0 : $params['isAdditionalPost'];
            $isC3User =empty($params['isC3User']) ? 0 : $params['isC3User'] ;
            $isStudyAbroad = empty($params['isStudyAbraod']) ? 0 : $params['isStudyAbraod'];
            $isSpeedCourse = empty($params['isSpeedCourse']) ? 0 : $params['isSpeedCourse'];

            $query = "INSERT INTO tb_personal(id,mail,first_name,last_name,
                      first_name_kana,last_name_kana,
                      company_id,dept_id,branch_id,
                      phone,work_location,code,position,outsideCall,fax,
                      isAdditionalPost,isC3User,isStudyAbroad,isSpeedCourse) 
                      SELECT MAX(id) +1,'$mail','$first_name','$last_name',
                      '$first_name_kana','$last_name_kana',
                      $company_id,$dept_id,$branch_id,
                      '$phone','$work_location','$code','$position','$outsideCall','$fax',
                      $isAdditionalPost,$isC3User,$isStudyAbroad,$isSpeedCourse FROM tb_personal
                      ON DUPLICATE KEY  UPDATE first_name='$first_name',last_name='$last_name',
                      first_name_kana='$first_name_kana',last_name_kana='$last_name_kana',
                      company_id=$company_id,dept_id=$dept_id,branch_id=$branch_id,
                      phone='$phone',work_location='$work_location',code='$code',position='$position',outsideCall='$outsideCall',fax='$fax',
                      isAdditionalPost=$isAdditionalPost";
            DB::insert($query);
            return "success";

        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    
    public function GetInchargeIdByMail($mail){
        $query = "SELECT id from tb_personal WHERE mail = '$mail'";
        $result = DB::select($query);
        return json_decode(json_encode($result),true); 
    }

    public function InsertPersonalSubHaken($personal_id, $company_id)
    {
        try{
            $query = "INSERT INTO tb_haken(id,user_id,company_id) 
                      SELECT MAX(id) +1,$personal_id,$company_id FROM tb_haken
                      ON DUPLICATE KEY  UPDATE company_id=$company_id";
            DB::insert($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function DeleteHakenByUserId($personalId)
    {
        $query = "DELETE FROM tb_haken WHERE user_id = $personalId";
        DB::delete($query);
        return "success";
    }

    //***********Bim速習****************//
    public function InsertPersonalBySpeedCourse($inserList)
    {
        try{
            foreach($inserList as $userinfo){
                $mail = $userinfo['email'];
                $first_name = $userinfo['name'];
                $last_name = $userinfo['lastname'];
                $first_name_kana = "";
                $last_name_kana = "";
                $company_id = empty($userinfo['companyId']) ? "NULL" : $userinfo['companyId'];
                $dept_id = empty($userinfo['deptId']) ? "NULL" : $userinfo['deptId'];
                $branch_id =  empty($userinfo['branchId'])  ? "NULL" : $userinfo['branchId'];
                $phone = "";
                $work_location = "";
                $code = $userinfo['code'];
                $position = $userinfo['position'];
                $outsideCall = "";
                $fax = "";
                $isAdditionalPost = empty($userinfo['isAdditionalPost']) ? 0 : $userinfo['isAdditionalPost'];
                $isC3User = empty($userinfo['isC3User']) ? 0 : $userinfo['isC3User'];
                $isStudyAbroad = empty($userinfo['isStudyAbraod']) ? 0 : $userinfo['isStudyAbraod'];
                $isSpeedCourse = 1;
                $contract_type = empty($userinfo['contractType']) ? 0 : $userinfo['contractType'];
                
                if($contract_type == "2"){
                    $company_id = "1";  //$contract_type("2")=大林組(派遣)の場合は、会社名には派遣元会社名が入っているため、固定で大林組のIDを設定
                }

                $query = "INSERT INTO tb_personal(id,mail,first_name,last_name,
                          first_name_kana,last_name_kana,
                          company_id,dept_id,branch_id,
                          phone,work_location,code,position,outsideCall,fax,
                          isAdditionalPost,isC3User,isStudyAbroad,isSpeedCourse,contract_type) 
                          SELECT MAX(id) +1,'$mail','$first_name','$last_name',
                          '$first_name_kana','$last_name_kana',
                          $company_id,$dept_id,$branch_id,
                          '$phone','$work_location','$code','$position','$outsideCall','$fax',
                          $isAdditionalPost,$isC3User,$isStudyAbroad,$isSpeedCourse,$contract_type FROM tb_personal
                          ON DUPLICATE KEY  UPDATE company_id=$company_id,code='$code',position='$position',
                          isSpeedCourse=$isSpeedCourse,contract_type=$contract_type";
                DB::insert($query);
            }
            
        }catch(Exception $e){
            return $e->getMessage();
        }
        
   }
   
   public function GetBIMCourseUser($userinfoList){
        $idArray = array();
        try{
            foreach($userinfoList as $userinfo){
                $email = $userinfo['email'];
                $query = "SELECT id from tb_personal WHERE mail = '$email' AND isSpeedCourse =1";
                $id = DB::select($query);
                array_push($idArray, $id[0]->id);
                
            }
            
            return $idArray;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function DeletePersonalById($personalId)
    {
        $query = "DELETE FROM tb_personal WHERE id = $personalId";
        DB::delete($query);
        return "success";
    }
   
   //***********留学生情報****************//
    public function InsertPersonalByStudyAbraod($userinfo)
    {
        print_r($userinfo);
       
        try{
            $first_name = $userinfo['first_name'];
            $last_name = $userinfo['last_name'];
            $first_name_kana = $userinfo['first_name_kana'];
            $last_name_kana = $userinfo['last_name_kana'];
            $mail = $userinfo['email'];
            $company_id = empty($userinfo["company_id"]) ? 1 : $userinfo["company_id"];
            $dept_id = empty($userinfo['dept_id']) ? "NULL" : $userinfo['dept_id'];
            $branch_id =  empty($userinfo['branch_id'])  ? "NULL" : $userinfo['branch_id'];
            $phone = "";
            $work_location = "";
            $code = $userinfo['genzai_code'];
            $position = $userinfo["position"];
            $outsideCall = "";
            $fax = "";
            $isAdditionalPost = 0;
            $isC3User = empty($userinfo['isC3User']) ? 0 : $userinfo['isC3User'];
            $isSpeedCourse = empty($userinfo['isSpeedCourse']) ? 0 : $userinfo['isSpeedCourse'];
            $isStudyAbroad = 1;
            $contract_type = empty($userinfo['contractType']) ? 0 : $userinfo['contractType'];
                
            if($contract_type == "2"){
                $company_id = "1";  //$contract_type("2")=大林組(派遣)の場合は、会社名には派遣元会社名が入っているため、固定で大林組のIDを設定
            }
                $query = "INSERT INTO tb_personal(id,mail,first_name,last_name,
                          first_name_kana,last_name_kana,
                          company_id,dept_id,branch_id,
                          phone,work_location,code,position,outsideCall,fax,
                          isAdditionalPost,isC3User,isStudyAbroad,isSpeedCourse,contract_type) 
                          SELECT MAX(id) +1,'$mail','$first_name','$last_name',
                          '$first_name_kana','$last_name_kana',
                          $company_id,$dept_id,$branch_id,
                          '$phone','$work_location','$code','$position','$outsideCall','$fax',
                          $isAdditionalPost,$isC3User,$isStudyAbroad,$isSpeedCourse, $contract_type FROM tb_personal
                          ON DUPLICATE KEY  UPDATE 
                          company_id=$company_id,
                          code='$code',
                          position='$position',
                          isStudyAbroad=$isStudyAbroad,
                          contract_type=$contract_type";
                DB::insert($query);
        }catch(Exception $e){
            return $e->getMessage();
        }
        
   }
   
   public function GetAllForeignStudents(){
       //SELECT * from tb_personal JOIN tb_students On tb_personal.id = tb_students.user_id order by tb_students.startDate DESC
       $query = "SELECT * from tb_personal where isStudyAbroad=1";
       $result = DB::select($query);
       return json_decode(json_encode($result),true);
       
   }
   
   public function GetAllForeignStudentsByStartDateDesc(){
       $query = "SELECT tb_personal.* from tb_personal JOIN tb_students On tb_personal.id = tb_students.user_id order by tb_students.startDate DESC";
       $result = DB::select($query);
       return json_decode(json_encode($result),true);
   }
   
   public function DeleteForeignStudentFromPersonal($id){
       try{
           $query = DB::update("UPDATE tb_personal SET isStudyAbroad=0 WHERE id=?", [$id]);
            return "success";
       }catch(Exception $e){
           return $e->getMessage();
       }
       
   }
   
   public function DeleteForeignStudentByUserId($personalId)
    {
        $query = "DELETE FROM tb_students WHERE user_id = $personalId";
        DB::delete($query);
        return "success";
    }
   
    //***********留学生情報****************//
    
    public function GetLoginUserIdFromUserName($userName){
        $query = "select id from tb_personal where concat(first_name,last_name) = '$userName'";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
        
    }
    
    public function GetUserNameFromId($id){
        $query = "select concat(first_name,last_name) as username from tb_personal where id = $id";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
        
    }
    
    public function GetUserNameByMail($mail){
        $query = "select concat(first_name,last_name) as username from tb_personal where mail = '$mail'";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    
}

// if($userinfo['companyId'] != "" && $userinfo['companyId'] != null){
                //     $company_id = $userinfo['companyId'];
                // }