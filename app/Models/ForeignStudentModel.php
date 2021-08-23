<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ForeignStudentModel extends Model
{
    protected $table = 'tb_employees';
    public $timestamps = false;
    
    protected $fillable = [
		'username',
		's_place',
		's_skill',
		's_field',
		's_haken_department',
		's_obayashi_department',
		's_code',
		's_type',
		's_afterfinish_department',
		'genzai_place',
		'genzai_skill',
		'genzai_field',
		'genzai_haken_department',
		'genzai_obayashi_department',
		'genzai_code',
		'genzai_type',
		's_startDate',
		's_endDate',
		'pichi1',
		'pichi2',
		'pichi3',
		'pichi4',
		
	];
    
    
    public function GetAllStudents()
    {
        $query = "SELECT * FROM tb_employees";
        $data = DB::select($query);     
        return $data;
    }
    
    public function InsertStudent($studentInfo, $personalId)
    {
        try{
            $userId = $personalId;
            $genzai_skill = $studentInfo["genzai_skill"];
            $genzai_field = $studentInfo["genzai_field"];
            $genzai_type  = $studentInfo["genzai_type"];
            
            $s_branch_id  = empty($studentInfo["s_branch_id"]) ? "NULL" : $studentInfo["s_branch_id"];
            $s_dept_id    = empty($studentInfo["s_dept_id"]) ? "NULL" : $studentInfo["s_dept_id"];
            $s_haken_company_id  = empty($studentInfo["s_haken_company_id"]) ? "NULL" : $studentInfo["s_haken_company_id"];
            $s_code      = $studentInfo["s_code"];
            $s_skill     = $studentInfo["s_skill"];
            $s_field      = $studentInfo["s_field"];
            $s_type      = $studentInfo["s_type"];
            $startDate  = empty($studentInfo["startDate"]) ? "NULL" : $studentInfo["startDate"] ;
            $endDate  = empty($studentInfo["endDate"]) ? "NULL" : $studentInfo["endDate"] ;
            $puchi1  = empty($studentInfo["puchi1"]) ? "NULL" : $studentInfo["puchi1"] ;
            $puchi2  = empty($studentInfo["puchi2"]) ? "NULL" : $studentInfo["puchi2"] ;
            $puchi3  = empty($studentInfo["puchi3"]) ? "NULL" : $studentInfo["puchi3"] ;
            $puchi4  = empty($studentInfo["puchi4"]) ? "NULL" : $studentInfo["puchi4"] ;
            $contractType = empty($studentInfo["studentContractType"]) ? 0 : $studentInfo["studentContractType"];
            
            
            
                $query = "INSERT INTO tb_students(id,user_id,genzai_skill,genzai_field,
                          genzai_type,s_branch_id,
                          s_dept_id,s_haken_company_id,s_code,
                          s_skill,s_field,s_type,startDate,endDate,puchi1,
                          puchi2,puchi3,puchi4,contract_type) 
                          SELECT MAX(id) +1,$userId,'$genzai_skill','$genzai_field',
                          '$genzai_type',$s_branch_id,
                          $s_dept_id,$s_haken_company_id,
                          '$s_code','$s_skill','$s_field','$s_type',
                          '$startDate','$endDate','$puchi1','$puchi2','$puchi3','$puchi4',$contractType
                           FROM tb_students
                          ON DUPLICATE KEY  UPDATE user_id=$userId,
                          genzai_skill='$genzai_skill',
                          genzai_field = '$genzai_field',
                          genzai_type = '$genzai_type',
                          s_branch_id = $s_branch_id,
                          s_dept_id = $s_dept_id,
                          s_haken_company_id = $s_haken_company_id,
                          s_code = '$s_code',
                          s_skill = '$s_skill',
                          s_type = '$s_type',
                          startDate='$startDate',
                          endDate='$endDate',
                          puchi1 = '$puchi1',
                          puchi2 = '$puchi2',
                          puchi3 = '$puchi3',
                          puchi4 = '$puchi4',
                          contract_type = $contractType";
                DB::insert($query);
            
            
        }catch(Exception $e){
            return $e->getMessage();
        }
        
   }
    
    public function GetAllStudentsDuration(){
        $query = "SELECT s_startDate, s_endDate FROM tb_employees ORDER BY s_startDate DESC";
        $data = DB::select($query);     
        return $data;
    }
    
    public function GetAllStudentsDurationById($id){
        $query = "SELECT id, s_startDate, s_endDate FROM tb_employees WHERE id=$id ORDER BY s_startDate DESC";
        $data = DB::select($query);     
        return $data;
    }
    
    public function GetAllStudentsByStartDateDesc(){
        $query = "SELECT * FROM tb_employees ORDER BY s_startDate DESC";
        $data = DB::select($query);     
        return $data;
    }
    
    //************留学終了**********//
    public function GetFinishedStudents($id){
        $query = "SELECT * FROM tb_students WHERE user_id=$id AND endDate < CURDATE() ";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    // public function GetFinishedStudentsTimeline(){
    //     $query = "SELECT s_startDate, s_endDate FROM tb_employees WHERE   s_endDate < CURDATE() ";
    //     $data = DB::select($query);     
    //     return $data;
    // }
    
    // public function GetNotFinishedStudents(){
    //     $query = "SELECT * FROM tb_employees WHERE s_startDate <= CURDATE() AND s_endDate >= CURDATE()
    //                 UNION ALL
    //                 SELECT * FROM tb_employees WHERE s_startDate <= CURDATE() AND s_endDate IS NULL";
    //     $data = DB::select($query);     
    //     return $data;
    // }
    
    //************留学中**********//
    public function GetNotFinishedStudents($id){
        $query = "SELECT * FROM tb_students WHERE user_id =$id AND ( startDate <= CURDATE() AND endDate >= CURDATE())
                    UNION ALL
                    SELECT * FROM tb_students WHERE user_id = $id AND (startDate <= CURDATE() AND endDate =  0000-00-00)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    // public function GetNotFinishedStudentsTimeline(){
    //     $query = "SELECT s_startDate, s_endDate FROM tb_employees WHERE s_startDate <= CURDATE() AND s_endDate >= CURDATE()
    //                 UNION ALL
    //                 SELECT s_startDate, s_endDate FROM tb_employees WHERE s_startDate <= CURDATE() AND s_endDate IS NULL";
    //     $data = DB::select($query);     
    //     return $data;
    // }
    
    // public function GetNotYetStudents(){
    //     $query = "SELECT * FROM tb_employees WHERE s_startDate > CURDATE()";
    //     $data = DB::select($query);     
    //     return $data;
    // }
    
    //************留学予定**********//
    public function GetNotYetStudents($id){
        $query = "SELECT * FROM tb_students WHERE user_id = $id AND startDate > CURDATE()";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    // public function GetNotYetStudentsTimeline(){
    //     $query = "SELECT s_startDate, s_endDate FROM tb_employees WHERE s_startDate > CURDATE()";
    //     $data = DB::select($query);     
    //     return $data;
    // }
    
    //***********留学中＆留学終了**********//
    public function GetFinishedAndNotFinishedStudents($id){
        $query = "SELECT * FROM tb_students WHERE user_id=$id AND endDate < CURDATE()
                    UNION ALL
                  SELECT * FROM tb_students WHERE user_id=$id AND (startDate < CURDATE() AND endDate = 0000-00-00)
                   UNION ALL
                   SELECT * FROM tb_students WHERE user_id=$id AND (startDate <= CURDATE() AND endDate >= CURDATE())";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);         
    }
    
    // public function GetFinishedAndNotFinishedStudentsTimeline(){
    //     $query = "SELECT s_startDate, s_endDate FROM tb_employees WHERE s_endDate < CURDATE()
    //                 UNION ALL
    //               SELECT s_startDate, s_endDate FROM tb_employees WHERE s_startDate <= CURDATE() AND s_endDate IS  NULL
    //               UNION ALL
    //               SELECT s_startDate, s_endDate FROM tb_employees WHERE s_startDate <= CURDATE() AND s_endDate >= CURDATE()";
    //     $data = DB::select($query);     
    //     return $data;          
    // }
    
    //***********留学中＆留学予定**********//
    public function GetNotFinishedAndNotYetStudents($id){
        $query = "SELECT * FROM tb_students WHERE user_id=$id AND  startDate > CURDATE()
                    UNION ALL
                  SELECT * FROM tb_students WHERE user_id=$id AND (startDate <= CURDATE() AND endDate= 0000-00-00)
                    UNION ALL
                    SELECT * FROM tb_students WHERE user_id=$id AND (startDate <= CURDATE() AND endDate >= CURDATE())";
        $data = DB::select($query);     
         return json_decode(json_encode($data),true);          
    }
    
    // public function GetNotFinishedAndNotYetStudentsTimeline(){
    //     $query = "SELECT s_startDate, s_endDate FROM tb_employees WHERE  s_startDate > CURDATE()
    //                 UNION ALL
    //               SELECT s_startDate, s_endDate FROM tb_employees WHERE s_startDate <= CURDATE() AND s_endDate IS  NULL
    //               UNION ALL
    //               SELECT s_startDate, s_endDate FROM tb_employees WHERE s_startDate <= CURDATE() AND s_endDate >= CURDATE()";
    //     $data = DB::select($query);     
    //     return $data;          
    // }
    
    //***********留学予定＆留学終了**********//
    public function GetNotYetAndFinishedStudents($id){
        $query = "SELECT * FROM tb_students WHERE user_id=$id AND startDate > CURDATE()
                    UNION ALL
                  SELECT * FROM tb_students  WHERE user_id=$id AND endDate < CURDATE()";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true); 
    }
    
    // public function GetNotYetAndFinishedStudentsTimeline(){
    //     $query = "SELECT s_startDate, s_endDate FROM tb_employees WHERE  s_startDate > CURDATE()
    //                 UNION ALL
    //               SELECT s_startDate, s_endDate FROM tb_employees  WHERE s_endDate < CURDATE()";
    //     $data = DB::select($query);     
    //     return $data;
    // }
    
    
    
    public function GetStudentById($id){
    	$query = "SELECT * FROM tb_employees WHERE id = $id" ;
    	$data = DB::select($query);
    	return $data;
    }
 
    public function UpdateStudentById($id, $data){
    	$username = $data['username'];
    	$s_place = $data['s-place'];
        $s_skill = $data['s-skill'];
        $s_field = $data['s-field'];
        $s_haken_department = $data['s-hakenplace'];
        $s_obayashi_department = $data['s-obayashi'];
        $s_code = $data['s-code'];
        $s_type = $data['s-type'];
            
        $genzai_place = $data['e-place'];
        $genzai_skill = $data['e-skill'];
        $genzai_field = $data['e-field'];
        $genzai_haken_department = $data['e-hakenplace'];
        $genzai_obayashi_department = $data['e-obayashi'];
        $genzai_code = $data['e-code'];
        $genzai_type = $data['e-type'];
        $s_startDate = $data['startDate'];
        $s_endDate = $data['endDate'];
        $pichi1 = $data['puchi1'];
        $pichi2 = $data['puchi2'];
        $pichi3 = $data['puchi3'];
        $pichi4 = $data['puchi4'];
    	
    	$data = DB::update('update tb_employees set username = ?,
    										s_place =?,
    										s_skill =?,
    										s_field =?,
    										s_haken_department  =?,
    										s_obayashi_department  =?,
    										s_code  =?,
    										s_type  =?,
    										genzai_place  =?,
    										genzai_skill  =?,
    										genzai_field  =?,
    										genzai_haken_department  =?,
    										genzai_obayashi_department  =?,
    										genzai_code   =?,
    										genzai_type   =?,
    										s_startDate =?,
											s_endDate =?,
											pichi1 =?,
											pichi2 =?,
											pichi3 =?,
											pichi4 =?
    										where id = ?',
    										[$username,
    										$s_place ,
    										$s_skill,
    										$s_field,
    										$s_haken_department,
    										$s_obayashi_department,
    										$s_code,
    										$s_type,
    										$genzai_place,
    										$genzai_skill,
    										$genzai_field,
    										$genzai_haken_department,
    										$genzai_obayashi_department,
    										$genzai_code,
    										$genzai_type,
    										$s_startDate,
											$s_endDate,
											$pichi1,
											$pichi2,
											$pichi3,
											$pichi4,
    										$id]);
    	
    	return $data;
   
    }
    
    //***********留学履歴削除**********//
    public function DeleteStudentById($id){
        $data = DB::delete('DELETE FROM tb_students WHERE user_id = ?',[$id]);
        return $data;
    }
    
    //Get Mise
    public function GetAllMise(){
        $query = "select s_place from tb_employees where  s_place is not null 
                    UNION ALL
                    select genzai_place from tb_employees where  genzai_place is not null";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    //Get Field
    public function GetAllField(){
        $query = "select distinct s_field,genzai_field from tb_students";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    //Get Skill
    public function GetAllSkill(){
        $query = "select distinct s_skill,genzai_skill from tb_students";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    
    //Get HakenPlace
    public function GetAllHakenPlace(){
        $query = "select s_haken_department from tb_employees where  s_haken_department is not null 
                    UNION ALL
                    select genzai_haken_department from tb_employees where  genzai_haken_department is not null";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    //Get AllObayashi
    public function GetAllObayashi(){
        $query = "select s_obayashi_department from tb_employees where  s_obayashi_department is not null 
                    UNION ALL
                    select genzai_obayashi_department from tb_employees where  genzai_obayashi_department is not null";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetAllType(){
        $query = "select distinct s_type,genzai_type from tb_students";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetCompareStudent($list){
        $count = count($list);
        $query = "SELECT * FROM tb_personal WHERE id IN (";
        for($i =0; $i<$count; $i++){
            if($i == $count-1){
                $query.= $list[$i] . ")";
            }else{
                $query.= $list[$i] . "," ;
            }
        }
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
        
    }
    
    //tb_displayornot_foreignstudent
    public function HideTableColumnDisplay($columnName,$loginUser){
    	$query = "select count(loginUser) as count from tb_displayornot_foreignstudent where loginUser = '$loginUser'" ;
    	$data = DB::select($query);
    	if($data[0]->count == 0){
    		DB::insert("insert into tb_displayornot_foreignstudent (loginUser, $columnName) values(?,?)",[$loginUser,0]);
    	}else{
    		DB::update("update tb_displayornot_foreignstudent set $columnName=? where loginUser=?", [0, $loginUser ]);
    	}
    	
    }
    
    public function ShowTableColumnDisplay($columnName,$loginUser){
    	$query = "select count(loginUser) as count from tb_displayornot_foreignstudent where loginUser = '$loginUser'" ;
    	$data = DB::select($query);
    	if($data[0]->count == 0){
    		DB::insert("insert into tb_displayornot_foreignstudent (loginUser, $columnName) values(?,?)",[$loginUser,1]);
    	}else{
    		DB::update("update tb_displayornot_foreignstudent set $columnName=? where loginUser=?", [1, $loginUser ]);
    	}
    	
    	// $query1 = "select $columnName from tb_displayornot where loginUser = '$loginUser'" ;
    	// $result = DB::select($query1);
    	// print_r($result);
    	// return $result;
    }
    
    public function HideOrShowTable($loginUser){
    	$query = "select * from tb_displayornot_foreignstudent where loginUser = '$loginUser'";
    	$data = DB::select($query);
        return $data;
    }

}
