<?php

namespace App\Http\Controllers;
use App\Models\ApplicationModel;
use App\Models\PersonalModel;
use App\Models\CompanyModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;


class ApplicationController extends Controller
{
    public function index(){
        session()->forget('numOfApplicants');
        session()->forget('classType');
        session()->forget('desireDate');
        session()->forget('userInfoList');  
        return view('application');
    }
    
    public function ConfirmPage(){
        return view('applicationConfirm');
    }
    
    public function InsertPage(){
        return view('applicationInsert');
    }
    
    public function InsertPageWithDate($date){
        return view('applicationInsert')->with("selectedDate",$date);
    }
    
    public function EditPage(){
        return view('applicationEdit');
    }
    
    public function EditPage2(){
        return view('applicationConfirmEdit');
    }
    
    public function EditUserInfo($id){
        $application = new ApplicationModel();
        $personal = new PersonalModel();
        $company = new CompanyModel();
        $userinfoById = $personal->GetBimUserByID($id);
        $bimcourseInfo = $application->GetBimCourseInfoByUserID($id);
        $innerArray = array();
        foreach($userinfoById as $user){
            
            foreach($bimcourseInfo as $info){
                $desireDate = $info['desireDate'];
                $classType = $info['classType'];
                $inviterId = $info['inviter'];
                                
                $inviterInfo = $personal->GetUserNameFromId($inviterId);
                if(count($inviterInfo) > 0){
                    $inviter = $inviterInfo[0]['username'];
                }else{
                    $inviter = "";
                }
            } 
            
            $companyID = $user['company_id'];
            $firstName = $user['first_name'];
            $lastName = $user['last_name'];
            //Move to Another Table
            $dept="";
            $branch="";
            $deptId = $user['dept_id'];
            if(!empty($deptId)){
                $deptInfo = $company->GetDeptById($deptId);
                foreach($deptInfo as $d){
                    $dept = $d['name'];
                    }
            }
                    
            $mail = $user['mail'];
            $branchId = $user['branch_id'];
            if(!empty($branchId)){
                $branchInfo = $company->GetBranchById($branchId);
                foreach($branchInfo as $b){
                    $branch = $b['name'];
                }
            }
            $position = $user['position'];
            $code = $user['code'];
            $companyInfo = $company->GetCompanyById($companyID);
            if(count($companyInfo)>0){
                foreach($companyInfo as $c){
                        $companyName = $c['name'];
                        $companyTypeId = $c['company_type_id'];
                        $companyTypeInfo = $company->GetCompanyTypeById($companyTypeId);
                        
                        foreach($companyTypeInfo as $ct){
                            $companyTypeName = $ct['name'];
                              
                                //CreateArray
                                $innerArray['id'] = $id;
                                $innerArray['firstName']=$firstName;
                                $innerArray['lastName']=$lastName;
                                $innerArray['companyType']=$companyTypeName;
                                $innerArray['companyTypeId']=$companyTypeId;
                                $innerArray['company']=$companyName;
                                $innerArray['companyId']=$companyID;
                                $innerArray['dept']=$dept;
                                $innerArray['branch']=$branch;
                                $innerArray['code']=$code;
                                $innerArray['position']=$position;
                                $innerArray['mail'] = $mail;
                                $innerArray['inviter'] = $inviter;
                                $innerArray['classType'] = $classType;
                                $innerArray['desireDate'] = $desireDate;
                            }
                            
                            
                        }
            }else{
                        $innerArray['id'] = $id;
                        $innerArray['firstName']=$firstName;
                        $innerArray['lastName']=$lastName;
                        $innerArray['companyType']="";
                        $innerArray['companyTypeId']="";
                        $innerArray['company']="";
                        $innerArray['companyId']="";
                        $innerArray['dept']=$dept;
                        $innerArray['branch']=$branch;
                        $innerArray['code']=$code;
                        $innerArray['position']=$position;
                        $innerArray['mail'] = $mail;
                        $innerArray['inviter'] = $inviter;
                        $innerArray['classType'] = $classType;
                        $innerArray['desireDate'] = $desireDate;
                    }
        }
           
        // print_r($innerArray);
        
        //$userinfoById = $application->GetUserInfoById($id);
        return view("applicationEditById")->with("userInfo",$innerArray);
        
    }
    
    public function UpdateData(Request $request){
        $message = $request->get('message');
        $application = new ApplicationModel();
        if($message == "updateDecidedDate"){
            $decidedDate = $request->get("decidedDate");
            $update = $application->UpdateDecidedDate($decidedDate);
            return "successs";
        }elseif($message == "updateDisableDate"){
            $disableDate = $request->get("disableDate");
            $update = $application->UpdateDisableDate($disableDate);
            return "successs";
        }elseif($message == "deleteDisableDate"){
            $disableDate = $request->get("disableDate");
            $update = $application->DeleteDisableDate($disableDate);
            return "successs";
        }elseif($message == "deleteDecidedDate"){
            $decidedDate = $request->get("decidedDate");
            $update = $application->DeleteDecidedDate($decidedDate);
            return "successs";
        }elseif($message == "clearDesireDate"){
            $clearDate = $request->get("clearDate");
            $update = $application->ClearDesireDate($clearDate);
            return "successs";
        }elseif($message == "updateDisableMonth"){
            $disableMonth = $request->get("disableMonth");
            $update = $application->UpdateDisableMonth($disableMonth);
            return "successs";
        }elseif($message == "deleteDisableMonth"){
            $disableMonth = $request->get("disableMonth");
            $update = $application->DeleteDisableMonth($disableMonth);
            return "successs";
        }
    }
    
    public function UpdateData1(Request $request){
        $message = $request->get("message");
        $desireDate = $request->get("desireDate");
        $userId =  $request->get("id");
        $classType =  $request->get("classType");
        $application = new ApplicationModel();
        if($message == "updateData"){
            $update = $application->UpdateBimCourseInfo($desireDate,$classType,$userId);
            return "success";
        }
    }
    
    public function DeleteData(Request $request){
        $message = $request->get('message');
        $application = new ApplicationModel();
        if($message == "deleteDesireDate"){
            $desireDate = $request->get("desireDate");
            $id = $request->get("id");
            $update = $application->DeleteDesireDate($desireDate, $id);
            return "successs";
        }
    }
    
    public function SaveInsertData(Request $request){
        $message = $request->get("message");
        $application = new ApplicationModel();
        $company = new CompanyModel();
        $personal = new PersonalModel();
        if($message == "saveInsertData"){
            $numOfApplicants = $request->get("numOfApplicants");
            $desireDate = $request->get("desireDate");
            $classType = $request->get("classType");
            session(['numOfApplicants' => $numOfApplicants]);
            session(['classType' => $classType]);
            session(['desireDate' => $desireDate]);
        }elseif($message == "saveInsertData2"){
            $userInfoList = $request->get("userInfoList");
            for($i=0; $i<count($userInfoList); $i++){
                //print_r($userInfoList[$i]);
                if($userInfoList[$i]['companyTypeId'] == "10"){
                    $userInfoList[$i]['companyTypeId'] = "1";
                }

                $companyName = $userInfoList[$i]['companyName'];
                $companyTypeId = $userInfoList[$i]['companyTypeId'];
                $branchId  = $userInfoList[$i]['branchId'];
                $dept = $userInfoList[$i]['dept'];
                
                $checkCompanyExist = $company->CheckExistingCompany($companyName);
                if(count($checkCompanyExist) == 0){
                   //$companyInsert = $company->InsertNewCompany($companyName,$companyTypeId);
                   //$companyId = $company->GetCompanyIdFromCompany($companyName,$companyTypeId);
                   //$userInfoList[$i]['companyId'] =  $companyId[0]->id;
                   $userInfoList[$i]['companyId'] =0;
                }else{
                   $companyId = $company->GetCompanyIdFromCompany($companyName,$companyTypeId);
                   $userInfoList[$i]['companyId'] = $companyId[0]->id;
                }
                
                if($companyTypeId == 1){
                    if(!empty($dept) && !empty($branchId)){
                        $checkDeptExist = $company->CheckExistingDept($branchId,$dept);
                        if(count($checkDeptExist) == 0){
                            $userInfoList[$i]['deptId'] = "";
                        }else{
                            $deptId = $company->GetDeptIdByNameAndBranchId($dept,$branchId);
                            $userInfoList[$i]['deptId'] = $deptId[0]->id;
                        }
                    }else{
                        $userInfoList[$i]['deptId'] = "";
                        $userInfoList[$i]['branchId'] ="";
                    }
                    
                    
                }else{
                    $userInfoList[$i]['deptId'] = "";
                    $userInfoList[$i]['branchId'] ="";
                }
            }
            session(['userInfoList' => $userInfoList]);
            return session()->get("userInfoList");
            
        }elseif($message == "insertData"){
            $userInfoList = $request->get("userInfoList");
            $desireDate = $request->get("desireDate");
            $inviter = $request->get("inviter");
            $classType = $request->get("classType");
            print_r($desireDate);
            
            try{

                for($i=0; $i<count($userInfoList); $i++){
                    $companyId = $userInfoList[$i]['companyId'];
                    $companyName = $userInfoList[$i]['companyName'];
                    $companyTypeId = $userInfoList[$i]['companyTypeId'];
                    $branchId =  $userInfoList[$i]['branchId'];
                    $dept =  $userInfoList[$i]['dept'];
                    $deptId =  $userInfoList[$i]['deptId'];
                    $contractType = $userInfoList[$i]['contractType'];
                    
                    //会社新規作成
                    if($companyId == 0){
                        if(!empty($companyName)){
                            $companyInsert = $company->InsertNewCompany($companyName,$companyTypeId);
                            $companyNewId = $company->GetCompanyIdFromCompany($companyName,$companyTypeId);
                            $userInfoList[$i]['companyId'] =  $companyNewId[0]->id;
                        }
                        
                    }else{
                        
                    }
                    
                    if($companyTypeId == 1 && empty($deptId)){
                        if(!empty($dept) && !empty($branchId)){
                            $deptInsert = $company->InsertNewDepartment($dept,$branchId);
                            $deptNewId = $company->GetDeptIdByNameAndBranchId($dept,$branchId);
                            $userInfoList[$i]['deptId'] =  $deptNewId[0]->id;
                        }
                    }else{
                        
                    }
                    
                }
                $personalInsert = $personal->InsertPersonalBySpeedCourse($userInfoList);

                for($i=0; $i<count($userInfoList); $i++){
                    if($contractType == "2"){
                        $companyId = $userInfoList[$i]['companyId'];
                        $personalData = $personal->GetPersonalByMail($userInfoList[$i]['email']);
                        if(!empty($personalData)){
                            $haken_result = $personal->InsertPersonalSubHaken($personalData[0]['id'], $companyId); 
                        }
                    }
                }

                $personalId_array = $personal->GetBIMCourseUser($userInfoList);
                print_r($personalId_array);
                
                //*********ユーザー名からID取得*********************//
                $loginUserInfo =  $personal->GetLoginUserIdFromUserName($inviter);
                $loginUserId = $loginUserInfo[0]['id'];
                print_r($loginUserId);
                foreach($personalId_array as $p){
                    $userid = $p;
                    print_r($userid);
                    
                    $bimcourse = $application->InsertBimCourseInfo($userid,$desireDate,$classType,$loginUserId);
                }

                session()->forget('numOfApplicants');
                session()->forget('classType');
                session()->forget('desireDate');
                session()->forget('userInfoList');
                return;
            }catch(Exception $e){
                return $e;
            }
            
        }elseif($message == "updateData"){
            $id = $request->get('id');
            $userInfo = $request->get('userInfo');
            try{
                $update = $application->UpdateUserInfo($userInfo,$id);
                return "success";
            }catch(Exception $e){
                return $e;
            }
            
            
        }
       
    }
    
    public function GetData(Request $request){
        $message = $request->get("message");
        $application = new ApplicationModel();
        $company = new CompanyModel();
        $personal = new PersonalModel();
        if($message == "getDate"){
            $desireDate = $application->GetDate();
            return array("Date" => $desireDate);
        }elseif($message == "getDateFromBimCalendar"){
            $dateFromBimCalendar = $application->GetDateFromBimCalendar();
            return array("GetDateFromBimCalendar" => $dateFromBimCalendar);
        }elseif($message == "getDateFromBimCourseInfo"){
            $dateFromBimCourseInfo = $application->GetDateFromBimCourseInfo();
            return array("GetDateFromBimCourseInfo" => $dateFromBimCourseInfo);
        }elseif($message == "getDateFromBimCourseInfoByLoginUser"){
            $loginUser = $request->get("loginUser");
            
            $loginUserInfo =  $personal->GetLoginUserIdFromUserName($loginUser);
            $loginUserId = $loginUserInfo[0]['id'];
            
            $dateFromBimCourseInfoByLoginUser = $application->GetDateFromBimCourseInfoByLoginUser($loginUserId);
            return array("GetDateFromBimCourseInfoByLoginUser" => $dateFromBimCourseInfoByLoginUser);
        }elseif($message == "getDesireDate"){
            $loginUser = $request->get("loginUser");
            $desireDateByLoginUser = $application->GetDesireDateByLoginUser($loginUser);
            return array("DesireDateByLoginUser" => $desireDateByLoginUser);
        }elseif($message == "getUserInfoBySelectedDate"){
            $selectedDate = $request->get("selectedDate");
            $personal = new PersonalModel();
            $company = new CompanyModel();
            $result =array();
            $BimCourseUserIDs = $application->GetBimUserIDBySelectedDate($selectedDate);
            
            foreach($BimCourseUserIDs as $bu){
                $innerArray = array();
                $id = $bu['user_id'];
                $desireDate = $bu['desireDate'];
                $inviterId = $bu['inviter'];
                
                $inviterInfo = $personal->GetUserNameFromId($inviterId);
                if(count($inviterInfo) > 0){
                    $inviter = $inviterInfo[0]['username'];
                }else{
                    $inviter = "";
                }
                
                
                $classType = $bu['classType'];
                $userInfo = $personal->GetBimUserByID($id);
                
                
                foreach($userInfo as $user){
                    $companyID = $user['company_id'];
                    $firstName = $user['first_name'];
                    $lastName = $user['last_name'];
                    
                    //Move to another table
                    $dept="";
                    $branch="";
                    $deptId = $user['dept_id'];
                    if(!empty($deptId)){
                        $deptInfo = $company->GetDeptById($deptId);
                        foreach($deptInfo as $d){
                            $dept = $d['name'];
                        }
                    }
                    
                    $mail = $user['mail'];
                    $branchId = $user['branch_id'];
                    if(!empty($branchId)){
                        $branchInfo = $company->GetBranchById($branchId);
                        foreach($branchInfo as $b){
                            $branch = $b['name'];
                        }
                    }
                    
                    $position = $user['position'];
                    $code = $user['code'];
                    $companyInfo = $company->GetCompanyById($companyID);
                    if(count($companyInfo)>0){
                        foreach($companyInfo as $c){
                            $companyName = $c['name'];
                            $companyTypeId = $c['company_type_id'];
                            $companyTypeInfo = $company->GetCompanyTypeById($companyTypeId);
                        
                            foreach($companyTypeInfo as $ct){
                                $companyTypeName = $ct['name'];
                                $innerArray['id'] = $id;
                                $innerArray['firstName']=$firstName;
                                $innerArray['lastName']=$lastName;
                                $innerArray['companyType']=$companyTypeName;
                                $innerArray['companyTypeId']=$companyTypeId;
                                $innerArray['company']=$companyName;
                                $innerArray['companyId']=$companyID;
                                $innerArray['dept']=$dept;
                                $innerArray['branch']=$branch;
                                $innerArray['code']=$code;
                                $innerArray['position']=$position;
                                $innerArray['mail'] = $mail;
                                $innerArray['inviter'] = $inviter;
                                $innerArray['classType'] = $classType;
                                $innerArray['desireDate'] = $desireDate;
                            
                            }
                        }
                    }else{
                        $innerArray['id'] = $id;
                        $innerArray['firstName']=$firstName;
                        $innerArray['lastName']=$lastName;
                        $innerArray['companyType']="";
                        $innerArray['companyTypeId']="";
                        $innerArray['company']="";
                        $innerArray['companyId']="";
                        $innerArray['dept']=$dept;
                        $innerArray['branch']=$branch;
                        $innerArray['code']=$code;
                        $innerArray['position']=$position;
                        $innerArray['mail'] = $mail;
                        $innerArray['inviter'] = $inviter;
                        $innerArray['classType'] = $classType;
                        $innerArray['desireDate'] = $desireDate;
                    }
                    
                }
                
                $result[]=$innerArray;
                
            }
            
            
            return array("UserInfoBySelectedDate" => $result);
            //$userInfoBySelectedDateAndLoginUser = $application->GetUserInfoBySelectedDateAndLoginUser($selectedDate,$loginUser);
            
        }elseif($message == "getUserInfoBySelectedDateAndLoginUser"){
            $selectedDate = $request->get("selectedDate");
            $loginUser = $request->get("loginUser");
            $personal = new PersonalModel();
            $company = new CompanyModel();
            $result =array();
            
            $loginUserInfo =  $personal->GetLoginUserIdFromUserName($loginUser);
            $loginUserId = $loginUserInfo[0]['id'];
            
            $BimCourseUserIDs = $application->GetBimUserID($selectedDate,$loginUserId);
            foreach($BimCourseUserIDs as $bu){
                $innerArray = array();
                $id = $bu['user_id'];
                $desireDate = $bu['desireDate'];
                $inviterId = $bu['inviter'];
                
                $inviterInfo = $personal->GetUserNameFromId($inviterId);
                if(count($inviterInfo) > 0){
                    $inviter = $inviterInfo[0]['username'];
                }else{
                    $inviter = "";
                }
                
                $classType = $bu['classType'];
                $userInfo = $personal->GetBimUserByID($id);
                foreach($userInfo as $user){
                    $companyID = $user['company_id'];
                    $firstName = $user['first_name'];
                    $lastName = $user['last_name'];
                    //Move to another table
                    $dept="";
                    $branch="";
                    $deptId = $user['dept_id'];
                    if(!empty($deptId)){
                        $deptInfo = $company->GetDeptById($deptId);
                        foreach($deptInfo as $d){
                            $dept = $d['name'];
                        }
                    }
                    
                    $mail = $user['mail'];
                    $branchId = $user['branch_id'];
                    if(!empty($branchId)){
                        $branchInfo = $company->GetBranchById($branchId);
                        foreach($branchInfo as $b){
                            $branch = $b['name'];
                        }
                    }
                    $position = $user['position'];
                    $code = $user['code'];
                    $companyInfo = $company->GetCompanyById($companyID);
                    if(count($companyInfo)>0){
                        foreach($companyInfo as $c){
                            $companyName = $c['name'];
                            $companyTypeId = $c['company_type_id'];
                            $companyTypeInfo = $company->GetCompanyTypeById($companyTypeId);
                        
                            foreach($companyTypeInfo as $ct){
                                $companyTypeName = $ct['name'];
                                $innerArray['id'] = $id;
                                $innerArray['firstName']=$firstName;
                                $innerArray['lastName']=$lastName;
                                $innerArray['companyType']=$companyTypeName;
                                $innerArray['companyTypeId']=$companyTypeId;
                                $innerArray['company']=$companyName;
                                $innerArray['companyId']=$companyID;
                                $innerArray['dept']=$dept;
                                $innerArray['branch']=$branch;
                                $innerArray['code']=$code;
                                $innerArray['position']=$position;
                                $innerArray['mail'] = $mail;
                                $innerArray['inviter'] = $inviter;
                                $innerArray['classType'] = $classType;
                                $innerArray['desireDate'] = $desireDate;
                            
                            }
                        }
                    }else{
                        $innerArray['id'] = $id;
                        $innerArray['firstName']=$firstName;
                        $innerArray['lastName']=$lastName;
                        $innerArray['companyType']="";
                        $innerArray['companyTypeId']="";
                        $innerArray['company']="";
                        $innerArray['companyId']="";
                        $innerArray['dept']=$dept;
                        $innerArray['branch']=$branch;
                        $innerArray['code']=$code;
                        $innerArray['position']=$position;
                        $innerArray['mail'] = $mail;
                        $innerArray['inviter'] = $inviter;
                        $innerArray['classType'] = $classType;
                        $innerArray['desireDate'] = $desireDate;
                    }
                    
                }
                
                $result[]=$innerArray;
                
            }
            
            return array("UserInfoBySelectedDateAndLoginUser" => $result);
            //$userInfoBySelectedDateAndLoginUser = $application->GetUserInfoBySelectedDateAndLoginUser($selectedDate,$loginUser);
            
        }elseif($message == "searchMise"){
            $startDate = $request->get("startDate");
            $endDate = $request->get("endDate");
            $result = $application->GetMiseBetweenTwoDate($startDate,$endDate);
            return $result;
        }elseif($message == "applicantsInfo"){
            $applicantInfo = $application->GetAllApplicantInfo();
            return array("Applicantinfo" => $applicantInfo);
        }elseif($message == "getCompanyList"){
            $companyTypeId = $request->get("companyTypeId");
            if($companyTypeId == 0){
                $companyList = $application->GetAllCompanyList();
            }else{
                $companyList = $application->GetCompanyListByCompanyTypeId($companyTypeId);
            }
            return array("CompanyList" => $companyList);
        }elseif($message == "getDeptList"){
            $branchId = $request->get("branchId");
            $deptList = $application->GetDeptListByBranchId($branchId);
            return array("DeptList" => $deptList);
        }elseif($message == "getAllDeptList"){
            $deptList = $application->GetAllDeptList();
            return array("DeptList" => $deptList);
        }elseif($message == "excelExportData"){
            $resultSetArray = array();
            $personal = new PersonalModel();
            $startDate = $request->get("startDate");
            $endDate = $request->get("endDate");
            $userInfoList = $application->GetExportData($startDate,$endDate);
            
            foreach($userInfoList as $user){
                $innerArray = array();
                $decidedDate = $user->decidedDate;
                $inviterId = $user->inviter;
                
                $inviterInfo = $personal->GetUserNameFromId($inviterId);
                if(count($inviterInfo) > 0){
                    $inviter = $inviterInfo[0]['username'];
                }else{
                    $inviter = "";
                }
                
                $classType = $user->classType;
                $userId = $user->user_id;
                $userInfo = $personal->GetBimUserByID($userId);
                foreach($userInfo as $u){
                    $userId  = $u['id'];
                    $firstName = $u['first_name'];
                    $lastName = $u['last_name'];
                    //Move to another table
                    $dept="";
                    $branch="";
                    $deptId = $u['dept_id'];
                    if(!empty($deptId)){
                        $deptInfo = $company->GetDeptById($deptId);
                        foreach($deptInfo as $d){
                            $dept = $d['name'];
                        }
                    }
                    
                    $mail = $u['mail'];
                    $branchId = $u['branch_id'];
                    if(!empty($branchId)){
                        $branchInfo = $company->GetBranchById($branchId);
                        foreach($branchInfo as $b){
                            $branch = $b['name'];
                        }
                    }
                    $code = $u['code'];
                    $position = $u['position'];
                    
                    $companyId = $u['company_id'];
                    $companyInfo = $company->GetCompanyById($companyId);
                    foreach($companyInfo as $c){
                        $companyName = $c['name'];
                        $companyTypeId = $c['company_type_id'];
                        $companyTypeInfo = $company->GetCompanyTypeById($companyTypeId);
                        
                        foreach($companyTypeInfo as $ct){
                            $companyTypeName = $ct['name'];
                            $innerArray["date"] = $decidedDate;
                            $innerArray["userId"] = $userId;
                            $innerArray["firstName"] = $firstName;
                            $innerArray["lastName"] = $lastName;
                            $innerArray["companyId"] = $companyId;
                            $innerArray["companyName"] = $companyName;
                            $innerArray["companyTypeName"] = $companyTypeName;
                            $innerArray["companyTypeId"] = $companyTypeId;
                            $innerArray["dept"] = $dept;
                            $innerArray["branch"] = $branch;
                            $innerArray["code"] = $code;
                            $innerArray["position"] = $position;
                            $innerArray["mail"] = $mail;
                            $innerArray["inviter"] = $inviter;
                            $innerArray["classType"] = $classType;
                        }
                    }
                }
               $resultSetArray[] = $innerArray; 
            }
            
            //print_r($resultSetArray);
            return array("ExportData"=>$resultSetArray);
        }
    }
    
    // public function GetDateForAdmin(Request $request){
    //     $message = $request->get('message');
    //     $application = new ApplicationModel();
    //     if($message == 'getDisableDateList'){
    //         $disableDateList = $application->GetDisableDateList();
    //         return $disableDateList;
    //     }
    // }
    
    public function GetCourseInfo(Request $request){
        $message = $request->get('message');
        $application = new ApplicationModel();
        if($message == "get_course_info_by_inviter"){
            $loginId = $request->get('login_id');
            $bim_student_lists = $application->GetStudentListsByInviter($loginId);
            // print_r($bim_student_lists);
            return $bim_student_lists;
        }elseif($message == "get_all_course_info"){
            $all_bimcourse_info = $application->GetAllBimCourseInfo();
            return $all_bimcourse_info;
        }
    }
    
    
    public function InsertPage2(){
        return view('applicationInsertPage2');
    }
    
    public function InsertPage3(){
        return view('applicationInsertPage3');
    }
    
    //Testing Excel Export
    public function ExcelExport(){
        return view('excelDownload');
    }
}


