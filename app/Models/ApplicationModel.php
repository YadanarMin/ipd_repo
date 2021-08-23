<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
class ApplicationModel extends Model
{
    public function InsertApplicants($userInfoList, $desireDate, $inviter,$classType){
        try{
            foreach($userInfoList as $userinfo){
                
                DB::insert("insert into tb_applicants(name,place,obayashi,hakenplace,code,job,mail,desireDate,inviter,classType)
                          values ( ?,?,?,?,?,?,?,?,?,?)" ,
                          [$userinfo['name'],$userinfo['place'],$userinfo['obayashi'],$userinfo['hakenplace'],$userinfo['code'],$userinfo['job'],
                          $userinfo['email'],$desireDate,$inviter,$classType] );
            }
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function InsertBimCourseInfo($id, $desireDate, $classType, $inviter){
        
        $query1 = "select * from tb_bimcourse_info where user_id=$id";
        $existingUser = DB::select($query1);
        print_r($existingUser);
        if(count($existingUser)>0){
            $query = DB::update("update tb_bimcourse_info set desireDate=?,classType=?,inviter=? where user_id=?",[$desireDate,$classType,$inviter,$id]);
            return "success";
        }else{
            $query = "insert into tb_bimcourse_info(desireDate,classType,inviter,user_id) values ('$desireDate','$classType',$inviter,$id)";
            $result = DB::insert($query);
            return "success";
        }
        
        
    }
    
    public function GetBimCourseInfoByUserID($id){
        $query = "select * from tb_bimcourse_info where user_id=$id";
        $result = DB::select($query);
        return json_decode(json_encode($result),true);
    }
    
    public function GetDate(){
        $query = "select desireDate, decidedDate, disableDate, disableMonth from tb_applicants";
        $data = DB::select($query);
        return $data;
    }
    
    public function GetDesireDateByLoginUser($loginUser){
        $query = "select desireDate from tb_applicants where inviter = '$loginUser'";
        $data = DB::select($query);
        return $data;
    }
    
    public function GetAllApplicantInfo(){
        $query = "select * from tb_applicants";
        $data = DB::select($query);
        return $data;
    }
    
    public function GetAllCompanyList(){
        $query = "select * from tb_company";
        $data = DB::select($query);
        return $data;
    }

    public function GetCompanyListByCompanyTypeId($companyTypeId){
        $query = "select * from tb_company where company_type_id = $companyTypeId";
        $data = DB::select($query);
        return $data;
    }
    
    public function GetDeptListByBranchId($branchId){
        $query = "select * from tb_dept where branch_id = $branchId";
        $data = DB::select($query);
        return $data;
    }

    public function GetAllDeptList(){
        $query = "select * from tb_dept";
        $data = DB::select($query);
        return $data;
    }
    
    public function GetUserInfoBySelectedDate($selectedDate){
        $query = "select * from tb_applicants where desireDate like '%$selectedDate%'";
        $data = DB::select($query);
        //print_r($data);
        return $data;
    }
    
    public function GetUserInfoBySelectedDateAndLoginUser($selectedDate, $loginUser){
        $query = "select * from tb_applicants where inviter = '$loginUser' AND (desireDate like '%$selectedDate%' OR undecidedDate like '%$selectedDate%') ";
        $data = DB::select($query);
        //print_r($data);
        return $data;
    }
    
    public function GetBimUserID($selectedDate, $loginUserId){
        //Show userinfo only created by login user
        // $query = "select * from tb_bimcourse_info where inviter =$loginUserId AND desireDate LIKE '%$selectedDate%'";
        
        //Show every userinfo
        $query = "select * from tb_bimcourse_info where desireDate LIKE '%$selectedDate%'";
        $resultSet = DB::select($query);
        return json_decode(json_encode($resultSet),true);
    }
    
    public function GetBimUserIDBySelectedDate($selectedDate){
        $query = "select * from tb_bimcourse_info where desireDate LIKE '%$selectedDate%'";
        $resultSet = DB::select($query);
        return json_decode(json_encode($resultSet),true);
    }
    
    
    public function GetUserInfoById($id){
        $query = "select * from tb_applicants where id=$id";
        $data = DB::select($query);
        return $data;
    }
    
    public function UpdateBimCourseInfo($desireDate,$classType,$userId){
        $data = DB::update("update tb_bimcourse_info set desireDate=?, classType=? where user_id=?",[$desireDate,$classType,$userId]);
        return $data;
    }
    
    public function UpdateUserInfo($userInfo,$id){
        $data = DB::update('update tb_applicants set name = ?,
    										place =?,
    										obayashi =?,
    										hakenplace =?,
    										code  =?,
    										job  =?,
    										mail  =?,
    										desireDate  =?,
    										inviter  =?,
    										classType  =?
    										where id = ?',
    										[$userInfo['username'],
    										$userInfo['place'] ,
    										$userInfo['obayashi'],
    										$userInfo['hakenplace'],
    										$userInfo['code'],
    										$userInfo['job'],
    										$userInfo['mail'],
    										$userInfo['hiddenSelectedDate'],
    										$userInfo['inviter'],
    										$userInfo['classType'],
    										$id]);
    	
    	return $data;
    }
    
    //決定日にする(Old)
    // public function UpdateDecidedDate($decidedDate){
    //     $query = "select id, decidedDate from tb_applicants where desireDate like '%$decidedDate%'";
    //     $existDecidedDate = DB::select($query);
    //     foreach($existDecidedDate as $d){
    //         $id = $d->id;
    //         $date = $d->decidedDate;
    //         if($date != ""){
    //             $date.=",".$decidedDate;
    //             $query = "update tb_applicants set decidedDate = '$date'  where desireDate LIKE '%$decidedDate%' AND id = $id";
    //             try{
    //                 $data = DB::update($query);
                    
    //             }catch(Exception $e){
    //                 return $e->getMessage();
    //             }
    //         }else{
    //             $query = "update tb_applicants set decidedDate = '$decidedDate'  where desireDate LIKE '%$decidedDate%' AND id = $id";
    //             try{
    //                 $data = DB::update($query);
                    
    //             }catch(Exception $e){
    //                 return $e->getMessage();
    //             }
    //         }
            
            
    //     }
        
    // }
    
    //決定日にする(New)
    public function UpdateDecidedDate($decidedDate){
        $query = "select * from tb_bimcourse_info where desireDate like '%$decidedDate%'";
        $existDecidedDate = DB::select($query);
        foreach($existDecidedDate as $d){
            $id = $d->id;
            $date = $d->decidedDate;
            if($date != ""){
                $date.=",".$decidedDate;
                $query = "update tb_bimcourse_info set decidedDate = '$date'  where  id = $id";
                try{
                    $data = DB::update($query);
                    
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }else{
                $query = "update tb_bimcourse_info set decidedDate = '$decidedDate'  where  id = $id";
                try{
                    $data = DB::update($query);
                    
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }
            
            
        }
        
    }
    
    //NG日にする(Old)
    // public function UpdateDisableDate($disableDate){
    //     $query = "select id,disableDate from tb_applicants";
    //     $existDisableDate = DB::select($query);
    //     // print_r(count($existDisableDate));
    //     if(count($existDisableDate) == 0){
    //         $query = "insert into tb_applicants(disableDate) values ('$disableDate') ";
    //         $data = DB::insert($query);
    //     }else{
    //         foreach($existDisableDate as $d){
    //             $i = $d->id;
    //             $date = $d->disableDate;
    //             $date.=",".$disableDate;
    //             $query = "update tb_applicants set disableDate = '$date' where id= $i ";
    //             try{
    //                 $data = DB::update($query);
    //             }catch(Exception $e){
    //                 return $e->getMessage();
    //             }
                
    //         }
    //     }
        
    // }
    
    //NG日にする(New)
    public function UpdateDisableDate($disableDate){
        try{
            $query = "insert into tb_bimcourse_calendar(disableDate) values ('$disableDate')";
            $data = DB::insert($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    
    //Get NG日 list
    // public function GetDisableDateList(){
    //     try{
    //         $query = "select disableDate from tb_bimcourse_calendar";
    //         $data = DB::select($query); 
    //         return $data;
    //     }catch(Exception $e){
    //         return $e->getMessage();
    //     }
        
        
    // }
    
    //希望日クリア（Old）
    // public function ClearDesireDate($clearDate){
    //     $query = "select * from tb_applicants where desireDate like '%$clearDate%'";
    //     $data = DB::select($query);
    //     foreach($data as $d){
    //         $id = $d->id;
    //         $desireDate = $d->desireDate;
    //         $undecidedDate = $d->undecidedDate;
    //         //test
    //         $desireDateArray = explode(",",$desireDate);
    //         //  print_r($desireDateArray);
    //         for($i=0; $i< count($desireDateArray) ; $i++){
    //             if($clearDate == trim($desireDateArray[$i]) ){
    //                 unset($desireDateArray[$i]);
    //             }
    //         }
    //     //   print_r($desireDateArray);
    //         $date = implode(',', $desireDateArray);
            
    //         //test
    //         //$date = str_replace($clearDate,'',$desireDate);
    //         $undecidedDate.= ",".$clearDate;
    //         $query1 = "update tb_applicants set desireDate = '$date' where id = $id ";
    //         $query2 = "update tb_applicants set undecidedDate = '$undecidedDate' where id = $id ";
    //         try{
    //             $result = DB::update($query1);
    //             $result1 = DB::update($query2);
    //         }catch(Exception $e){
    //             return $e->getMessage();
    //         }    
    //     }
    // }
    
    //希望日クリア（New）
    public function ClearDesireDate($desireDate){
        $query = "select * from tb_bimcourse_info where desireDate LIKE '%$desireDate%'";
        $data = DB::select($query);
        foreach($data as $d){
            $id = $d->id;
            $userId = $d->user_id;
            $desireDateStr = $d->desireDate;
            $desireDateArray = explode(",",$desireDateStr);
            for($i=0; $i< count($desireDateArray) ; $i++){
                if($desireDate == trim($desireDateArray[$i]) ){
                    unset($desireDateArray[$i]);
                }
            } 
            $updateDesireDateStr = implode(',', $desireDateArray);
            print_r($updateDesireDateStr);    
            $query1 = "update tb_bimcourse_info set desireDate = '$updateDesireDateStr' where id= $id AND user_id =$userId ";
            try{
                $data1 = DB::update($query1);
            }catch(Exception $e){
                return $e->getMessage();
            }
                
        }
    }  
    
    //決定日クリア（New）
    public function DeleteDecidedDate($decidedDate){
        $query = "select * from tb_bimcourse_info where decidedDate LIKE '%$decidedDate%'";
        $data = DB::select($query);
        foreach($data as $d){
            $id = $d->id;
            $userId = $d->user_id;
            $decidedDateStr = $d->decidedDate;
            $decidedDateArray = explode(",",$decidedDateStr);
            for($i=0; $i< count($decidedDateArray) ; $i++){
                if($decidedDate == trim($decidedDateArray[$i]) ){
                    unset($decidedDateArray[$i]);
                }
            } 
            $updateDecidedDateStr = implode(',', $decidedDateArray);
            print_r($updateDecidedDateStr);    
            $query1 = "update tb_bimcourse_info set decidedDate = '$updateDecidedDateStr' where id= $id AND user_id =$userId ";
            try{
                $data1 = DB::update($query1);
            }catch(Exception $e){
                return $e->getMessage();
            }
                
        }
    }  
    
    //店検索
    public function GetMiseBetweenTwoDate($startDate, $endDate){
        // Declare an empty array
        $array = array();
        $resultSet = array();
        $branchIdList = array();
        $branchList = array();
        
        // Use strtotime function
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        
        // Use for loop to store dates into array
        // 86400 sec = 24 hrs = 60*60*24 = 1 day
        for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400)) {
            $Store = date('Y-m-d', $currentDate);
            $array[] = $Store;
        }
        
        foreach($array as $decidedDate){
            $query = "select user_id from tb_bimcourse_info where decidedDate LIKE '%$decidedDate%' OR desireDate LIKE '%$decidedDate%'";
            $result = DB::select($query);
            if(count($result) != 0){
                foreach($result as $r){
                     $id = $r->user_id;
                     if(!in_array($id, $resultSet)){
                        $resultSet[]=$id;
                        }
                }
               
            }
        }
        
        foreach($resultSet as $userid){
            $query1 = "select branch_id from tb_personal where id=$userid";
            $data = DB::select($query1);
            foreach($data as $d){
                     $branchId = $d->branch_id;
                     $branchIdList[]=$branchId;
                     
                }
        }
        
        foreach($branchIdList as $branchId){
            $query1 = "select name from tb_branch_office where id=$branchId";
            $data = DB::select($query1);
            foreach($data as $d){
                     $branch = $d->name;
                     $branchList[]=$branch;
                }
        }
        //print_r($branchList);
        return $branchList;
        
        
    }
    
    //Excel出力
    public function GetExportData($startDate, $endDate){
        $array = array();
        $resultSet = array();
        $userInfoList = array();
        
        // Use strtotime function
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        
        // Use for loop to store dates into array
        // 86400 sec = 24 hrs = 60*60*24 = 1 day
        for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400)) {
            $Store = date('Y-m-d', $currentDate);
            $array[] = $Store;
        }
        
        foreach($array as $decidedDate){
            $query = "select * from tb_bimcourse_info where decidedDate LIKE '%$decidedDate%'";
            $result = DB::select($query);
            if(count($result) != 0){
                foreach($result as $r){
                    if(!in_array($r,$userInfoList)){
                        $userInfoList[]=$r;
                    }
                    
                }
            }
            
        }
        //print_r($userInfoList);
       
        
        // foreach($array as $decidedDate){
        //     $query = "select * from tb_bimcourse_info where decidedDate LIKE '%$decidedDate%'";
        //     $result = DB::select($query);
        //     print_r($result);
        //     if(count($result) != 0){
        //         foreach($result as $r){
        //              $id = $r->user_id;
        //              $decideddate = $r->decidedDate;
        //              if(!in_array($id, $resultSet)){
        //                 $resultSet[$id]=$decideddate;
                    
        //                 }
        //         }
               
        //     }
        // }
        // //print_r($resultSet);
        // foreach(array_keys($resultSet) as $userid){
        //     $query1 = "select * from tb_personal where id=$userid";
        //     $data = DB::select($query1);
        //     $result =[
        //         "decidedDate" => $resultSet[$userid],
        //         "userInfo"    => json_decode(json_encode($data),true)
        //     ];
        //     $userInfoList[] = $result;
        // }
        //print_r($userInfoList);
        return $userInfoList;
        
    }
    
    //今月締め切り（古い）
    // public function UpdateDisableMonth($disableMonth){
    //     $query = "select id, disableMonth from tb_applicants";
    //     $existDisableMonth = DB::select($query);
    //     // print_r(count($existDisableDate));
    //     if(count($existDisableMonth) == 0){
    //         $query = "insert into tb_applicants(disableMonth) values ('$disableMonth') ";
    //         $data = DB::insert($query);
    //     }else{
    //         foreach($existDisableMonth as $d){
    //             $i = $d->id;
    //             $date = $d->disableMonth;
    //             if(empty($date)){
    //                 $date = $disableMonth;
    //             }else{
    //                 $date.=",".$disableMonth;
    //             }
                
    //             $query = "update tb_applicants set disableMonth = '$date' where id= $i ";
    //             try{
    //                 $data = DB::update($query);
    //             }catch(Exception $e){
    //                 return $e->getMessage();
    //             }
                
    //         }
    //     }
        
    // }
    
    //今月締め切り（新しい）
    public function UpdateDisableMonth($disableMonth){
        try{
            $query = "insert into tb_bimcourse_calendar(disableMonth) values ('$disableMonth')";
            $data = DB::insert($query);
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
        
        
    }
    
    //今月締め切り解除（古い）
    // public function DeleteDisableMonth($disableMonth){
    //     $query = "select id, disableMonth from tb_applicants where disableMonth LIKE '%$disableMonth%'";
    //     $existDisableMonth = DB::select($query);
    //     if(count($existDisableMonth) == 0){
    //         return;
    //     }else{
    //         foreach($existDisableMonth as $d){
                
    //             $id = $d->id;
    //             $month = $d->disableMonth;
    //             $monthArray = explode(",",$month);
                
    //             for($i=0; $i< count($monthArray) ; $i++){
    //                 if($disableMonth == trim($monthArray[$i]) ){
    //                     unset($monthArray[$i]);
    //                 }
    //             }
        
    //             $updateMonth = implode(',', $monthArray);
                
    //             $query = "update tb_applicants set disableMonth = '$updateMonth' where id= $id ";
    //             try{
    //                 $data = DB::update($query);
    //             }catch(Exception $e){
    //                 return $e->getMessage();
    //             }
                
    //         }
    //     }
        
    // }
    
    //今月締め切り解除（新しい）
    public function DeleteDisableMonth($disableMonth){
        try{
           $query = "select id, disableMonth from tb_bimcourse_calendar where disableMonth LIKE '%$disableMonth%'" ;
           $resultSet = DB::select($query);
           if(count($resultSet) == 0){
               return;
           }else{
               foreach($resultSet as $d){
                
                    $id = $d->id;
                    $month = $d->disableMonth;
                    $monthArray = explode(",",$month);
                    
                    for($i=0; $i< count($monthArray) ; $i++){
                        if($disableMonth == trim($monthArray[$i]) ){
                            unset($monthArray[$i]);
                        }
                    }
            
                    $updateMonth = implode(',', $monthArray);
                    
                    $query = "update tb_bimcourse_calendar set disableMonth = '$updateMonth' where id= $id ";
                    $query1 = "delete from tb_bimcourse_calendar where (disableMonth =' ' OR disableMonth is NULL ) AND (disableDate = ' ' OR disableDate is NULL) ";
                    try{
                        $data = DB::update($query);
                        DB::delete($query1);
                    }catch(Exception $e){
                        return $e->getMessage();
                    }
                
                }
           }
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    //NG日をクリアする
    public function DeleteDisableDate($disableDate){
        try{
           $query = "select id, disableDate from tb_bimcourse_calendar where disableDate LIKE '%$disableDate%'" ;
           $resultSet = DB::select($query);
           if(count($resultSet) == 0){
               return;
           }else{
               foreach($resultSet as $d){
                
                    $id = $d->id;
                    $date = $d->disableDate;
                    $dateArray = explode(",",$date);
                    
                    for($i=0; $i< count($dateArray) ; $i++){
                        if($disableDate == trim($dateArray[$i]) ){
                            unset($dateArray[$i]);
                        }
                    }
            
                    $updateDay = implode(',', $dateArray);
                    
                    $query = "update tb_bimcourse_calendar set disableDate = '$updateDay' where id= $id ";
                    $query1 = "delete from tb_bimcourse_calendar where (disableMonth =' ' OR disableMonth is NULL ) AND (disableDate = ' ' OR disableDate is NULL) ";
                    try{
                        $data = DB::update($query);
                        DB::delete($query1);
                    }catch(Exception $e){
                        return $e->getMessage();
                    }
                
                }
           }
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    
    public function DeleteDesireDate($desireDate, $id){
        $query = "select desireDate from tb_bimcourse_info where user_id=$id";
        $data = DB::select($query);
        print_r($data);
        
        foreach($data as $d){
            $desireDateStr = $d->desireDate;
            $desireDateArray = explode(",",$desireDateStr);
            for($i=0; $i< count($desireDateArray) ; $i++){
                if($desireDate == trim($desireDateArray[$i]) ){
                    unset($desireDateArray[$i]);
                }
            } 
            $updateDesireDateStr = implode(',', $desireDateArray);
            print_r($updateDesireDateStr);    
            $query1 = "update tb_bimcourse_info set desireDate = '$updateDesireDateStr' where user_id= $id ";
            try{
                $data1 = DB::update($query1);
            }catch(Exception $e){
                return $e->getMessage();
            }
                
        }
    }  
    
    
    
    //テーブル変更後
    public function GetDateFromBimCalendar(){
        try{
            $query = "select disableDate, disableMonth from tb_bimcourse_calendar";
            $resultSet = DB::select($query);
            return $resultSet;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function GetDateFromBimCourseInfo(){
        try{
            $query = "select desireDate, decidedDate from tb_bimcourse_info";
            $resultSet = DB::select($query);
            return $resultSet;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
     public function GetDateFromBimCourseInfoByLoginUser($loginUser){
        try{
            $query = "select desireDate, decidedDate from tb_bimcourse_info where inviter = $loginUser";
            $resultSet = DB::select($query);
            return $resultSet;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    
    public function DeleteBimCourseInfoByUserId($personalId)
    {
        $query = "DELETE FROM tb_bimcourse_info WHERE user_id = $personalId";
        DB::delete($query);
        return "success";
    }
    
    //Home Page Design
    public function GetStudentListsByInviter($loginId){
        try{
            $query = "select concat(p.first_name,p.last_name) as username , bim.* from tb_bimcourse_info as bim 
                      JOIN  tb_personal as p on bim.user_id = p.id where
                      inviter = $loginId and decidedDate is NULL and desireDate is not null and desireDate != ''";
            $data = DB::select($query);
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function GetAllBimCourseInfo(){
        try{
            $query = "select concat(p.first_name,p.last_name) as username , bim.* from tb_bimcourse_info as bim 
                      JOIN  tb_personal as p on bim.inviter = p.id where
                      decidedDate is NULL and desireDate is not null and desireDate != ''";
            $data = DB::select($query);
            return json_decode(json_encode($data),1);
            
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
}