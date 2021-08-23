<?php

namespace App\Http\Controllers;
use App\Models\LoginModel;
use App\Models\ForgeModel;
use App\Models\PersonalModel;
use App\Models\CompanyModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Artisan;
use App\Mail\ApprovalEmail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
class LoginController extends Controller
{
    function index()
    {
     return view('login');
    }
    
    function NewPasswordIndex()
    {
     return view('change_password');
    }
    

    function LoadAccountCreationgStep1(){
        return view('loginAccountCreatingStep1');
    }
    
    function LoadAccountCreationgStep2(){
        return view('loginAccountCreatingStep2');
    }
    
    function LoadAccountCreationgStep3(){
        return view('loginAccountCreatingStep3');
    }
    
    function LoadApprovalByChiefAdmin($user_id){
        $login = new LoginModel();
        $userInfo = $login->GetPersonalInfoById($user_id);
        if($userInfo != null){
            $userInfo = $userInfo[0];
        }
        return view('loginAccountApprovalByChiefAdmin')->with(["userID" => $user_id,"userInfo" => $userInfo]);
    }
    
    function LoadApprovalByCCCAdmin($user_id,$chiefAdminId){
        $login = new LoginModel();
        $userInfo = $login->GetPersonalInfoById($user_id);
        if($userInfo != null){
            $userInfo = $userInfo[0];
        }
        $chiefAdmin = $login->GetPersonalInfoById($chiefAdminId);
        if($chiefAdmin != null){
            $chiefAdmin = $chiefAdmin[0];
        }
        return view('loginAccountApprovalByCCCAdmin')->with(["userID" => $user_id,"userInfo" => $userInfo,"chiefAdmin" => $chiefAdmin]);
    }
    
    function ChangeLoginUserSetting(Request $request){
        try{
            $checkboxName = $request->get('checkboxName');
            $personalId = $request->get('personalId');
            $status = $request->get('status');
            $login = new LoginModel();
            $result = $login->ChangeLoginUserSetting($checkboxName,$personalId,$status);
            return $result;
        }catch(Exception $e){
            $e->getMessage();
        }
    }
    
    function SaveData(Request $request){
        $message = $request->get('message');
        try{
            if($message == "step1_to_session"){
                $step1Data = json_decode($request->get('step1Data'));
                session(['step1' =>$step1Data]);//set data to session
                return "success";
            }else if($message == "step2_to_session"){
                $chiefAdmin = $request->get('chiefAdmin');
                $chiefAdminId = $request->get('chiefAdminId');
                session(['step2' =>$chiefAdmin]);//set data to session
                session(['chiefAdminId' =>$chiefAdminId]);//set data to session
                return "success";
            }else if($message == "save_login_account_info"){
                $step1Data = session('step1');
                $chiefAdmin = session('step2');
                $chiefAdminId = session('chiefAdminId');
                if($step1Data == null || $chiefAdminId == null 
                  || $step1Data->firstName == "" || $step1Data->lastName == "" || $step1Data->password == "" 
                  || $step1Data->email == ""){
                    return "required";
                }else{
                    $login = new LoginModel();
                    $result = $login->SaveLoginAccountInfo($step1Data,$chiefAdminId);
                    session()->forget('step1');
                    session()->forget('step2');
                    session()->forget('chiefAdminId');
                    return $result;
                }
            }else if($message == "approve_by_chief_admin"){
                $user_id = $request->get("userID");
                $login = new LoginModel();
                $result = $login->UpdateIsC3UserByChiefAdmin($user_id);
                return "success";
            }else if($message == "approve_by_ccc_admin"){
                $user_id = $request->get("userID");
                $authorityId = $request->get("authorityId");
                $login = new LoginModel();
                $result = $login->UpdateIsC3UserByCCCAdmin($user_id,$authorityId);
                return "success";
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function GetPersonalInfo(Request $request){
        $message = $request->get('message');
        try{
            if($message == "get_chief_admin_info"){
                $login = new loginModel();
                $personalInfo = $login->GetChiefAdminInfo();
                return $personalInfo;
            }else if($message == "get_authority_list"){
                $login = new loginModel();
                $result = $login->GetAllAuthority();
                return $result;
            }else if($message == "get_company_data"){
                $login = new loginModel();
                $result = $login->GetCompanyData();
                return $result;
            }else if($message == "get_company_type"){
                $login = new loginModel();
                $result = $login->GetCompanyType();
                return $result;
            }else if($message == "get_department_by_branchId"){
                $login = new loginModel();
                $branchId = $request->get('branchId');
                $result = $login->GetDepartmentByBranchId($branchId);
                return $result;
            }else if($message =="check_duplicate_email"){
                $login = new loginModel();
                $mail = $request->get('mail');
                $result = $login->DuplicateEmailChecking($mail);
                return $result;
            }else if($message =="check_duplicate_company"){
                $login = new loginModel();
                $name = $request->get('name');
                $result = $login->DuplicateCompanyChecking($name);
                return $result;
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function DeleteLoginAccountInfo(Request $request){
        try{
            $message = $request->get('message');
            if($message == "delete_login_info"){
                $personalId = $request->get('personalId');
                $companyId = $request->get('companyId');
                $login = new LoginModel();
                $result = $login->DeleteLoginAccountInfo($personalId,$companyId);
                session()->forget('step1');
                session()->forget('step2');
                session()->forget('chiefAdminId');
                return $result;
            }
            
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    
    function SendingEmail(Request $request){
        $message = $request->get('message');
        if($message == "send_mail_to_chief_admin"){
         $data = array(
             'name' =>"yadanar.min.au@obayashi.co.jp",
             'message'=>" This is greeting From CCC."
         ); 
         
         
         $Correo = new PHPMailer(true);
      //$Correo->IsSMTP();
      $Correo->SMTPAuth = true;
      $Correo->SMTPSecure = "tls";
      $Correo->Host = "smtp.gmail.com";
      $Correo->Port = 587;
      $Correo->Username = "";
      $Correo->Password = "";
      $Correo->setFrom('','De Yo');
      //$Correo->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      //$Correo->FromName = "From";
      $Correo->addAddress("");
      $Correo->Subject = "Prueba con PHPMailer";
      $Correo->Body = "<H3>Bienvenido! Esto Funciona!</H3>";
      $Correo->isHTML (true);
      if (!$Correo->send())
      {
        return "Error: $Correo->ErrorInfo";
      }
      else
      {
        return "Message Sent!";
      }
        $mail = new PHPMailer(TRUE);
          $mail->setFrom('yadanarmin.my@gmail.com', 'Darth Vader');
           $mail->addAddress('yadanar.min.au@obayashi.co.jp', 'Emperor');
           $mail->Subject = 'Force';
           $mail->Body = 'There is a great disturbance in the Force.';
           
           /* SMTP parameters. */
           $mail->isSMTP();
           $mail->Host = 'smtp.gmail.com';
           $mail->SMTPAuth = TRUE;
           $mail->SMTPSecure = 'tls';
           $mail->Username = 'yadanarmin.my@gmail.com';
           $mail->Password = '404error';
           $mail->Port = 587;
           
           /* Disable some SSL checks. */
           $mail->SMTPOptions = array(
            //   'ssl' => array(
            //   'verify_peer' => false,
            //   'verify_peer_name' => false,
            //   'allow_self_signed' => true
            //   )
           );
           
           /* Finally send the mail. */
           $mail->send();
           
        //  Mail::to('yadanar.min.au@obayashi.co.jp')->send(new ApprovalEmail());
        //  return "success";
         //return back()->with('success','Thanks for using CCC.');
        }
    }
    
    function ChangePassword(Request $request)
    {
        $newPassword = $request->get('newPassword');
        $loginName = session('userName');
        $personalId = session('login_user_id');

        $login = new loginModel();
        $result = $login->ChangePassword($newPassword,$loginName,$personalId);
        return $result;
    }
    
    function loginUser(){
        $login = new loginModel();
        $personal = new PersonalModel();
        $company = new CompanyModel();
        $users = $login->GetAllUser();
        $notC3Users = $personal->GetNotC3UserData();
        
        return view('user')->with(["users"=>$users,"notC3Users"=>$notC3Users]);
    }
    function authoritySettings(){
        $login = new loginModel();
        $users = $login->GetAllUser();   
        return view('userAuthoritySettings')->with(["users"=>$users]);
    }

    function checklogin(Request $request)
    {
        $this->validate($request, [
            'username'   => 'required',
            'password'  => 'required'
        ]);

        $user_data = array(
            'name'  => $request->get('username'),
            'password' => $request->get('password')
        );
        
        $login = new LoginModel();
        // データ取得
        $result = $login->getData($user_data);
        $authortiy_id = $login->getAuthorityId($user_data);
        if($result != null)
        {
            $userName = $result[0]->name; 
            $login_id = $result[0]->id;
            $password = $result[0]->password; 
            $login_email = $result[0]->mail; 
            $authority_id = $result[0]->authority_id;
            $first_time_login = $result[0]->first_time_login;

            session(['userName' =>$userName]);
            session(['login_user_id' =>$login_id]);
            session(['current_password' =>$password]);
            session(['login_email' =>$login_email]);
            session(['authority_id'=>$authority_id]);
            $this->SetDownloadFileToSession();
            if($first_time_login == 1 && trim($userName) !== "useradmin")
                    //return redirect()->route('/change/password', ['password' => $password]);
                return redirect('/change/password');
            else
                return redirect('login/successlogin');
        }
        else
        {
            return back()->with('error', 'Wrong Login Details');
        }

    }
    
    function successlogin()
    {
        if(session('userName') && session('login_user_id')){ //check session data           
            $forge = new ForgeModel();
            $login = new LoginModel();
            $projects = $forge->GetProjects();
            $authority_data = $login->GetAuthorityById(session('authority_id'));
            $ccc_all_authority_data = $login->GetAllAuthority();
            
            $loginUserId = session('login_user_id'); 
            $chiefAdminNoti = $login->GetChiefAdminNoti($loginUserId);//get notification for管理責任者
            $cccMasterNoti = $login->GetCCCMasterNoti($loginUserId);//get notification for CCCMaster
            
            if(empty($authority_data)){
                $authority_data[0] = "";
            }
            // return view('home')->with(["projects"=>$projects, "authority_data"=>$authority_data[0]]);
            return view('homedesign')->with(["projects"=>$projects, "authority_data"=>$authority_data[0],
                        "ccc_all_authority_data"=>$ccc_all_authority_data,"chief_admin_noti"=>$chiefAdminNoti,"ccc_master_noti"=>$cccMasterNoti]);
        }else{
            return view('login');
        }
    }
    
    function SetDownloadFileToSession(){
        $directory = "/var/www/html/iPD/public/UploadedFiles/";
        $files = array_diff(scandir($directory), array('.', '..'));
        session(['DownloadFiles' =>$files]);
    }

    function logout()
    {
       //clear all cache
        /*$clearcache = Artisan::call('cache:clear');
        $clearview = Artisan::call('view:clear');
        $clearview = Artisan::call('route:clear');
        $clearconfig = Artisan::call('config:clear');*/

         session()->flush();//delete all session data
         return redirect('login');
    }

    function SaveLoginUserInfo(Request $request){  

        $postData = $request->form;
        
        $user_data = array();
        foreach($postData as $data){
            $key = $data['name'];//input textbox name
            $value = $data['value'];//input value
            $user_data[$key] = $value;
        }

        $login = new LoginModel();
        $personal = new PersonalModel();
        try{
            if($user_data["hidPersonalId"] != 0){

                $saveResult = $login->SaveLoginUser($user_data);

                if($user_data["hidIsC3User"] == 0){
                    $updateResult = $personal->UpdateC3Available($user_data["hidPersonalId"]);
                }
                
                if($user_data["hidOldAuthorityID"] != $user_data["authoritySelect"]){
                    $recentlyInfo = $login->DeleteRecentlyUsedCCC($user_data["hidPersonalId"]);
                }
            }
            
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
        
        return $saveResult;
    }
    
    function CreateAuthorityInfo(Request $request)
    {  
        $name = $request->get('name');
        $authority = $request->get('param_authority');
        $login = new LoginModel();
        $tempResult = $login->CreateAuthority($name,$authority);
        return $tempResult;
    }

    function UpdateAuthorityInfo(Request $request)
    {  
        $authorityData = $request->get('authorityData');
        $name = $authorityData["name"];
        $authority_string = $authorityData["authority_string"];
        $box_access_authority = 0;
        if($authorityData["box_access_authority"] == false){
            $box_access_authority = 0;
        }else{
            $box_access_authority = 1;
        }
        
        $login = new LoginModel();
        $tempResult = $login->UpdateAuthority($name,$authority_string,$box_access_authority);
        return $tempResult;
    }
    
    function UpdateAllAuthorityInfo(Request $request)
    {  
        $allAuthorityData = $request->get('allAuthorityData');
        $login = new LoginModel();
        $tempResult = $login->UpdateAllAuthority($allAuthorityData);
        return $tempResult;
    }
    
    public function GetData(Request $request)
    {
        $userId = $request->get('userID');
        $personal = new PersonalModel();
        $personalData = $personal->GetPersonalById($userId);
        return $personalData;
    }
    
    public function GetAllUserData()
    {
        $login = new loginModel();
        $users = $login->GetAllUser();
        return $users;
    }
    
    public function GetAuthorityData(Request $request)
    {
        $message = $request->get('message');
        
        if($message == 'getAllAuthority'){
            $login = new LoginModel();
            $authorityInfo = $login->GetAllAuthority();
            return $authorityInfo;
        }else if($message == 'getAuthorityById'){
            $authority_id = $request->get('authority_id');
            $login = new LoginModel();
            $authorityInfo = $login->GetAuthorityById($authority_id);
            return $authorityInfo;
        }else if($message == 'getAuthorityByName'){
            $authority_name = $request->get('authority_name');
            $login = new LoginModel();
            $authorityInfo = $login->GetAuthorityByName($authority_name);
            return $authorityInfo;
        }else{
            return [];
        }
    }
    
    public function deleteData(Request $request)
    {
        $personalId = $request->get('userID');
        $login = new LoginModel();
        $personal = new PersonalModel();
        
        try{
            $userInfo = $login->DeleteUserByPersonalId($personalId);
            $c3UnavailableResult = $personal->UpdateC3Unavailable($personalId);
            $recentlyInfo = $login->DeleteRecentlyUsedCCC($personalId);
            
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function DeleteAuthorityData(Request $request)
    {
        $authorityId = $request->get('authorityID');
        $login = new LoginModel();
        $retInfo = $login->DeleteAuthorityById($authorityId);
        return $retInfo;
    }
    

    public function GetContents(Request $request)
    {
        $login = new LoginModel();
        $contents = $login->GetContents();
        return $contents;
    }
    
    public function SetContents(Request $request)
    {
        $content_name = $request->get('content_name');
        $category = $request->get('category');
        $img_src = $request->get('img_src');
        $login = new LoginModel();
        $contents = $login->SetContents($content_name,$category,$img_src);
        
        $data = $login->GetAllAuthority();
        $latest_contents = $login->GetContentsLatestId();
        $latest_id = $latest_contents[0]["id"];
        
        for($i = 0; $i < count($data); $i++){
            $curAuthority = $data[$i]["authority"];
            
            $id = $data[$i]["id"];  //未使用($nameで一致するものを更新しているから)
            $name = $data[$i]["name"];
            $authority = $curAuthority . "," . $latest_id . ":0";
            $box_access = $data[$i]["box_access"];
            
            $result = $login->UpdateAuthority($name,$authority,$box_access);
        }

        return $contents;
    }
    
    public function DeleteContent(Request $request)
    {
        $contentsId = $request->get('contentsID');
        $login = new LoginModel();
        $result = $login->DeleteContentById($contentsId);
        return $result;
    }
    
    public function GetAccessHistory(Request $request)
    {
        $personal_id = $request->get('personal_id');
        // return $login_user_id;
        $login = new LoginModel();
        $access_history = $login->GetCCCAccessHistory($personal_id);
        return $access_history;
    }
    
    function SetAccessHistory(Request $request)
    {  
        $personal_id = $request->get('personal_id');
        $img_src = $request->get('img_src');
        $url = $request->get('data_url');
        $content_name = $request->get('content_name');
        
        //tb_recently_used_ccc(user_id)からlogin_user_idと一致するカラムを取得
        $login = new LoginModel();
        $access_history = $login->GetCCCAccessHistory($personal_id);
        // return count($access_history);
        $img_src_list = "";
        $url_list = "";
        if(count($access_history) > 0)
        {
            $img_src_list = $access_history[0]['img_src'];
            $url_list = $access_history[0]['url'];
            $content_name_list = $access_history[0]['content_name'];
            
            $aryImgSrc = explode(",", $img_src_list);
            $aryUrl = explode(",", $url_list);
            $aryContent = explode(",", $content_name_list);
            
            if(count($aryUrl) == 1){
                if($url_list != $url){
                    $url_list = $url . ',' . $url_list;
                    $img_src_list = $img_src . ',' . $img_src_list;
                    $content_name_list = $content_name . ',' . $content_name_list;
                }
            }else if(count($aryUrl) == 2){
                if($aryUrl[0] != $url && $aryUrl[1] != $url){
                    $url_list = $url . ',' . $aryUrl[0] . ',' . $aryUrl[1];
                    $img_src_list = $img_src . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1];
                    $content_name_list = $content_name . ',' . $aryContent[0] . ',' . $aryContent[1];
                }else{
                    if($aryUrl[0] != $url){
                        $url_list = $aryUrl[1] . ',' . $aryUrl[0];
                        $img_src_list = $aryImgSrc[1] . ',' . $aryImgSrc[0];
                        $content_name_list = $aryContent[1] . ',' . $aryContent[0];
                    }
                }
            }else if(count($aryUrl) == 3){
                
                if($aryUrl[0] != $url && $aryUrl[1] != $url && $aryUrl[2] != $url){
                    $url_list = $url . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2];
                    $img_src_list = $img_src . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2];
                    $content_name_list = $content_name . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2];
                }else{
                    if($aryUrl[0] != $url){
                        if($aryUrl[1] == $url){
                            $url_list = $aryUrl[1] . ',' . $aryUrl[0] . ',' . $aryUrl[2];
                            $img_src_list = $aryImgSrc[1] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[2];
                            $content_name_list = $aryContent[1] . ',' . $aryContent[0] . ',' . $aryContent[2];
                        }else{
                            $url_list = $aryUrl[2] . ',' . $aryUrl[0] . ',' . $aryUrl[1];
                            $img_src_list = $aryImgSrc[2] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1];
                            $content_name_list = $aryContent[2] . ',' . $aryContent[0] . ',' . $aryContent[1];
                        }
                    }
                }
                
            }else if(count($aryUrl) == 4){
                
                if($aryUrl[0] != $url && $aryUrl[1] != $url && $aryUrl[2] != $url && $aryUrl[3] != $url){
                    $url_list = $url . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2] . ',' . $aryUrl[3];
                    $img_src_list = $img_src . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[3];
                    $content_name_list = $content_name . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2] . ',' . $aryContent[3];
                }else{
                    if($aryUrl[0] != $url){
                        if($aryUrl[1] == $url){
                            $url_list = $aryUrl[1] . ',' . $aryUrl[0] . ',' . $aryUrl[2] . ',' . $aryUrl[3];
                            $img_src_list = $aryImgSrc[1] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[3];
                            $content_name_list = $aryContent[1] . ',' . $aryContent[0] . ',' . $aryContent[2] . ',' . $aryContent[3];
                        }else if($aryUrl[2] == $url){
                            $url_list = $aryUrl[2] . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[3];
                            $img_src_list = $aryImgSrc[2] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[3];
                            $content_name_list = $aryContent[2] . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[3];
                        }else{
                            $url_list = $aryUrl[3] . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2];
                            $img_src_list = $aryImgSrc[3] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2];
                            $content_name_list = $aryContent[3] . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2];
                        }
                    }
                }
                
            }else if(count($aryUrl) == 5){
                
                if($aryUrl[0] != $url && $aryUrl[1] != $url && $aryUrl[2] != $url && $aryUrl[3] != $url && $aryUrl[4] != $url){
                    $url_list = $url . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2] . ',' . $aryUrl[3] . ',' . $aryUrl[4];
                    $img_src_list = $img_src . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[3] . ',' . $aryImgSrc[4];
                    $content_name_list = $content_name . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2] . ',' . $aryContent[3] . ',' . $aryContent[4];
                }else{
                    if($aryUrl[0] != $url){
                        if($aryUrl[1] == $url){
                            $url_list = $aryUrl[1] . ',' . $aryUrl[0] . ',' . $aryUrl[2] . ',' . $aryUrl[3] . ',' . $aryUrl[4];
                            $img_src_list = $aryImgSrc[1] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[3] . ',' . $aryImgSrc[4];
                            $content_name_list = $aryContent[1] . ',' . $aryContent[0] . ',' . $aryContent[2] . ',' . $aryContent[3] . ',' . $aryContent[4];
                        }else if($aryUrl[2] == $url){
                            $url_list = $aryUrl[2] . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[3] . ',' . $aryUrl[4];
                            $img_src_list = $aryImgSrc[2] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[3] . ',' . $aryImgSrc[4];
                            $content_name_list = $aryContent[2] . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[3] . ',' . $aryContent[4];
                        }else if($aryUrl[3] == $url){
                            $url_list = $aryUrl[3] . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2] . ',' . $aryUrl[4];
                            $img_src_list = $aryImgSrc[3] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[4];
                            $content_name_list = $aryContent[3] . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2] . ',' . $aryContent[4];
                        }else{
                            $url_list = $aryUrl[4] . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2] . ',' . $aryUrl[3];
                            $img_src_list = $aryImgSrc[4] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[3];
                            $content_name_list = $aryContent[4] . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2] . ',' . $aryContent[3];
                        }
                    }
                }
                
            }else if(count($aryUrl) >= 6){
                
                if($aryUrl[0] != $url && $aryUrl[1] != $url && $aryUrl[2] != $url && $aryUrl[3] != $url && $aryUrl[4] != $url && $aryUrl[5] != $url){
                    $url_list = $url . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2] . ',' . $aryUrl[3] . ',' . $aryUrl[4];
                    $img_src_list = $img_src . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[3] . ',' . $aryImgSrc[4];
                    $content_name_list = $content_name . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2] . ',' . $aryContent[3] . ',' . $aryContent[4];
                }else{
                    if($aryUrl[0] != $url){
                        if($aryUrl[1] == $url){
                            $url_list = $aryUrl[1] . ',' . $aryUrl[0] . ',' . $aryUrl[2] . ',' . $aryUrl[3] . ',' . $aryUrl[4] . ',' . $aryUrl[5];
                            $img_src_list = $aryImgSrc[1] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[3] . ',' . $aryImgSrc[4] . ',' . $aryImgSrc[5];
                            $content_name_list = $aryContent[1] . ',' . $aryContent[0] . ',' . $aryContent[2] . ',' . $aryContent[3] . ',' . $aryContent[4] . ',' . $aryContent[5];
                        }else if($aryUrl[2] == $url){
                            $url_list = $aryUrl[2] . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[3] . ',' . $aryUrl[4] . ',' . $aryUrl[5];
                            $img_src_list = $aryImgSrc[2] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[3] . ',' . $aryImgSrc[4] . ',' . $aryImgSrc[5];
                            $content_name_list = $aryContent[2] . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[3] . ',' . $aryContent[4] . ',' . $aryContent[5];
                        }else if($aryUrl[3] == $url){
                            $url_list = $aryUrl[3] . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2] . ',' . $aryUrl[4] . ',' . $aryUrl[5];
                            $img_src_list = $aryImgSrc[3] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[4] . ',' . $aryImgSrc[5];
                            $content_name_list = $aryContent[3] . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2] . ',' . $aryContent[4] . ',' . $aryContent[5];
                        }else if($aryUrl[4] == $url){
                            $url_list = $aryUrl[4] . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2] . ',' . $aryUrl[3] . ',' . $aryUrl[5];
                            $img_src_list = $aryImgSrc[4] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[3] . ',' . $aryImgSrc[5];
                            $content_name_list = $aryContent[4] . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2] . ',' . $aryContent[3] . ',' . $aryContent[5];
                        }else{
                            $url_list = $aryUrl[5] . ',' . $aryUrl[0] . ',' . $aryUrl[1] . ',' . $aryUrl[2] . ',' . $aryUrl[3] . ',' . $aryUrl[4];
                            $img_src_list = $aryImgSrc[5] . ',' . $aryImgSrc[0] . ',' . $aryImgSrc[1] . ',' . $aryImgSrc[2] . ',' . $aryImgSrc[3] . ',' . $aryImgSrc[4];
                            $content_name_list = $aryContent[5] . ',' . $aryContent[0] . ',' . $aryContent[1] . ',' . $aryContent[2] . ',' . $aryContent[3] . ',' . $aryContent[4];
                        }
                    }
                }
                
            }else{
                //NOP
            }
            
        }else{
            $img_src_list = $img_src;
            $url_list = $url;
            $content_name_list = $content_name;
        }
        
        $recently_used_data = array();
        $recently_used_data["user_id"] = $personal_id;
        $recently_used_data["img_src"] = $img_src_list;
        $recently_used_data["url"] = $url_list;
        $recently_used_data["content_name"] = $content_name_list;
        $saveResult = $login->SaveAccessHistory($recently_used_data);
        
        return $saveResult;
    }
    
    
}